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
use App\Models\TargetRpPertahunSasaran;
use App\Models\ProgramRpjmd;
use App\Models\PivotOpdProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;

class RenstraKegiatanController extends Controller
{
    public function get_kegiatan(Request $request)
    {
        $get_kegiatans = Kegiatan::select('id', 'kode','deskripsi')->where('program_id', $request->id)->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::select('kegiatan_id', 'kode', 'deskripsi')
                                        ->where('kegiatan_id', $get_kegiatan->id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'kode' => $cek_perubahan_kegiatan->kode
                ];
            } else {
                $kegiatan[] = [
                    'id' => $get_kegiatan->id,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'kode' => $get_kegiatan->kode
                ];
            }
        }
        return response()->json($kegiatan);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'renstra_kegiatan_program_id' => 'required',
            'renstra_kegiatan_program_rpjmd_id' => 'required',
            'renstra_kegiatan_kegiatan_id' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot = new PivotProgramKegiatanRenstra;
        $pivot->program_rpjmd_id = $request->renstra_kegiatan_program_rpjmd_id;
        $pivot->program_id = $request->renstra_kegiatan_program_id;
        $pivot->kegiatan_id = $request->renstra_kegiatan_kegiatan_id;
        $pivot->save();

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="70%">Misi</th>
                                    <th width="15%">Tahun Perubahan</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $get_misis = Misi::all();
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
                            foreach ($misis as $misi) {
                                $html .='<tr>
                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi'.$misi['id'].'" class="accordion-toggle">
                                            '.$misi['kode'].'
                                        </td>
                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi'.$misi['id'].'" class="accordion-toggle">
                                            '.$misi['deskripsi'].'
                                            <br>
                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                        </td>
                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi'.$misi['id'].'" class="accordion-toggle">
                                            '.$misi['tahun_perubahan'].'
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="hiddenRow">
                                            <div class="accordion-body collapse" id="renstra_kegiatan_misi'.$misi['id'].'">
                                                <table class="table table-striped table-condesed">
                                                    <tbody>';
                                                        $renstra_get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                        $renstra_tujuans = [];
                                                        foreach ($renstra_get_tujuans as $renstra_get_tujuan) {
                                                            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $renstra_get_tujuan->id)
                                                                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                            if($cek_perubahan_tujuan)
                                                            {
                                                                $renstra_tujuans[] = [
                                                                    'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                    'kode' => $cek_perubahan_tujuan->kode,
                                                                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                                                                ];
                                                            } else {
                                                                $renstra_tujuans[] = [
                                                                    'id' => $renstra_get_tujuan->id,
                                                                    'kode' => $renstra_get_tujuan->kode,
                                                                    'deskripsi' => $renstra_get_tujuan->deskripsi,
                                                                    'tahun_perubahan' => $renstra_get_tujuan->tahun_perubahan
                                                                ];
                                                            }
                                                        }
                                                        foreach ($renstra_tujuans as $renstra_tujuan) {
                                                            $html .= '<tr>
                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan'.$renstra_tujuan['id'].'" class="accordion-toggle" width="15%">
                                                                            '.$renstra_tujuan['kode'].'
                                                                        </td>
                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan'.$renstra_tujuan['id'].'" class="accordion-toggle" width="70%">
                                                                            '.$renstra_tujuan['deskripsi'].'
                                                                            <br>
                                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                            <span class="badge bg-secondary text-uppercase">'.$renstra_tujuan['kode'].' Tujuan</span>
                                                                        </td>
                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan'.$renstra_tujuan['id'].'" class="accordion-toggle" width="15%">
                                                                            '.$renstra_tujuan['tahun_perubahan'].'
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="3" class="hiddenRow">
                                                                            <div class="accordion-body collapse" id="renstra_kegiatan_tujuan'.$renstra_tujuan['id'].'">
                                                                                <table class="table table-striped table-condesed">
                                                                                    <tbody>';
                                                                                        $get_renstra_sasarans = Sasaran::where('tujuan_id', $renstra_tujuan['id'])->get();
                                                                                        $renstra_sasarans = [];
                                                                                        foreach ($get_renstra_sasarans as $get_renstra_sasaran) {
                                                                                            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_renstra_sasaran->id)
                                                                                                                        ->orderBy('tahun_perubahan', 'desc')
                                                                                                                        ->latest()->first();
                                                                                            if($cek_perubahan_sasaran)
                                                                                            {
                                                                                                $renstra_sasarans[] = [
                                                                                                    'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                    'kode' => $cek_perubahan_sasaran->kode,
                                                                                                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan
                                                                                                ];
                                                                                            } else {
                                                                                                $renstra_sasarans[] = [
                                                                                                    'id' => $get_renstra_sasaran->id,
                                                                                                    'kode' => $get_renstra_sasaran->kode,
                                                                                                    'deskripsi' => $get_renstra_sasaran->deskripsi,
                                                                                                    'tahun_perubahan' => $get_renstra_sasaran->tahun_perubahan
                                                                                                ];
                                                                                            }
                                                                                        }
                                                                                        foreach ($renstra_sasarans as $sasaran) {
                                                                                            $html .= '<tr>
                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran'.$sasaran['id'].'" class="accordion-toggle" width="15%">'.$sasaran['kode'].'</td>
                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran'.$sasaran['id'].'" class="accordion-toggle" width="70%">
                                                                                                    '.$sasaran['deskripsi'].'
                                                                                                    <br>
                                                                                                    <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                    <span class="badge bg-secondary text-uppercase">'.$renstra_tujuan['kode'].' Tujuan</span>
                                                                                                    <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                </td>
                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran'.$sasaran['id'].'" class="accordion-toggle" width="15%">
                                                                                                    '.$sasaran['tahun_perubahan'].'
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="hiddenRow" colspan="3">
                                                                                                    <div class="accordion-body collapse" id="renstra_kegiatan_sasaran'.$sasaran['id'].'">
                                                                                                        <table class="table table-striped table-condesed">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th width="60%">Sasaran Indikator</th>
                                                                                                                    <th width="20%">Target</th>
                                                                                                                    <th width="20%">Satuan</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>';
                                                                                                                $get_renstra_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                foreach ($get_renstra_sasaran_indikators as $sasaran_indikator) {
                                                                                                                    $html .= '<tr>
                                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                            '.$sasaran_indikator->indikator.'
                                                                                                                            <br>
                                                                                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                            <span class="badge bg-secondary text-uppercase">'.$renstra_tujuan['kode'].' Tujuan</span>
                                                                                                                            <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                            <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                        </td>
                                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                            '.$sasaran_indikator->target.'
                                                                                                                        </td>
                                                                                                                        <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator'.$sasaran_indikator['id'].'" class="accordion-toggle">
                                                                                                                            '.$sasaran_indikator->satuan.'
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                    <tr>
                                                                                                                        <td class="hiddenRow" colspan="3">
                                                                                                                            <div class="accordion-body collapse" id="renstra_kegiatan_sasaran_indikator'.$sasaran_indikator['id'].'">
                                                                                                                                <table class="table table-striped table-condesed">
                                                                                                                                    <thead>
                                                                                                                                        <tr>
                                                                                                                                            <th width="45%">Program RPJMD</th>
                                                                                                                                            <th width="15%">Status Program</th>
                                                                                                                                            <th width="15%">Pagu</th>
                                                                                                                                            <th width="15%">OPD</th>
                                                                                                                                            <th width="10%">Aksi</th>
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
                                                                                                                                                                                'program_id' => $cek_perubahan_program->program_id
                                                                                                                                                                            ];
                                                                                                                                                                        } else {
                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                'program_id' => $program->id
                                                                                                                                                                            ];
                                                                                                                                                                        }
                                                                                                                                                                    }
                                                                                                                                        foreach ($programs as $program) {
                                                                                                                                            $html .= '<tr>
                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                    '.$program['deskripsi'].'
                                                                                                                                                    <br>
                                                                                                                                                    <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                                    <span class="badge bg-secondary text-uppercase">'.$renstra_tujuan['kode'].' Tujuan</span>
                                                                                                                                                    <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                                    <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                                    <span class="badge bg-success text-uppercase">Program RPJMD</span>
                                                                                                                                                </td>
                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                    '.$program['status_program'].'
                                                                                                                                                </td>
                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd'.$program['id'].'" class="accordion-toggle">
                                                                                                                                                    '.$program['pagu'].'
                                                                                                                                                </td>
                                                                                                                                                <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd'.$program['id'].'" class="accordion-toggle">';
                                                                                                                                                    $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                    $html .= '<ul>';
                                                                                                                                                    foreach ($get_opds as $get_opd) {
                                                                                                                                                        $html .= '<li>'.$get_opd->opd->nama.'</li>';
                                                                                                                                                    }
                                                                                                                                                    $html .= '</ul>
                                                                                                                                                </td>
                                                                                                                                                <td>
                                                                                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 renstra_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditRenstraKegiatanModal" title="Tambah Kegiatan" data-program-id="'.$program['program_id'].'" data-program-rpjmd-id="'.$program['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                                                                </td>
                                                                                                                                            </tr>
                                                                                                                                            <tr>
                                                                                                                                                <td class="hiddenRow" colspan="5">
                                                                                                                                                    <div class="accordion-body collapse" id="renstra_kegiatan_program_rpjmd'.$program['id'].'">
                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                        <thead>
                                                                                                                                                            <tr>
                                                                                                                                                                <th width="85%">Kegiatan</th>
                                                                                                                                                                <th width="15%">Tahun Perubahan</th>
                                                                                                                                                            </tr>
                                                                                                                                                        </thead>
                                                                                                                                                        <tbody>';
                                                                                                                                                        $pivot_kegiatan_renstras = PivotProgramKegiatanRenstra::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                    ->where('program_id', $program['program_id'])
                                                                                                                                                                                    ->get();
                                                                                                                                                        $kegiatans = [];
                                                                                                                                                        foreach ($pivot_kegiatan_renstras as $kegiatan_renstra) {
                                                                                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_renstra->kegiatan_id)
                                                                                                                                                                                        ->orderBy('tahun_perubahan', 'desc')
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
                                                                                                                                                                $kegiatan = Kegiatan::find($kegiatan_renstra->kegiatan_id);
                                                                                                                                                                $kegiatans[] = [
                                                                                                                                                                    'id' => $kegiatan->id,
                                                                                                                                                                    'kode' => $kegiatan->kode,
                                                                                                                                                                    'deskripsi' => $kegiatan->deskripsi,
                                                                                                                                                                    'tahun_perubahan' => $kegiatan->tahun_perubahan
                                                                                                                                                                ];
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                        foreach ($kegiatans as $kegiatan) {
                                                                                                                                                            $html .= '<tr>
                                                                                                                                                                <td>'.$kegiatan['deskripsi'].'</td>
                                                                                                                                                                <td>'.$kegiatan['tahun_perubahan'].'</td>
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
        return response()->json(['success' => $html]);
    }
}
