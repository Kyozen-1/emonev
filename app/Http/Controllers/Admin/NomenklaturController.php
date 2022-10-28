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
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
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

        return view('admin.nomenklatur.index', [
            'tahuns' => $tahuns,
            'urusans' => $urusans
        ]);
    }

    public function get_program()
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                            <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="50%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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
                                $html .= '<tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['kode'].'
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['deskripsi'].'
                                                <br>
                                                <span class="badge bg-primary text-uppercase program-tagging">'.$urusan['kode'].' Urusan</span>
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['tahun_perubahan'].'
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-plus"></i></button>
                                                <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_program.xlsx').'" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-file-import"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="hiddenRow">
                                                <div class="collapse" id="program_urusan'.$urusan['id'].'">
                                                    <table class="table table-striped table-condesed">
                                                        <tbody>';
                                                            foreach ($programs as $program) {
                                                                $html .= '<tr>
                                                                            <td width="15%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                            <td width="50%">
                                                                                '.$program['deskripsi'].'
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase program-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                <span class="badge bg-warning text-uppercase program-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                            </td>
                                                                            <td width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program" data-program-id="'.$program['id'].'" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-program" data-program-id="'.$program['id'].'" data-urusan-id="'.$urusan['id'].'" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingKegiatan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="50%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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

                                    $html .= '<tr>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['kode'].'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['deskripsi'].'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['tahun_perubahan'].'
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse" id="kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="50%">
                                                                                    '.$program['deskripsi'].'
                                                                                    <br>
                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                                <td width="20%">
                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_kegiatan.xlsx').'" title="Download Template Import Data Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                                                    <button class="btn btn-info waves-effect waves-light kegiatan_btn_impor_template" title="Import Data Kegiatan" type="button" data-program-id="'.$program['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="collapse" id="kegiatan_program'.$program['id'].'">
                                                                                        <table class="table table-striped table-condesed">
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
                                                                                                foreach ($kegiatans as $kegiatan) {
                                                                                                    $html .= '<tr>
                                                                                                                <td width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                <td width="50%">
                                                                                                                    '.$kegiatan['deskripsi'].'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                    <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>
                                                                                                                <td width="15%">'.$kegiatan['tahun_perubahan'].'</td>
                                                                                                                <td width="20%">
                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
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

    public function get_sub_kegiatan()
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingSubKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingSubKegiatan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="50%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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

                                    $html .= '<tr>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['kode'].'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['deskripsi'].'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['tahun_perubahan'].'
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="hiddenRow">
                                                    <div class="collapse" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%">
                                                                                    '.$urusan['kode'].'.'.$program['kode'].'
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="50%">
                                                                                    '.$program['deskripsi'].'
                                                                                    <br>
                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                                <td width="20%"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="collapse" id="sub_kegiatan_program'.$program['id'].'">
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
                                                                                                    $html .= '<tr>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="15%">
                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="50%">
                                                                                                                    '.$kegiatan['deskripsi'].'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="15%">'.$kegiatan['tahun_perubahan'].'</td>
                                                                                                                <td width="20%">
                                                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 sub_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSubKegiatanModal" title="Tambah Data Sub Kegiatan" data-kegiatan-id="'.$kegiatan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sub_kegiatan.xlsx').'" title="Download Template Import Data Sub Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                                                                                    <button class="btn btn-info waves-effect waves-light sub_kegiatan_btn_impor_template" title="Import Data Sub Kegiatan" type="button" data-kegiatan-id="'.$kegiatan['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="collapse" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
                                                                                                                        <table class="table table-striped table-condesed">
                                                                                                                            <tbody>';
                                                                                                                                $get_sub_kegiatans = SubKegiatan::where('kegiatan_id', $kegiatan['id'])->get();
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
                                                                                                                                                <td width="15%">
                                                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                </td>
                                                                                                                                                <td width="50%">
                                                                                                                                                    '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                    <br>
                                                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                </td>
                                                                                                                                                <td width="15%">'.$sub_kegiatan['tahun_perubahan'].'</td>
                                                                                                                                                <td width="20%">
                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
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

    public function filter_get_program(Request $request)
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
    }

    public function filter_get_kegiatan(Request $request)
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

    public function filter_get_sub_kegiatan(Request $request)
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

        return response()->json($sub_kegiatans);
    }

    public function filter_sub_kegiatan(Request $request)
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingSubKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingSubKegiatan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="70%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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

                                    $html .= '<tr>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['kode'].'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['deskripsi'].'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['tahun_perubahan'].'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="hiddenRow">
                                                    <div class="accordion-body collapse" id="sub_kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%">
                                                                                    '.$urusan['kode'].'.'.$program['kode'].'
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="70%">
                                                                                    '.$program['deskripsi'].'
                                                                                    <br>
                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="accordion-body collapse" id="sub_kegiatan_program'.$program['id'].'">
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
                                                                                                    $html .= '<tr>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="15%">
                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="50%">
                                                                                                                    '.$kegiatan['deskripsi'].'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>
                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sub_kegiatan_kegiatan'.$kegiatan['id'].'" class="accordion-toggle" width="15%">'.$kegiatan['tahun_perubahan'].'</td>
                                                                                                                <td width="20%">
                                                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 sub_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSubKegiatanModal" title="Tambah Data Sub Kegiatan" data-kegiatan-id="'.$kegiatan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sub_kegiatan.xlsx').'" title="Download Template Import Data Sub Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                                                                                    <button class="btn btn-info waves-effect waves-light sub_kegiatan_btn_impor_template" title="Import Data Sub Kegiatan" type="button" data-kegiatan-id="'.$kegiatan['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr>
                                                                                                                <td colspan="12" class="hiddenRow">
                                                                                                                    <div class="accordion-body collapse" id="sub_kegiatan_kegiatan'.$kegiatan['id'].'">
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
                                                                                                                                                <td width="15%">
                                                                                                                                                    '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'
                                                                                                                                                </td>
                                                                                                                                                <td width="50%">
                                                                                                                                                    '.$sub_kegiatan['deskripsi'].'
                                                                                                                                                    <br>
                                                                                                                                                    <span class="badge bg-primary text-uppercase sub-kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-warning text-uppercase sub-kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                                                                                    <span class="badge bg-danger text-uppercase sub-kegiatan-tagging">Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</span>
                                                                                                                                                    <span class="badge bg-info text-uppercase sub-kegiatan-tagging">Sub Kegiatan '.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'.'.$sub_kegiatan['kode'].'</span>
                                                                                                                                                </td>
                                                                                                                                                <td width="15%">'.$sub_kegiatan['tahun_perubahan'].'</td>
                                                                                                                                                <td width="20%">
                                                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" type="button" title="Detail Sub Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sub-kegiatan" data-sub-kegiatan-id="'.$sub_kegiatan['id'].'" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Edit Sub Kegiatan"><i class="fas fa-edit"></i></button>
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

    public function filter_kegiatan(Request $request)
    {
        $get_urusans = new Urusan;
        if($request->urusan)
        {
            $get_urusans = $get_urusans->where('id', $request->urusan);
        }
        $get_urusans = $get_urusans->get();
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingKegiatan" checked>
                            <label class="form-check-label" for="onOffTaggingKegiatan">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="70%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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

                                    $html .= '<tr>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['kode'].'
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['deskripsi'].'
                                                    <br>
                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                </td>
                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                    '.$urusan['tahun_perubahan'].'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="hiddenRow">
                                                    <div class="accordion-body collapse" id="kegiatan_urusan'.$urusan['id'].'">
                                                        <table class="table table-striped table-condesed">
                                                            <tbody>';
                                                                foreach ($programs as $program) {
                                                                    $html .= '<tr>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="50%">
                                                                                    '.$program['deskripsi'].'
                                                                                    <br>
                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                                </td>
                                                                                <td data-bs-toggle="collapse" data-bs-target="#kegiatan_program'.$program['id'].'" class="accordion-toggle" width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                                <td width="20%">
                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditKegiatanModal" title="Tambah Data Kegiatan" data-program-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_kegiatan.xlsx').'" title="Download Template Import Data Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                                                    <button class="btn btn-info waves-effect waves-light kegiatan_btn_impor_template" title="Import Data Kegiatan" type="button" data-program-id="'.$program['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="12" class="hiddenRow">
                                                                                    <div class="accordion-body collapse" id="kegiatan_program'.$program['id'].'">
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
                                                                                                foreach ($kegiatans as $kegiatan) {
                                                                                                    $html .= '<tr>
                                                                                                                <td width="15%">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].'</td>
                                                                                                                <td width="50%">
                                                                                                                    '.$kegiatan['deskripsi'].'
                                                                                                                    <br>
                                                                                                                    <span class="badge bg-primary text-uppercase kegiatan-tagging">'.$urusan['kode'].' Urusan</span>
                                                                                                                    <span class="badge bg-warning text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].' Program</span>
                                                                                                                    <span class="badge bg-danger text-uppercase kegiatan-tagging">'.$urusan['kode'].'.'.$program['kode'].'.'.$kegiatan['kode'].' Kegiatan</span>
                                                                                                                </td>
                                                                                                                <td width="15%">'.$kegiatan['tahun_perubahan'].'</td>
                                                                                                                <td width="20%">
                                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Detail Kegiatan"><i class="fas fa-eye"></i></button>
                                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-kegiatan" data-kegiatan-id="'.$kegiatan['id'].'" data-program-id="'.$program['id'].'" type="button" title="Edit Kegiatan"><i class="fas fa-edit"></i></button>
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

    public function filter_program(Request $request)
    {
        $get_urusans = new Urusan;
        if($request->urusan)
        {
            $get_urusans = $get_urusans->where('id', $request->urusan);
        }
        $get_urusans = $get_urusans->get();
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

        $html = '<div class="row mb-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                            <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                        </div>
                    </div>
                </div>
                <div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="50%">Deskripsi</th>
                                    <th width="15%">Tahun Perubahan</th>
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
                                $html .= '<tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['kode'].'
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['deskripsi'].'
                                                <br>
                                                <span class="badge bg-primary text-uppercase program-tagging">'.$urusan['kode'].' Urusan</span>
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#program_urusan'.$urusan['id'].'" class="accordion-toggle">
                                                '.$urusan['tahun_perubahan'].'
                                            </td>
                                            <td>
                                                <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-plus"></i></button>
                                                <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_program.xlsx').'" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button" data-urusan-id="'.$urusan['id'].'"><i class="fas fa-file-import"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="hiddenRow">
                                                <div class="accordion-body collapse" id="program_urusan'.$urusan['id'].'">
                                                    <table class="table table-striped table-condesed">
                                                        <tbody>';
                                                            foreach ($programs as $program) {
                                                                $html .= '<tr>
                                                                            <td width="15%">'.$urusan['kode'].'.'.$program['kode'].'</td>
                                                                            <td width="50%">
                                                                                '.$program['deskripsi'].'
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase program-tagging">Urusan '.$urusan['kode'].'</span>
                                                                                <span class="badge bg-warning text-uppercase program-tagging">Program '.$urusan['kode'].'.'.$program['kode'].'</span>
                                                                            </td>
                                                                            <td width="15%"> '.$program['tahun_perubahan'].'</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program" data-program-id="'.$program['id'].'" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-program" data-program-id="'.$program['id'].'" data-urusan-id="'.$urusan['id'].'" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
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
