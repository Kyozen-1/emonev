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
    public function index()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        return view('admin.nomenklatur.index', [
            'tahuns' => $tahuns
        ]);
    }

    public function get_program()
    {
        $get_urusans = Urusan::all();
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
