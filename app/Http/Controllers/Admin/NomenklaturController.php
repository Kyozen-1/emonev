<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunPeriode;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\ProgramIndikatorKinerja;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\MasterOpd;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\ProgramRpjmd;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;

class NomenklaturController extends Controller
{
    // accordion-body
    public function index()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $urusans[] = [
                'id' => $get_urusan->id,
                'kode' => $get_urusan->kode,
                'deskripsi' => $get_urusan->deskripsi
            ];
        }

        $opd = MasterOpd::pluck('nama', 'id');

        return view('admin.nomenklatur.index', [
            'tahuns' => $tahuns,
            'urusans' => $urusans,
            'opd' => $opd
        ]);
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

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->orderBy('tahun_perubahan', 'desc')->first();
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
                                $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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

        return response()->json(['html' => $html]);
    }

    public function get_program_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($tahun == 'semua')
        {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
            $urusans = [];
            foreach ($get_urusans as $get_urusan) {
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->orderBy('tahun_perubahan', 'desc')->first();
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
                                    $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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

            return response()->json(['html' => $html]);
        } else {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
            $urusans = [];
            foreach ($get_urusans as $get_urusan) {
                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->orderBy('tahun_perubahan', 'desc')->first();
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
                                    $get_programs = Program::where('urusan_id', $urusan['id'])->where('tahun_perubahan', $tahun)->get();
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

            return response()->json(['html' => $html]);
        }
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

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="40%">Deskripsi</th>
                                    <th width="25%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                                foreach ($urusans as $urusan) {
                                    $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['kode']).'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['deskripsi']).'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr style="background: #c04141;">
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                    '.strtoupper($program['deskripsi']);
                                                                                    $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                    if($cek_program_rjmd)
                                                                                    {
                                                                                        $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                    }
                                                                                    $html .= '<br>
                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                <td width="20%">
                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                        <table class="table table-striped table-condesed">
                                                                                            <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->orderBy('kode', 'asc')->get();
                                                                                                $kegiatans = [];
                                                                                                foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                ->orderBy('id', 'desc')->first();
                                                                                                    if($cek_perubahan_kegiatan)
                                                                                                    {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                            'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                            'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    } else {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                }
                                                                                                foreach ($kegiatans as $kegiatan) {
                                                                                                    $html .= '<tr>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                    '.$kegiatan['deskripsi'].'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                    <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>';
                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                    $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                            $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                            if($opd_kegiatan_indikator_kinerja)
                                                                                                                            {
                                                                                                                                $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                            }
                                                                                                                    $html .= '</li>';
                                                                                                                }
                                                                                                                $html .='</ul></td>
                                                                                                                <td width="20%">
                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                                    <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <thead>
                                                                                                                                <tr>
                                                                                                                                    <th>No</th>
                                                                                                                                    <th>Indikator</th>
                                                                                                                                    <th>Target Kinerja Awal</th>
                                                                                                                                    <th>Target Anggaran Awal</th>
                                                                                                                                    <th>OPD</th>
                                                                                                                                    <th>Target</th>
                                                                                                                                    <th>Satuan</th>
                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                    <th>Realisasi</th>
                                                                                                                                    <th>Realisasi Anggaran</th>
                                                                                                                                    <th>Tahun</th>
                                                                                                                                </tr>
                                                                                                                            </thead>
                                                                                                                            <tbody>';
                                                                                                                            $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                            $no_kegiatan_indikator_kinerja = 1;
                                                                                                                            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                $html .= '<tr>';
                                                                                                                                    $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                    $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                    $a = 1;
                                                                                                                                    $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                        ->get();
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
                                                                                                                                                        $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                            $kegiatan_realisasi = [];
                                                                                                                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                        $html .='</tr>';
                                                                                                                                                    } else {
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                        $html .='</>';
                                                                                                                                                    }
                                                                                                                                                } else {
                                                                                                                                                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                ->where('tahun', $tahun)
                                                                                                                                                                                                ->first();
                                                                                                                                                    if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                    {
                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                        $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                        if($cek_kegiatan_tw_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                            $kegiatan_realisasi = [];
                                                                                                                                                            foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                        $html .='</tr>';
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                                $b++;
                                                                                                                                            }
                                                                                                                                        } else {
                                                                                                                                            $html .= '<tr>';
                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                $html .= '<td></td>';
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
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                ->where('tahun', $tahun)
                                                                                                                                                                                                ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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

    public function get_kegiatan_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($tahun == 'semua')
        {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .= '<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->orderBy('kode', 'asc')->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                    ->orderBy('id', 'desc')->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
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
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
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
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
        } else {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->where('tahun_perubahan', $tahun)->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
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
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
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
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
    }

    public function get_sub_kegiatan()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="40%">Deskripsi</th>
                                    <th width="25%">Indikator Kinerja</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                                foreach ($urusans as $urusan) {
                                    $get_programs = Program::where('urusan_id', $urusan['id'])->get();
                                    $programs = [];
                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                        if($cek_perubahan_program)
                                        {
                                            $programs[] = [
                                                'id' => $get_program->id,
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
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['kode']).'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                    '.strtoupper($urusan['deskripsi']).'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse show" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr style="background: #c04141;">
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                    '.$urusan['kode'].'.'.$program['kode'].'
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                    '.strtoupper($program['deskripsi']);
                                                                                    $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                    if($cek_program_rjmd)
                                                                                    {
                                                                                        $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                    }
                                                                                    $html .= '<br>
                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                <td width="20%"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="collapse show" id="sub_kegiatan_program'.$program['id'].'">
                                                                                        <table class="table table-striped">
                                                                                            <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                                                                                                $kegiatans = [];
                                                                                                foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                    if($cek_perubahan_kegiatan)
                                                                                                    {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                            'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    } else {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                }
                                                                                                $a = 1;
                                                                                                foreach ($kegiatans as $kegiatan) {
                                                                                                    $html .= '<tr style="background: #41c0c0">
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                                                    '.strtoupper($kegiatan['deskripsi']).'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                                                <td width="20%">
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse show" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <tbody>';
                                                                                                                                $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])
                                                                                                                                                        ->orderBy('kode', 'asc')->get();
                                                                                                                                $sub_kegiatans = [];
                                                                                                                                foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                                                                                                                    $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                    if($cek_perubahan_sub_kegiatan)
                                                                                                                                    {
                                                                                                                                        $sub_kegiatans[] = [
                                                                                                                                            'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                            'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                                                                                                                            'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                            'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                                                                                                                            'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                                                                                                                                            'status_aturan' => $cek_perubahan_sub_kegiatan->status_aturan
                                                                                                                                        ];
                                                                                                                                    } else {
                                                                                                                                        $sub_kegiatans[] = [
                                                                                                                                            'id' => $get_sub_kegiatan->id,
                                                                                                                                            'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                            'kode' => $get_sub_kegiatan->kode,
                                                                                                                                            'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                            'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                            'status_aturan' => $get_sub_kegiatan->status_aturan
                                                                                                                                        ];
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                                                                    $html .= '<tr>
                                                                                                                                                <td width="15%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                </td>
                                                                                                                                                <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                    '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                    <br>
                                                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                </td>
                                                                                                                                                <td width="25%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                    $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                                    $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                    foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                                                                        $html .= '<span class="badge bg-muted text-uppercase sub-kegiatan-tagging">'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</li>';
                                                                                                                                                }
                                                                                                                                                $html .= '</ul></td>
                                                                                                                                                <td width="20%">
                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                            <tr>
                                                                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                                                                    <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'">
                                                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                                                            <thead>
                                                                                                                                                                <tr>
                                                                                                                                                                    <th>No</th>
                                                                                                                                                                    <th>Indikator</th>
                                                                                                                                                                    <th>OPD</th>
                                                                                                                                                                    <th>Target</th>
                                                                                                                                                                    <th>Satuan</th>
                                                                                                                                                                    <th>Target Anggaran</th>
                                                                                                                                                                    <th>Realisasi</th>
                                                                                                                                                                    <th>Realisasi Anggaran</th>
                                                                                                                                                                    <th>Tahun</th>
                                                                                                                                                                </tr>
                                                                                                                                                            </thead>
                                                                                                                                                            <tbody>';
                                                                                                                                                                $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja)
                                                                                                                                                                {
                                                                                                                                                                    $html .= '<tr>';
                                                                                                                                                                        $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                        $a = 1;
                                                                                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                            ->get();
                                                                                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja)
                                                                                                                                                                        {
                                                                                                                                                                            if($a == 1)
                                                                                                                                                                            {
                                                                                                                                                                                    $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                    $b = 1;
                                                                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                        {
                                                                                                                                                                                            $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                ->where('tahun', $tahun)
                                                                                                                                                                                                ->first();
                                                                                                                                                                                            if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                        $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                        $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                    foreach($tahuns as $tahun)
                                                                                                                                                                                    {
                                                                                                                                                                                        if($b == 1)
                                                                                                                                                                                        {
                                                                                                                                                                                            $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                ->where('tahun', $tahun)
                                                                                                                                                                                                ->first();
                                                                                                                                                                                            if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                            {
                                                                                                                                                                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                        $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                $html .='</tr>';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                    $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                    $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                    $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                    $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                    if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                    {
                                                                                                                                                                                                        $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                        $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                        foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                            $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                    $html .= '<td>'.$tahun.'</td>';
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
                                                </td>
                                            </tr>';
                                }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['html' => $html]);
    }

    public function get_sub_kegiatan_tahun($tahun)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        if($tahun == 'semua')
        {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                        '.$urusan['kode'].'.'.$program['kode'].'
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="sub_kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    $a = 1;
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                                                        '.strtoupper($kegiatan['deskripsi']).'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                                                    <td width="20%">
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                        <div class="collapse show" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <tbody>';
                                                                                                                                    $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])
                                                                                                                                                            ->orderBy('kode', 'desc')->get();
                                                                                                                                    $sub_kegiatans = [];
                                                                                                                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                                                                                                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)
                                                                                                                                                                        ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                        if($cek_perubahan_sub_kegiatan)
                                                                                                                                        {
                                                                                                                                            $sub_kegiatans[] = [
                                                                                                                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                                'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                                                                                                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                                                                                                                                'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                                                                                                                                                'status_aturan' => $cek_perubahan_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        } else {
                                                                                                                                            $sub_kegiatans[] = [
                                                                                                                                                'id' => $get_sub_kegiatan->id,
                                                                                                                                                'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                                'kode' => $get_sub_kegiatan->kode,
                                                                                                                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                                'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                                'status_aturan' => $get_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                                                                        $html .= '<tr>
                                                                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                    </td>
                                                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                        <br>
                                                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                    </td>
                                                                                                                                                    <td width="25%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                        $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                                                                            $html .= '<span class="badge bg-muted text-uppercase sub-kegiatan-tagging">'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '</li>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</ul></td>
                                                                                                                                                    <td width="20%">
                                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                        <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'">
                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                <thead>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <th>No</th>
                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                        <th>OPD</th>
                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                        <th>Satuan</th>
                                                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                    </tr>
                                                                                                                                                                </thead>
                                                                                                                                                                <tbody>';
                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                    $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                            $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                            $a = 1;
                                                                                                                                                                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                                ->get();
                                                                                                                                                                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                if($a == 1)
                                                                                                                                                                                {
                                                                                                                                                                                        $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                        $b = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['html' => $html]);
        } else {
            $get_urusans = Urusan::orderBy('kode', 'asc')->get();
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id'])->get();
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                        '.$urusan['kode'].'.'.$program['kode'].'
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .= '<br>
                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="sub_kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    $a = 1;
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                                                        '.strtoupper($kegiatan['deskripsi']).'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                                                    <td width="20%">
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                        <div class="collapse show" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <tbody>';
                                                                                                                                    $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->where('tahun_perubahan', $tahun)->orderBy('kode', 'asc')->get();
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
                                                                                                                                                'status_aturan' => $cek_perubahan_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        } else {
                                                                                                                                            $sub_kegiatans[] = [
                                                                                                                                                'id' => $get_sub_kegiatan->id,
                                                                                                                                                'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                                'kode' => $get_sub_kegiatan->kode,
                                                                                                                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                                'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                                'status_aturan' => $get_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                                                                        $html .= '<tr>
                                                                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                    </td>
                                                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                        <br>
                                                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                    </td>
                                                                                                                                                    <td width="25%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                        $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                                                                            $html .= '<span class="badge bg-muted text-uppercase sub-kegiatan-tagging">'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '</li>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</ul></td>
                                                                                                                                                    <td width="20%">
                                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                        <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'">
                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                <thead>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <th>No</th>
                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                        <th>OPD</th>
                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                        <th>Satuan</th>
                                                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                    </tr>
                                                                                                                                                                </thead>
                                                                                                                                                                <tbody>';
                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                    $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                            $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                            $a = 1;
                                                                                                                                                                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                                ->get();
                                                                                                                                                                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                if($a == 1)
                                                                                                                                                                                {
                                                                                                                                                                                        $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                        $b = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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

    public function filter_get_program(Request $request)
    {
        if($request->tahun == 'semua')
        {
            $get_programs = Program::where('urusan_id', $request->id)->get();
            $programs = [];

            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                if($cek_perubahan_program)
                {
                    $programs[] = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $programs[] = [
                        'id' => $get_program->id,
                        'kode' => $get_program->kode,
                        'deskripsi' => $get_program->deskripsi
                    ];
                }
            }

            return response()->json($programs);
        } else {
            $get_programs = Program::where('urusan_id', $request->id)->where('tahun_perubahan', $request->tahun)->get();
            $programs = [];

            foreach ($get_programs as $get_program) {
                $programs[] = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi
                ];
            }

            return response()->json($programs);
        }
    }

    public function filter_get_kegiatan(Request $request)
    {
        if($request->tahun == 'semua')
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
        } else {
            $get_kegiatans = Kegiatan::where('program_id', $request->id)->where('tahun_perubahan', $request->tahun)->get();
            $kegiatans = [];
            foreach ($get_kegiatans as $get_kegiatan) {
                $kegiatans[] = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi
                ];
            }
        }

        return response()->json($kegiatans);
    }

    public function filter_get_sub_kegiatan(Request $request)
    {
        if($request->tahun == 'semua')
        {
            $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $request->id)->get();
            $sub_kegiatans = [];
            foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)
                                                ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                if($cek_perubahan_sub_kegiatan)
                {
                    $sub_kegiatans[] = [
                        'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                        'kode' => $cek_perubahan_sub_kegiatan->kode,
                        'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                    ];
                } else {
                    $sub_kegiatans[] = [
                        'id' => $get_sub_kegiatan->id,
                        'kode' => $get_sub_kegiatan->kode,
                        'deskripsi' => $get_sub_kegiatan->deskripsi
                    ];
                }
            }
        } else {
            $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $request->id)->get();
            $sub_kegiatans = [];
            foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                $sub_kegiatans[] = [
                    'id' => $get_sub_kegiatan->id,
                    'kode' => $get_sub_kegiatan->kode,
                    'deskripsi' => $get_sub_kegiatan->deskripsi
                ];
            }
        }

        return response()->json($sub_kegiatans);
    }

    public function filter_sub_kegiatan(Request $request)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        if($request->tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class=" collapse show" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                        '.$urusan['kode'].'.'.$program['kode'].'
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .= '<br>
                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class=" collapse show" id="sub_kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    $a = 1;
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="25%"></td>
                                                                                                                    <td width="20%">
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                        <div class=" collapse show" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <tbody>';
                                                                                                                                    $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id']);
                                                                                                                                    if($request->sub_kegiatan)
                                                                                                                                    {
                                                                                                                                        $get_sub_kegiatans = $get_sub_kegiatans->where('id', $request->sub_kegiatan);
                                                                                                                                    }
                                                                                                                                    $get_sub_kegiatans = $get_sub_kegiatans->get();
                                                                                                                                    $sub_kegiatans = [];
                                                                                                                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                                                                                                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                        if($cek_perubahan_sub_kegiatan)
                                                                                                                                        {
                                                                                                                                            $sub_kegiatans[] = [
                                                                                                                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                                'kegiatan_id' => $cek_perubahan_sub_kegiatan->kegiatan_id,
                                                                                                                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi,
                                                                                                                                                'tahun_perubahan' => $cek_perubahan_sub_kegiatan->tahun_perubahan,
                                                                                                                                                'status_aturan' => $cek_perubahan_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        } else {
                                                                                                                                            $sub_kegiatans[] = [
                                                                                                                                                'id' => $get_sub_kegiatan->id,
                                                                                                                                                'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                                'kode' => $get_sub_kegiatan->kode,
                                                                                                                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                                'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                                'status_aturan' => $get_sub_kegiatan->status_aturan
                                                                                                                                            ];
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                    foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                                                                        $html .= '<tr>
                                                                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                    </td>
                                                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                        <br>
                                                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                    </td>
                                                                                                                                                    <td width="25%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                        $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                                                                            $html .= '<span class="badge bg-muted text-uppercase sub-kegiatan-tagging">'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '</li>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</ul></td>
                                                                                                                                                    <td width="20%">
                                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                        <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'">
                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                <thead>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <th>No</th>
                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                        <th>OPD</th>
                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                        <th>Satuan</th>
                                                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                    </tr>
                                                                                                                                                                </thead>
                                                                                                                                                                <tbody>';
                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                    $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                            $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                            $a = 1;
                                                                                                                                                                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                                ->get();
                                                                                                                                                                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                if($a == 1)
                                                                                                                                                                                {
                                                                                                                                                                                        $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                        $b = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                    </td>
                                                </tr>';
                                    }
                                $html .= '</tbody>
                            </table>
                        </div>
                    </div>';

            return response()->json(['html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="hiddenRow">
                                                        <div class=" collapse show" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                        '.$urusan['kode'].'.'.$program['kode'].'
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .= '<br>
                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="20%"></td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class=" collapse show" id="sub_kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    $a = 1;
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr style="background: #41c0c0">
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="15%">
                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle text-white" width="25%"></td>
                                                                                                                    <td width="20%">
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="12" class="hiddenRow">
                                                                                                                        <div class=" collapse show" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <tbody>';
                                                                                                                                    $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id']);
                                                                                                                                    if($request->sub_kegiatan)
                                                                                                                                    {
                                                                                                                                        $get_sub_kegiatans = $get_sub_kegiatans->where('id', $request->sub_kegiatan);
                                                                                                                                    }
                                                                                                                                    $get_sub_kegiatans = $get_sub_kegiatans->where('tahun_perubahan', $request->tahun)->get();
                                                                                                                                    $sub_kegiatans = [];
                                                                                                                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                                                                                                                        $sub_kegiatans[] = [
                                                                                                                                            'id' => $get_sub_kegiatan->id,
                                                                                                                                            'kegiatan_id' => $get_sub_kegiatan->kegiatan_id,
                                                                                                                                            'kode' => $get_sub_kegiatan->kode,
                                                                                                                                            'deskripsi' => $get_sub_kegiatan->deskripsi,
                                                                                                                                            'tahun_perubahan' => $get_sub_kegiatan->tahun_perubahan,
                                                                                                                                            'status_aturan' => $get_sub_kegiatan->status_aturan
                                                                                                                                        ];
                                                                                                                                    }
                                                                                                                                    foreach ($sub_kegiatans as $sub_kegiatan) {
                                                                                                                                        $html .= '<tr>
                                                                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                    </td>
                                                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle">
                                                                                                                                                        '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                        <br>
                                                                                                                                                        <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                        <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                        <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                    </td>
                                                                                                                                                    <td width="25%" data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'" class="accordion-toggle"><ul>';
                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                                                                                                                                                        $html .= '<li class="mb-2">'.$sub_kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                                        $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)->get();
                                                                                                                                                        foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja) {
                                                                                                                                                            $html .= '<span class="badge bg-muted text-uppercase sub-kegiatan-tagging">'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                                        }
                                                                                                                                                        $html .= '</li>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</ul></td>
                                                                                                                                                    <td width="20%">
                                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                                                    </td>
                                                                                                                                                </tr>
                                                                                                                                                <tr>
                                                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                                                        <div class="collapse accordion-body" id="sub_kegiatan_sub_kegiatan'.$sub_kegiatan['id'].'">
                                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                                <thead>
                                                                                                                                                                    <tr>
                                                                                                                                                                        <th>No</th>
                                                                                                                                                                        <th>Indikator</th>
                                                                                                                                                                        <th>OPD</th>
                                                                                                                                                                        <th>Target</th>
                                                                                                                                                                        <th>Satuan</th>
                                                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                                                        <th>Realisasi</th>
                                                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                                                        <th>Tahun</th>
                                                                                                                                                                    </tr>
                                                                                                                                                                </thead>
                                                                                                                                                                <tbody>';
                                                                                                                                                                    $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::where('sub_kegiatan_id', $sub_kegiatan['id'])->get();
                                                                                                                                                                    $no_sub_kegiatan_indikator_kinerja = 1;
                                                                                                                                                                    foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja)
                                                                                                                                                                    {
                                                                                                                                                                        $html .= '<tr>';
                                                                                                                                                                            $html .= '<td>'.$no_sub_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                                                            $html .= '<td>'.$sub_kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                                                            $a = 1;
                                                                                                                                                                            $opd_sub_kegiatan_indikator_kinerjas = OpdSubKegiatanIndikatorKinerja::where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                                ->get();
                                                                                                                                                                            foreach ($opd_sub_kegiatan_indikator_kinerjas as $opd_sub_kegiatan_indikator_kinerja)
                                                                                                                                                                            {
                                                                                                                                                                                if($a == 1)
                                                                                                                                                                                {
                                                                                                                                                                                        $html .= '<td>'.$opd_sub_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                                                                                                                                                                        $b = 1;
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                        foreach($tahuns as $tahun)
                                                                                                                                                                                        {
                                                                                                                                                                                            if($b == 1)
                                                                                                                                                                                            {
                                                                                                                                                                                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::where('opd_sub_kegiatan_indikator_kinerja_id', $opd_sub_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                                                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                                                                {
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                                                    $html .='</tr>';
                                                                                                                                                                                                } else {
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td></td>';
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$cek_sub_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                                                        $html .= '<td>'.$sub_kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                                                        $html .= '<td>Rp.'.number_format($cek_sub_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                                                                        $cek_sub_kegiatan_tw_realisasi = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                                                        if($cek_sub_kegiatan_tw_realisasi)
                                                                                                                                                                                                        {
                                                                                                                                                                                                            $sub_kegiatan_tw_realisasies = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                                                            $sub_kegiatan_realisasi = [];
                                                                                                                                                                                                            foreach ($sub_kegiatan_tw_realisasies as $sub_kegiatan_tw_realisasi) {
                                                                                                                                                                                                                $sub_kegiatan_realisasi[] = $sub_kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                                                            }
                                                                                                                                                                                                            $html .= '<td>'.array_sum($sub_kegiatan_realisasi).'</td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                                                        $html .= '<td>'.$tahun.'</td>';
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

    public function filter_kegiatan(Request $request)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($request->tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Indikator Kinerja</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                    $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                    if($request->kegiatan)
                                                                                                    {
                                                                                                        $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan);
                                                                                                    }
                                                                                                    $get_kegiatans = $get_kegiatans->get();

                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                                                                                                                    ->orderBy('id', 'desc')->first();
                                                                                                        if($cek_perubahan_kegiatan)
                                                                                                        {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                'program_id' => $cek_perubahan_kegiatan->program_id,
                                                                                                                'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $cek_perubahan_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        } else {
                                                                                                            $kegiatans[] = [
                                                                                                                'id' => $get_kegiatan->id,
                                                                                                                'program_id' => $get_kegiatan->program_id,
                                                                                                                'kode' => $get_kegiatan->kode,
                                                                                                                'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                                'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                                'status_aturan' => $get_kegiatan->status_aturan
                                                                                                            ];
                                                                                                        }
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
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
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
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
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
        } else {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                            <table class="table table-striped table-condesed">
                                <thead>
                                    <tr>
                                        <th width="15%">Kode</th>
                                        <th width="40%">Deskripsi</th>
                                        <th width="25%">Tahun Perubahan</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                    foreach ($urusans as $urusan) {
                                        $get_programs = Program::where('urusan_id', $urusan['id']);
                                        if($request->program)
                                        {
                                            $get_programs = $get_programs->where('id', $request->program);
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
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['kode']).'
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle text-white">
                                                        '.strtoupper($urusan['deskripsi']).'
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="collapse show" id="kegiatan_urusan'.$urusan['id'].'">
                                                            <table class="table table-striped table-condesed">
                                                                <tbody>';
                                                                    foreach ($programs as $program) {
                                                                        $html .= '<tr style="background: #c04141;">
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="15%">'.strtoupper($urusan['kode']).'.'.strtoupper($program['kode']).'</td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle text-white" width="40%">
                                                                                        '.strtoupper($program['deskripsi']);
                                                                                        $cek_program_rjmd = ProgramRpjmd::where('program_id', $program['id'])->first();
                                                                                        if($cek_program_rjmd)
                                                                                        {
                                                                                            $html .= '<i class="fas fa-star text-primary" title="Program RPJMD">';
                                                                                        }
                                                                                        $html .='<br>
                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="25%"></td>
                                                                                    <td width="20%">
                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="12" class="hiddenRow">
                                                                                        <div class="collapse show" id="kegiatan_program'.$program['id'].'">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>';
                                                                                                $get_kegiatans = Kegiatan::where('program_id', $program['id']);
                                                                                                if($request->kegiatan)
                                                                                                {
                                                                                                    $get_kegiatans = $get_kegiatans->where('id', $request->kegiatan);
                                                                                                }
                                                                                                $get_kegiatans = $get_kegiatans->get();
                                                                                                    $kegiatans = [];
                                                                                                    foreach ($get_kegiatans as $get_kegiatan) {
                                                                                                        $kegiatans[] = [
                                                                                                            'id' => $get_kegiatan->id,
                                                                                                            'program_id' => $get_kegiatan->program_id,
                                                                                                            'kode' => $get_kegiatan->kode,
                                                                                                            'deskripsi' => $get_kegiatan->deskripsi,
                                                                                                            'tahun_perubahan' => $get_kegiatan->tahun_perubahan,
                                                                                                            'status_aturan' => $get_kegiatan->status_aturan
                                                                                                        ];
                                                                                                    }
                                                                                                    foreach ($kegiatans as $kegiatan) {
                                                                                                        $html .= '<tr>
                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                    <td width="40%" data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >
                                                                                                                        '.$kegiatan['deskripsi'].'
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                        <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                        <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                    </td>';
                                                                                                                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                    $html .= '<td width="25%"><ul data-bs-toggle="collapse" data-bs-target="#kegiatan_kegiatan_'.$kegiatan['id'].'" >';
                                                                                                                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                        $html .= '<li class="mb-2">'.$kegiatan_indikator_kinerja->deskripsi.'<br>';
                                                                                                                                $opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->first();
                                                                                                                                if($opd_kegiatan_indikator_kinerja)
                                                                                                                                {
                                                                                                                                    $html .= '<span class="badge bg-muted text-uppercase kegiatan-tagging">'.$opd_kegiatan_indikator_kinerja->opd->nama.'</span>';
                                                                                                                                }
                                                                                                                        $html .= '</li>';
                                                                                                                    }
                                                                                                                    $html .='</ul></td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" data-tahun="semua" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
                                                                                                                        <button class="btn btn-icon btn-danger waves-effect waves-light hapus-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-tahun="semua" type="button" title="Hapus Kegiatan"><i class="fas fa-trash"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                                        <div class="collapse accordion-body" id="kegiatan_kegiatan_'.$kegiatan['id'].'">
                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                <thead>
                                                                                                                                    <tr>
                                                                                                                                        <th>No</th>
                                                                                                                                        <th>Indikator</th>
                                                                                                                                        <th>Target Kinerja Awal</th>
                                                                                                                                        <th>Target Anggaran Awal</th>
                                                                                                                                        <th>OPD</th>
                                                                                                                                        <th>Target</th>
                                                                                                                                        <th>Satuan</th>
                                                                                                                                        <th>Target Anggaran</th>
                                                                                                                                        <th>Realisasi</th>
                                                                                                                                        <th>Realisasi Anggaran</th>
                                                                                                                                        <th>Tahun</th>
                                                                                                                                    </tr>
                                                                                                                                </thead>
                                                                                                                                <tbody>';
                                                                                                                                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                                                                                                                                $no_kegiatan_indikator_kinerja = 1;
                                                                                                                                foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                                                                                                                    $html .= '<tr>';
                                                                                                                                        $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                                                                                                                        $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                                                                                                                        $html .= '<td>Rp.'.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                                                                                                                        $a = 1;
                                                                                                                                        $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)
                                                                                                                                                                            ->get();
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
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2,',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        } else {
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</>';
                                                                                                                                                        }
                                                                                                                                                    } else {
                                                                                                                                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                        {
                                                                                                                                                            $html .= '<tr>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                            $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                            $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                            $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                            if($cek_kegiatan_tw_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                $kegiatan_realisasi = [];
                                                                                                                                                                foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                    $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                            }
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td></td>';
                                                                                                                                                            $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                            $html .='</tr>';
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                    $b++;
                                                                                                                                                }
                                                                                                                                            } else {
                                                                                                                                                $html .= '<tr>';
                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                    $html .= '<td></td>';
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
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            } else {
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                                                                                                $html .='</tr>';
                                                                                                                                                            }
                                                                                                                                                        } else {
                                                                                                                                                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                                                                                                                                    ->where('tahun', $tahun)
                                                                                                                                                                                                    ->first();
                                                                                                                                                            if($cek_kegiatan_target_satuan_rp_realisasi)
                                                                                                                                                            {
                                                                                                                                                                $html .= '<tr>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                                                                                                                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                                                                                                                                $html .= '<td>Rp.'.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp, 2, ',', '.').'</td>';
                                                                                                                                                                $cek_kegiatan_tw_realisasi = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->first();
                                                                                                                                                                if($cek_kegiatan_tw_realisasi)
                                                                                                                                                                {
                                                                                                                                                                    $kegiatan_tw_realisasies = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)->get();
                                                                                                                                                                    $kegiatan_realisasi = [];
                                                                                                                                                                    foreach ($kegiatan_tw_realisasies as $kegiatan_tw_realisasi) {
                                                                                                                                                                        $kegiatan_realisasi[] = $kegiatan_tw_realisasi->realisasi;
                                                                                                                                                                    }
                                                                                                                                                                    $html .= '<td>'.array_sum($kegiatan_realisasi).'</td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                } else {
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                    $html .= '<td></td>';
                                                                                                                                                                }
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td></td>';
                                                                                                                                                                $html .= '<td>'.$tahun.'</td>';
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
    }

    public function filter_program(Request $request)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        if($request->tahun == 'semua')
        {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                                    if($request->program)
                                    {
                                        $get_programs = $get_programs->where('id', $request->program);
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

            return response()->json(['html' => $html]);
        } else {
            $get_urusans = new Urusan;
            if($request->urusan)
            {
                $get_urusans = $get_urusans->where('id', $request->urusan);
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
                                    if($request->program)
                                    {
                                        $get_programs = $get_programs->where('id', $request->program);
                                    }
                                    $get_programs = $get_programs->where('tahun_perubahan', $request->tahun);
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

            return response()->json(['html' => $html]);
        }
    }
}
