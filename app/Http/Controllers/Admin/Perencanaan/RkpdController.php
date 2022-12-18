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
use App\Models\RkpdTahunPembangunan;
use App\Models\RkpdOpdTahunPembangunan;
use App\Models\RkpdTahunPembangunanUrusan;
use App\Models\RkpdTahunPembangunanProgram;
use App\Models\RkpdTahunPembangunanKegiatan;
use App\Models\RkpdTahunPembangunanSubKegiatan;

class RkpdController extends Controller
{
    public function renja_tahun_pembangunan()
    {
        if(request()->ajax())
        {
            $data = RkpdTahunPembangunan::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="rkpd_tahun_pembangunan_detail" id="'.$data->id.'" class="rkpd_tahun_pembangunan_detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="rkpd_tahun_pembangunan_edit" id="'.$data->id.'" class="rkpd_tahun_pembangunan_edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="rkpd_tahun_pembangunan_delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function renja_tahun_pembangunan_store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'rkpd_tahun_pembangunan_deskripsi' => 'required',
            'rkpd_tahun_pembangunan_tahun' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $cek_tahun_pembangunan = RkpdTahunPembangunan::where('tahun', $request->rkpd_tahun_pembangunan_tahun)->first();
        if($cek_tahun_pembangunan)
        {
            return response()->json(['errors' => 'Tahun '.$request->rkpd_tahun_pembangunan_tahun.' Sugah memiliki tema pembangunan' ]);
        }

        $rkpd_tahun_pembangunan = new RkpdTahunPembangunan;
        $rkpd_tahun_pembangunan->deskripsi = $request->rkpd_tahun_pembangunan_deskripsi;
        $rkpd_tahun_pembangunan->tahun = $request->rkpd_tahun_pembangunan_tahun;
        $rkpd_tahun_pembangunan->save();

        return response()->json(['success' => 'Berhasil']);
    }

    public function renja_tahun_pembangunan_edit($id)
    {
        $data = RkpdTahunPembangunan::find($id);

        return response()->json(['result' => $data]);
    }

    public function renja_tahun_pembangunan_detail($id)
    {
        $data = RkpdTahunPembangunan::find($id);

        return response()->json(['result' => $data]);
    }

    public function renja_tahun_pembangunan_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'rkpd_tahun_pembangunan_deskripsi' => 'required',
            'rkpd_tahun_pembangunan_tahun' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $rkpd_tahun_pembangunan = RkpdTahunPembangunan::find($request->rkpd_tahun_pembangunan_hidden_id);
        $rkpd_tahun_pembangunan->deskripsi = $request->rkpd_tahun_pembangunan_deskripsi;
        $rkpd_tahun_pembangunan->tahun = $request->rkpd_tahun_pembangunan_tahun;
        $rkpd_tahun_pembangunan->save();

        return response()->json(['success' => 'Berhasil']);
    }

    public function renja_tahun_pembangunan_destroy($id)
    {
        RkpdTahunPembangunan::find($id)->delete();

        return response()->json(['success' => 'Berhasil']);
    }

    public function renja_opd_tahun_pembangunan_store(Request $request)
    {
        $cek_tahun_pembangunan = RkpdTahunPembangunan::where('tahun', $request->rkpd_opd_tahun_pembangunan_tahun)->first();
        if($cek_tahun_pembangunan)
        {
            $opd_id = $request->rkpd_opd_tahun_pembangunan_opd_id;
            for ($i=0; $i < count($opd_id); $i++) {
                $cek_rkpd_opd_tahun_pembangunan = RkpdOpdTahunPembangunan::where('opd_id', $opd_id[$i])
                                                    ->where('rkpd_tahun_pembangunan_id', $cek_tahun_pembangunan->id)->first();
                if(!$cek_rkpd_opd_tahun_pembangunan)
                {
                    $rkpd_opd_tahun_pembangunan = new RkpdOpdTahunPembangunan;
                    $rkpd_opd_tahun_pembangunan->rkpd_tahun_pembangunan_id = $cek_tahun_pembangunan->id;
                    $rkpd_opd_tahun_pembangunan->opd_id = $opd_id[$i];
                    $rkpd_opd_tahun_pembangunan->save();
                }
            }
        }

        $opd_id = $request->rkpd_filter_opd;
        $tahun = $request->nav_rkpd_tahun;

        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun){
            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                $q->where('tahun', $tahun);
            });
        });
        if($opd_id)
        {
            $opds = $opds->where('id', $opd_id);
        }
        $opds = $opds->get();

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="16%">OPD</th>
                                    <th width="16%">Urusan</th>
                                    <th width="16%">Program</th>
                                    <th width="16%">Kegiatan</th>
                                    <th width="16%">Sub Kegiatan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';

                                    $get_urusans = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                $q->where('tahun', $tahun);
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

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
                                    $html .= '<td><ul>';
                                        foreach ($urusans as $urusan) {
                                            $html .= '<li>'.$urusan['kode'].'. '.$urusan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';


                                    $get_programs = Program::whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                    $q->where('tahun', $tahun);
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $programs = [];

                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
                                    $html .= '<td><ul>';
                                        foreach ($programs as $program) {
                                            $html .= '<li>'.$program['kode'].'. '.$program['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_kegiatans = Kegiatan::whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                        $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $kegiatans = [];

                                    foreach ($get_kegiatans as $get_kegiatan) {
                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                                    $html .= '<td><ul>';
                                        foreach ($kegiatans as $kegiatan) {
                                            $html .= '<li>'.$kegiatan['kode'].'.'.$kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_sub_kegiatans = SubKegiatan::whereHas('rkpd_tahun_pembangunan_sub_kegiatan', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                            $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $sub_kegiatans = [];

                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->latest()->first();
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
                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                            ];
                                        }
                                    }

                                    $html .= '<td><ul>';
                                        foreach ($sub_kegiatans as $sub_kegiatan) {
                                            $html .= '<li>'.$sub_kegiatan['kode'].'.'.$sub_kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';
                                    $html .= '<td>
                                                <a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'"
                                                    class="btn btn-icon btn-warning waves-effect waves-light mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-icon btn-danger waves-effect waves-light btn-hapus-rkpd-opd-tahun-pembangunan" data-opd-id="'.$opd->id.'" data-tahun="'.$tahun.'"><i class="fas fa-trash"></i></button>
                                            </td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['success' => 'Berhasil menambahkan OPD','html' => $html]);
    }

    public function data_per_opd($tahun)
    {
        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun){
                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                        $q->where('tahun', $tahun);
                    });
                })->get();
        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="16%">OPD</th>
                                    <th width="16%">Urusan</th>
                                    <th width="16%">Program</th>
                                    <th width="16%">Kegiatan</th>
                                    <th width="16%">Sub Kegiatan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';

                                    $get_urusans = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                $q->where('tahun', $tahun);
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

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
                                    $html .= '<td><ul>';
                                        foreach ($urusans as $urusan) {
                                            $html .= '<li>'.$urusan['kode'].'. '.$urusan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';


                                    $get_programs = Program::whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                    $q->where('tahun', $tahun);
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $programs = [];

                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
                                    $html .= '<td><ul>';
                                        foreach ($programs as $program) {
                                            $html .= '<li>'.$program['kode'].'. '.$program['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_kegiatans = Kegiatan::whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                        $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $kegiatans = [];

                                    foreach ($get_kegiatans as $get_kegiatan) {
                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                                    $html .= '<td><ul>';
                                        foreach ($kegiatans as $kegiatan) {
                                            $html .= '<li>'.$kegiatan['kode'].'.'.$kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_sub_kegiatans = SubKegiatan::whereHas('rkpd_tahun_pembangunan_sub_kegiatan', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                            $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $sub_kegiatans = [];

                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->latest()->first();
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
                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                            ];
                                        }
                                    }

                                    $html .= '<td><ul>';
                                        foreach ($sub_kegiatans as $sub_kegiatan) {
                                            $html .= '<li>'.$sub_kegiatan['kode'].'.'.$sub_kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';
                                    $html .= '<td>
                                                <a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'"
                                                    class="btn btn-icon btn-warning waves-effect waves-light mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-icon btn-danger waves-effect waves-light btn-hapus-rkpd-opd-tahun-pembangunan"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['html' => $html]);
    }

    public function get_opd($tahun)
    {
        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun) {
            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                $q->where('tahun', $tahun);
            });
        })->get();

        return response()->json($opds);
    }

    public function data_per_opd_atur($opd_id, $tahun)
    {
        $opd = MasterOpd::find($opd_id);

        $get_urusans = Urusan::all();
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

        $get_urusan_rkpds = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun){
            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun){
                $q->where('opd_id', $opd_id);
                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                });
            });
        })->get();

        $urusan_rkpds = [];

        foreach ($get_urusan_rkpds as $get_urusan_rkpd) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan_rkpd->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusan_rkpds[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi,
                ];
            } else {
                $urusan_rkpds[] = [
                    'id' => $get_urusan_rkpd->id,
                    'kode' => $get_urusan_rkpd->kode,
                    'deskripsi' => $get_urusan_rkpd->deskripsi,
                ];
            }
        }

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="80%">Deskripsi</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($urusan_rkpds as $urusan_rkpd) {
                                $html .= '<tr style="background: #bbbbbb;">';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['kode'].'</td>';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['deskripsi'].'</td>';
                                    $html .= '<td><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-program mr-1"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    type="button"
                                                    title="Tambah Program">
                                                        <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-program data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.'"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#rkpd_urusan'.$urusan_rkpd['id'].'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                                $html .= '</tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse accordion-body" id="rkpd_urusan'.$urusan_rkpd['id'].'">
                                                    <table class="table table-condensed table-striped">
                                                        <tbody>';
                                                            $get_program_rkpds = Program::whereHas('urusan', function($q) use ($opd, $tahun, $urusan_rkpd){
                                                                $q->where('id', $urusan_rkpd['id']);
                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd, $tahun, $urusan_rkpd) {
                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd, $tahun) {
                                                                        $q->where('opd_id', $opd->id);
                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        });
                                                                    });
                                                                });
                                                            })->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                                                    $q->where('opd_id', $opd->id);
                                                                });
                                                            })->whereHas('rkpd_tahun_pembangunan_program')->get();

                                                            $program_rkpds = [];

                                                            foreach ($get_program_rkpds as $get_program_rkpd) {
                                                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rkpd->id)->latest()->first();
                                                                if($cek_perubahan_program)
                                                                {
                                                                    $program_rkpds[] = [
                                                                        'id' => $cek_perubahan_program->program_id,
                                                                        'kode' => $cek_perubahan_program->kode,
                                                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                                                    ];
                                                                } else {
                                                                    $program_rkpds[] = [
                                                                        'id' => $get_program_rkpd->id,
                                                                        'kode' => $get_program_rkpd->kode,
                                                                        'deskripsi' => $get_program_rkpd->deskripsi
                                                                    ];
                                                                }
                                                            }
                                                            foreach ($program_rkpds as $program_rkpd) {
                                                                $html .= '<tr style="background: #c04141;">';
                                                                    $html .= '<td width="5%"class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'</td>';
                                                                    $html .= '<td width=80%" class="text-white">'.$program_rkpd['deskripsi'].'</td>';
                                                                    $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-kegiatan mr-1"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        type="button"
                                                                                        title="Tambah Kegiatan">
                                                                                            <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].'"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        value="close"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#rkpd_program'.$program_rkpd['id'].'"
                                                                                        class="accordion-toggle">
                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                    </button>
                                                                                </td>';
                                                                $html .= '</tr>
                                                                        <tr>
                                                                            <td colspan="3" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="rkpd_program'.$program_rkpd['id'].'">
                                                                                    <table class="table table-condensed table-striped">
                                                                                    <tbody>';
                                                                                        $get_kegiatan_rkpds = Kegiatan::whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd){
                                                                                            $q->where('id', $program_rkpd['id']);
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                });
                                                                                            });

                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                            $q->where('tahun', $tahun);
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        })->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->where('opd_id', $opd_id);
                                                                                            });
                                                                                        })->whereHas('rkpd_tahun_pembangunan_kegiatan')->get();

                                                                                        $kegiatan_rkpds = [];

                                                                                        foreach ($get_kegiatan_rkpds as $get_kegiatan_rkpd) {
                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan_rkpd->id)->latest()->first();
                                                                                            if($cek_perubahan_kegiatan)
                                                                                            {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                    'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                                                                                ];
                                                                                            } else {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $get_kegiatan_rkpd->id,
                                                                                                    'kode' => $get_kegiatan_rkpd->kode,
                                                                                                    'deskripsi' => $get_kegiatan_rkpd->deskripsi
                                                                                                ];
                                                                                            }
                                                                                        }
                                                                                        foreach ($kegiatan_rkpds as $kegiatan_rkpd) {
                                                                                            $html .= '<tr style="background: #41c0c0">';
                                                                                                $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'</td>';
                                                                                                $html .= '<td width=80%" class="text-white">'.$kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-sub-kegiatan mr-1"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                type="button"
                                                                                                                title="Tambah Sub Kegiatan">
                                                                                                                    <i class="fas fa-plus"></i>
                                                                                                            </button>
                                                                                                            <button type="button"
                                                                                                                class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-sub-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].' data-kegiatan-'.$kegiatan_rkpd['id'].'"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#rkpd_kegiatan'.$kegiatan_rkpd['id'].'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            $html .= '</tr>
                                                                                                    <tr>
                                                                                                        <td colspan="3" class="hiddenRow">
                                                                                                            <div class="collapse accordion-body" id="rkpd_kegiatan'.$kegiatan_rkpd['id'].'">
                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sub_kegiatan_rkpds = SubKegiatan::whereHas('kegiatan', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd, $kegiatan_rkpd) {
                                                                                                                        $q->where('kegiatan_id', $kegiatan_rkpd['id']);
                                                                                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id) {
                                                                                                                                $q->where('opd_id', $opd_id);
                                                                                                                            });
                                                                                                                        });

                                                                                                                        $q->whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd) {
                                                                                                                            $q->where('id', $program_rkpd['id']);

                                                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                                                });
                                                                                                                            });

                                                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                                                            $q->where('tahun', $tahun);
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    })->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->where('opd_id', $opd_id);
                                                                                                                        });
                                                                                                                    })->whereHas('rkpd_tahun_pembangunan_sub_kegiatan')->get();

                                                                                                                    $sub_kegiatan_rkpds = [];

                                                                                                                    foreach ($get_sub_kegiatan_rkpds as $get_sub_kegiatan_rkpd) {
                                                                                                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan_rkpd->id)->latest()->first();
                                                                                                                        if($cek_perubahan_sub_kegiatan)
                                                                                                                        {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $get_sub_kegiatan_rkpd->id,
                                                                                                                                'kode' => $get_sub_kegiatan_rkpd->kode,
                                                                                                                                'deskripsi' => $get_sub_kegiatan_rkpd->deskripsi
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sub_kegiatan_rkpds as $sub_kegiatan_rkpd) {
                                                                                                                        $html .= '<tr style="background:#41c081">';
                                                                                                                            $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'.'.$sub_kegiatan_rkpd['kode'].'</td>';
                                                                                                                            $html .= '<td width=80%" class="text-white">'.$sub_kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                                            $html .= '<td width=20%" class="text-white"></td>';
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
                </div>';

        return view('admin.perencanaan.rkpd.edit', [
            'opd' => $opd,
            'urusans' => $urusans,
            'tahun' => $tahun,
            'html' => $html
        ]);
    }

    public function data_per_opd_atur_urusan_store(Request $request)
    {
        $tahun = $request->rkpd_tahun_pembangunan_urusan_tahun;
        $opd_id = $request->rkpd_tahun_pembangunan_urusan_opd_id;

        $opd = MasterOpd::find($opd_id);

        $cek_rkpd_opd_tahun_pembangunan = RkpdOpdTahunPembangunan::where('opd_id', $opd_id)->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })->first();

        if($cek_rkpd_opd_tahun_pembangunan)
        {
            $urusan_id = $request->rkpd_tahun_pembangunan_urusan_urusan_id;
            for ($i=0; $i < count($urusan_id); $i++) {
                $cek_rkpd_tahun_pembangunan_urusan = RkpdTahunPembangunanUrusan::where('urusan_id', $urusan_id[$i])
                                                        ->where('rkpd_opd_tahun_pembangunan_id', $cek_rkpd_opd_tahun_pembangunan->id)->first();
                if(!$cek_rkpd_tahun_pembangunan_urusan)
                {
                    $rkpd_tahun_pembangunan_urusan = new RkpdTahunPembangunanUrusan;
                    $rkpd_tahun_pembangunan_urusan->rkpd_opd_tahun_pembangunan_id = $cek_rkpd_opd_tahun_pembangunan->id;
                    $rkpd_tahun_pembangunan_urusan->urusan_id = $urusan_id[$i];
                    $rkpd_tahun_pembangunan_urusan->save();
                }
            }
        }

        $opd = MasterOpd::find($opd_id);

        $get_urusans = Urusan::all();
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

        $get_urusan_rkpds = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun){
            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun){
                $q->where('opd_id', $opd_id);
                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                });
            });
        })->get();

        $urusan_rkpds = [];

        foreach ($get_urusan_rkpds as $get_urusan_rkpd) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan_rkpd->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusan_rkpds[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi,
                ];
            } else {
                $urusan_rkpds[] = [
                    'id' => $get_urusan_rkpd->id,
                    'kode' => $get_urusan_rkpd->kode,
                    'deskripsi' => $get_urusan_rkpd->deskripsi,
                ];
            }
        }

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="80%">Deskripsi</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($urusan_rkpds as $urusan_rkpd) {
                                $html .= '<tr style="background: #bbbbbb;">';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['kode'].'</td>';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['deskripsi'].'</td>';
                                    $html .= '<td><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-program mr-1"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    type="button"
                                                    title="Tambah Program">
                                                        <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-program data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.'"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#rkpd_urusan'.$urusan_rkpd['id'].'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                                $html .= '</tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse accordion-body" id="rkpd_urusan'.$urusan_rkpd['id'].'">
                                                    <table class="table table-condensed table-striped">
                                                        <tbody>';
                                                            $get_program_rkpds = Program::whereHas('urusan', function($q) use ($opd, $tahun, $urusan_rkpd){
                                                                $q->where('id', $urusan_rkpd['id']);
                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd, $tahun, $urusan_rkpd) {
                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd, $tahun) {
                                                                        $q->where('opd_id', $opd->id);
                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        });
                                                                    });
                                                                });
                                                            })->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                                                    $q->where('opd_id', $opd->id);
                                                                });
                                                            })->whereHas('rkpd_tahun_pembangunan_program')->get();

                                                            $program_rkpds = [];

                                                            foreach ($get_program_rkpds as $get_program_rkpd) {
                                                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rkpd->id)->latest()->first();
                                                                if($cek_perubahan_program)
                                                                {
                                                                    $program_rkpds[] = [
                                                                        'id' => $cek_perubahan_program->program_id,
                                                                        'kode' => $cek_perubahan_program->kode,
                                                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                                                    ];
                                                                } else {
                                                                    $program_rkpds[] = [
                                                                        'id' => $get_program_rkpd->id,
                                                                        'kode' => $get_program_rkpd->kode,
                                                                        'deskripsi' => $get_program_rkpd->deskripsi
                                                                    ];
                                                                }
                                                            }
                                                            foreach ($program_rkpds as $program_rkpd) {
                                                                $html .= '<tr style="background: #c04141;">';
                                                                    $html .= '<td width="5%"class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'</td>';
                                                                    $html .= '<td width=80%" class="text-white">'.$program_rkpd['deskripsi'].'</td>';
                                                                    $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-kegiatan mr-1"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        type="button"
                                                                                        title="Tambah Kegiatan">
                                                                                            <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].'"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        value="close"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#rkpd_program'.$program_rkpd['id'].'"
                                                                                        class="accordion-toggle">
                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                    </button>
                                                                                </td>';
                                                                $html .= '</tr>
                                                                        <tr>
                                                                            <td colspan="3" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="rkpd_program'.$program_rkpd['id'].'">
                                                                                    <table class="table table-condensed table-striped">
                                                                                    <tbody>';
                                                                                        $get_kegiatan_rkpds = Kegiatan::whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd){
                                                                                            $q->where('id', $program_rkpd['id']);
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                });
                                                                                            });

                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                            $q->where('tahun', $tahun);
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        })->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->where('opd_id', $opd_id);
                                                                                            });
                                                                                        })->whereHas('rkpd_tahun_pembangunan_kegiatan')->get();

                                                                                        $kegiatan_rkpds = [];

                                                                                        foreach ($get_kegiatan_rkpds as $get_kegiatan_rkpd) {
                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan_rkpd->id)->latest()->first();
                                                                                            if($cek_perubahan_kegiatan)
                                                                                            {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                    'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                                                                                ];
                                                                                            } else {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $get_kegiatan_rkpd->id,
                                                                                                    'kode' => $get_kegiatan_rkpd->kode,
                                                                                                    'deskripsi' => $get_kegiatan_rkpd->deskripsi
                                                                                                ];
                                                                                            }
                                                                                        }
                                                                                        foreach ($kegiatan_rkpds as $kegiatan_rkpd) {
                                                                                            $html .= '<tr style="background: #41c0c0">';
                                                                                                $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'</td>';
                                                                                                $html .= '<td width=80%" class="text-white">'.$kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-sub-kegiatan mr-1"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                type="button"
                                                                                                                title="Tambah Sub Kegiatan">
                                                                                                                    <i class="fas fa-plus"></i>
                                                                                                            </button>
                                                                                                            <button type="button"
                                                                                                                class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-sub-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].' data-kegiatan-'.$kegiatan_rkpd['id'].'"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#rkpd_kegiatan'.$kegiatan_rkpd['id'].'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            $html .= '</tr>
                                                                                                    <tr>
                                                                                                        <td colspan="3" class="hiddenRow">
                                                                                                            <div class="collapse accordion-body" id="rkpd_kegiatan'.$kegiatan_rkpd['id'].'">
                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sub_kegiatan_rkpds = SubKegiatan::whereHas('kegiatan', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd, $kegiatan_rkpd) {
                                                                                                                        $q->where('kegiatan_id', $kegiatan_rkpd['id']);
                                                                                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id) {
                                                                                                                                $q->where('opd_id', $opd_id);
                                                                                                                            });
                                                                                                                        });

                                                                                                                        $q->whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd) {
                                                                                                                            $q->where('id', $program_rkpd['id']);

                                                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                                                });
                                                                                                                            });

                                                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                                                            $q->where('tahun', $tahun);
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    })->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->where('opd_id', $opd_id);
                                                                                                                        });
                                                                                                                    })->whereHas('rkpd_tahun_pembangunan_sub_kegiatan')->get();

                                                                                                                    $sub_kegiatan_rkpds = [];

                                                                                                                    foreach ($get_sub_kegiatan_rkpds as $get_sub_kegiatan_rkpd) {
                                                                                                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan_rkpd->id)->latest()->first();
                                                                                                                        if($cek_perubahan_sub_kegiatan)
                                                                                                                        {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $get_sub_kegiatan_rkpd->id,
                                                                                                                                'kode' => $get_sub_kegiatan_rkpd->kode,
                                                                                                                                'deskripsi' => $get_sub_kegiatan_rkpd->deskripsi
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sub_kegiatan_rkpds as $sub_kegiatan_rkpd) {
                                                                                                                        $html .= '<tr style="background:#41c081">';
                                                                                                                            $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'.'.$sub_kegiatan_rkpd['kode'].'</td>';
                                                                                                                            $html .= '<td width=80%" class="text-white">'.$sub_kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                                            $html .= '<td width=20%" class="text-white"></td>';
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
                </div>';

        return response()->json(['success' => 'Berhasil mengatur urusan', 'html' => $html]);
    }

    public function get_program_rkpd($opd_id, $tahun, $urusan_id)
    {
        $get_program_rkpds = Program::whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_id){
            $q->where('id', $urusan_id);
            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_id) {
                $q->where('urusan_id', $urusan_id);
                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                    $q->where('opd_id', $opd_id);
                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                        $q->where('tahun', $tahun);
                    });
                });
            });
        })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                $q->where('opd_id', $opd_id);
            });
        })->get();

        $program_rkpds = [];

        foreach ($get_program_rkpds as $get_program_rkpd) {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rkpd->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program_rkpds[] = [
                    'id' => $cek_perubahan_program->program_id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $program_rkpds[] = [
                    'id' => $get_program_rkpd->id,
                    'deskripsi' => $get_program_rkpd->deskripsi
                ];
            }
        }

        return response()->json($program_rkpds);
    }

    public function data_per_opd_atur_program_store(Request $request)
    {
        $tahun = $request->rkpd_tahun_pembangunan_program_tahun;
        $opd_id = $request->rkpd_tahun_pembangunan_program_opd_id;
        $urusan_id = $request->rkpd_tahun_pembangunan_program_urusan_id;

        $opd = MasterOpd::find($opd_id);

        $cek_rkpd_tahun_pembangunan_urusan = RkpdTahunPembangunanUrusan::where('urusan_id', $urusan_id)
                                                ->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd_id){
                                                    $q->where('opd_id', $opd_id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                })->first();

        if($cek_rkpd_tahun_pembangunan_urusan)
        {
            $program_id = $request->rkpd_tahun_pembangunan_program_program_id;
            for ($i=0; $i < count($program_id); $i++) {
                $cek_rkpd_tahun_pembangunan_program = RkpdTahunPembangunanProgram::where('rkpd_tahun_pembangunan_urusan_id', $cek_rkpd_tahun_pembangunan_urusan->id)
                                                        ->where('program_id', $program_id[$i])->first();
                if(!$cek_rkpd_tahun_pembangunan_program)
                {
                    $rkpd_tahun_pembangunan_program = new RkpdTahunPembangunanProgram;
                    $rkpd_tahun_pembangunan_program->rkpd_tahun_pembangunan_urusan_id = $cek_rkpd_tahun_pembangunan_urusan->id;
                    $rkpd_tahun_pembangunan_program->program_id = $program_id[$i];
                    $rkpd_tahun_pembangunan_program->save();
                }
            }
        }

        $get_urusan_rkpds = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun){
            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun){
                $q->where('opd_id', $opd_id);
                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun) {
                    $q->where('tahun', $tahun);
                });
            });
        })->get();

        $urusan_rkpds = [];

        foreach ($get_urusan_rkpds as $get_urusan_rkpd) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan_rkpd->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusan_rkpds[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi,
                ];
            } else {
                $urusan_rkpds[] = [
                    'id' => $get_urusan_rkpd->id,
                    'kode' => $get_urusan_rkpd->kode,
                    'deskripsi' => $get_urusan_rkpd->deskripsi,
                ];
            }
        }

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">Kode</th>
                                    <th width="80%">Deskripsi</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach ($urusan_rkpds as $urusan_rkpd) {
                                $html .= '<tr style="background: #bbbbbb;">';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['kode'].'</td>';
                                    $html .= '<td class="text-white">'.$urusan_rkpd['deskripsi'].'</td>';
                                    $html .= '<td><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-program mr-1"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    type="button"
                                                    title="Tambah Program">
                                                        <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-program data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.'"
                                                    data-urusan-id="'.$urusan_rkpd['id'].'"
                                                    data-opd-id="'.$opd->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#rkpd_urusan'.$urusan_rkpd['id'].'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                                $html .= '</tr>
                                        <tr>
                                            <td colspan="3" class="hiddenRow">
                                                <div class="collapse accordion-body" id="rkpd_urusan'.$urusan_rkpd['id'].'">
                                                    <table class="table table-condensed table-striped">
                                                        <tbody>';
                                                            $get_program_rkpds = Program::whereHas('urusan', function($q) use ($opd, $tahun, $urusan_rkpd){
                                                                $q->where('id', $urusan_rkpd['id']);
                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd, $tahun, $urusan_rkpd) {
                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd, $tahun) {
                                                                        $q->where('opd_id', $opd->id);
                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                            $q->where('tahun', $tahun);
                                                                        });
                                                                    });
                                                                });
                                                            })->whereHas('program_indikator_kinerja', function($q) use ($opd){
                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd){
                                                                    $q->where('opd_id', $opd->id);
                                                                });
                                                            })->whereHas('rkpd_tahun_pembangunan_program')->get();

                                                            $program_rkpds = [];

                                                            foreach ($get_program_rkpds as $get_program_rkpd) {
                                                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rkpd->id)->latest()->first();
                                                                if($cek_perubahan_program)
                                                                {
                                                                    $program_rkpds[] = [
                                                                        'id' => $cek_perubahan_program->program_id,
                                                                        'kode' => $cek_perubahan_program->kode,
                                                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                                                    ];
                                                                } else {
                                                                    $program_rkpds[] = [
                                                                        'id' => $get_program_rkpd->id,
                                                                        'kode' => $get_program_rkpd->kode,
                                                                        'deskripsi' => $get_program_rkpd->deskripsi
                                                                    ];
                                                                }
                                                            }
                                                            foreach ($program_rkpds as $program_rkpd) {
                                                                $html .= '<tr style="background: #c04141;">';
                                                                    $html .= '<td width="5%"class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'</td>';
                                                                    $html .= '<td width=80%" class="text-white">'.$program_rkpd['deskripsi'].'</td>';
                                                                    $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-kegiatan mr-1"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        type="button"
                                                                                        title="Tambah Kegiatan">
                                                                                            <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].'"
                                                                                        data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                        data-opd-id="'.$opd->id.'"
                                                                                        data-tahun="'.$tahun.'"
                                                                                        data-program-id="'.$program_rkpd['id'].'"
                                                                                        value="close"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#rkpd_program'.$program_rkpd['id'].'"
                                                                                        class="accordion-toggle">
                                                                                            <i class="fas fa-chevron-right"></i>
                                                                                    </button>
                                                                                </td>';
                                                                $html .= '</tr>
                                                                        <tr>
                                                                            <td colspan="3" class="hiddenRow">
                                                                                <div class="collapse accordion-body" id="rkpd_program'.$program_rkpd['id'].'">
                                                                                    <table class="table table-condensed table-striped">
                                                                                    <tbody>';
                                                                                        $get_kegiatan_rkpds = Kegiatan::whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd){
                                                                                            $q->where('id', $program_rkpd['id']);
                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                });
                                                                                            });

                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                            $q->where('tahun', $tahun);
                                                                                                        });
                                                                                                    });
                                                                                                });
                                                                                            });
                                                                                        })->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                $q->where('opd_id', $opd_id);
                                                                                            });
                                                                                        })->whereHas('rkpd_tahun_pembangunan_kegiatan')->get();

                                                                                        $kegiatan_rkpds = [];

                                                                                        foreach ($get_kegiatan_rkpds as $get_kegiatan_rkpd) {
                                                                                            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan_rkpd->id)->latest()->first();
                                                                                            if($cek_perubahan_kegiatan)
                                                                                            {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                    'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                                                                                                ];
                                                                                            } else {
                                                                                                $kegiatan_rkpds[] = [
                                                                                                    'id' => $get_kegiatan_rkpd->id,
                                                                                                    'kode' => $get_kegiatan_rkpd->kode,
                                                                                                    'deskripsi' => $get_kegiatan_rkpd->deskripsi
                                                                                                ];
                                                                                            }
                                                                                        }
                                                                                        foreach ($kegiatan_rkpds as $kegiatan_rkpd) {
                                                                                            $html .= '<tr style="background: #41c0c0">';
                                                                                                $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'</td>';
                                                                                                $html .= '<td width=80%" class="text-white">'.$kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                $html .= '<td width="15%"><button class="btn btn-primary btn-icon waves-effect waves-light button-add-rkpd-tahun-pembangunan-sub-kegiatan mr-1"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                type="button"
                                                                                                                title="Tambah Sub Kegiatan">
                                                                                                                    <i class="fas fa-plus"></i>
                                                                                                            </button>
                                                                                                            <button type="button"
                                                                                                                class="btn btn-icon btn-success waves-effect waves-light btn-open-rkpd-tahun-pembangunan-sub-kegiatan data-urusan-'.$urusan_rkpd['id'].' data-opd-'.$opd->id.' data-tahun-'.$tahun.' data-program-'.$program_rkpd['id'].' data-kegiatan-'.$kegiatan_rkpd['id'].'"
                                                                                                                data-urusan-id="'.$urusan_rkpd['id'].'"
                                                                                                                data-opd-id="'.$opd->id.'"
                                                                                                                data-tahun="'.$tahun.'"
                                                                                                                data-program-id="'.$program_rkpd['id'].'"
                                                                                                                data-kegiatan-id="'.$kegiatan_rkpd['id'].'"
                                                                                                                value="close"
                                                                                                                data-bs-toggle="collapse"
                                                                                                                data-bs-target="#rkpd_kegiatan'.$kegiatan_rkpd['id'].'"
                                                                                                                class="accordion-toggle">
                                                                                                                    <i class="fas fa-chevron-right"></i>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            $html .= '</tr>
                                                                                                    <tr>
                                                                                                        <td colspan="3" class="hiddenRow">
                                                                                                            <div class="collapse accordion-body" id="rkpd_kegiatan'.$kegiatan_rkpd['id'].'">
                                                                                                                <table class="table table-condensed table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sub_kegiatan_rkpds = SubKegiatan::whereHas('kegiatan', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd, $kegiatan_rkpd) {
                                                                                                                        $q->where('kegiatan_id', $kegiatan_rkpd['id']);
                                                                                                                        $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id) {
                                                                                                                                $q->where('opd_id', $opd_id);
                                                                                                                            });
                                                                                                                        });

                                                                                                                        $q->whereHas('program', function($q) use ($opd_id, $tahun, $urusan_rkpd, $program_rkpd) {
                                                                                                                            $q->where('id', $program_rkpd['id']);

                                                                                                                            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                                    $q->where('opd_id', $opd_id);
                                                                                                                                });
                                                                                                                            });

                                                                                                                            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd){
                                                                                                                                $q->where('id', $urusan_rkpd['id']);
                                                                                                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_rkpd) {
                                                                                                                                    $q->where('urusan_id', $urusan_rkpd['id']);
                                                                                                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                                                                                                                                        $q->where('opd_id', $opd_id);
                                                                                                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                                                                                            $q->where('tahun', $tahun);
                                                                                                                                        });
                                                                                                                                    });
                                                                                                                                });
                                                                                                                            });
                                                                                                                        });
                                                                                                                    })->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                                                                                            $q->where('opd_id', $opd_id);
                                                                                                                        });
                                                                                                                    })->whereHas('rkpd_tahun_pembangunan_sub_kegiatan')->get();

                                                                                                                    $sub_kegiatan_rkpds = [];

                                                                                                                    foreach ($get_sub_kegiatan_rkpds as $get_sub_kegiatan_rkpd) {
                                                                                                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan_rkpd->id)->latest()->first();
                                                                                                                        if($cek_perubahan_sub_kegiatan)
                                                                                                                        {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                                                                                                                                'kode' => $cek_perubahan_sub_kegiatan->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sub_kegiatan_rkpds[] = [
                                                                                                                                'id' => $get_sub_kegiatan_rkpd->id,
                                                                                                                                'kode' => $get_sub_kegiatan_rkpd->kode,
                                                                                                                                'deskripsi' => $get_sub_kegiatan_rkpd->deskripsi
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sub_kegiatan_rkpds as $sub_kegiatan_rkpd) {
                                                                                                                        $html .= '<tr style="background:#41c081">';
                                                                                                                            $html .= '<td width="5%" class="text-white">'.$urusan_rkpd['kode'].'.'.$program_rkpd['kode'].'.'.$kegiatan_rkpd['kode'].'.'.$sub_kegiatan_rkpd['kode'].'</td>';
                                                                                                                            $html .= '<td width=80%" class="text-white">'.$sub_kegiatan_rkpd['deskripsi'].'</td>';
                                                                                                                            $html .= '<td width=20%" class="text-white"></td>';
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
                </div>';

        return response()->json(['success' => 'Berhasil mengatur Program', 'html' => $html]);
    }

    public function get_kegiatan_rkpd($opd_id, $tahun, $urusan_id, $program_id)
    {
        $get_kegiatan_rkpds = Kegiatan::whereHas('program', function($q) use ($opd_id, $tahun, $urusan_id, $program_id){
            $q->where('id', $program_id);
            $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            });

            $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_id){
                $q->where('id', $urusan_id);
                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_id) {
                    $q->where('urusan_id', $urusan_id);
                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                        $q->where('opd_id', $opd_id);
                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                            $q->where('tahun', $tahun);
                        });
                    });
                });
            });
        })->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                $q->where('opd_id', $opd_id);
            });
        })->get();

        $kegiatan_rkpds = [];

        foreach ($get_kegiatan_rkpds as $get_kegiatan_rkpd) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan_rkpd->id)->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan_rkpds[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                ];
            } else {
                $kegiatan_rkpds[] = [
                    'id' => $get_kegiatan_rkpd->id,
                    'deskripsi' => $get_kegiatan_rkpd->deskripsi
                ];
            }
        }

        return response()->json($kegiatan_rkpds);
    }

    public function data_per_opd_atur_kegiatan_store(Request $request)
    {
        $tahun = $request->rkpd_tahun_pembangunan_kegiatan_tahun;
        $opd_id = $request->rkpd_tahun_pembangunan_kegiatan_opd_id;
        $urusan_id = $request->rkpd_tahun_pembangunan_kegiatan_urusan_id;
        $program_id =$request->rkpd_tahun_pembangunan_kegiatan_program_id;

        $opd = MasterOpd::find($opd_id);

        $cek_rkpd_tahun_pembangunan_program = RkpdTahunPembangunanProgram::where('program_id', $program_id)
                                                ->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd_id, $urusan_id){
                                                    $q->where('urusan_id', $urusan_id);
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd_id){
                                                        $q->where('opd_id', $opd_id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                })->first();
        if($cek_rkpd_tahun_pembangunan_program)
        {
            $kegiatan_id = $request->rkpd_tahun_pembangunan_kegiatan_kegiatan_id;
            for ($i=0; $i < count($kegiatan_id); $i++) {
                $cek_rkpd_tahun_pembangunan_kegiatan = RkpdTahunPembangunanKegiatan::where('rkpd_tahun_pembangunan_program_id', $cek_rkpd_tahun_pembangunan_program->id)
                                                        ->where('kegiatan_id', $kegiatan_id[$i])->first();
                if(!$cek_rkpd_tahun_pembangunan_kegiatan)
                {
                    $rkpd_tahun_pembangunan_kegiatan = new RkpdTahunPembangunanKegiatan;
                    $rkpd_tahun_pembangunan_kegiatan->rkpd_tahun_pembangunan_program_id = $cek_rkpd_tahun_pembangunan_program->id;
                    $rkpd_tahun_pembangunan_kegiatan->kegiatan_id = $kegiatan_id[$i];
                    $rkpd_tahun_pembangunan_kegiatan->save();
                }
            }
        }

        Alert::success('Berhasil', 'Berhasil Mengatur Kegiatan untuk OPD '.$opd->nama);
        return redirect()->route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur', ['opd_id' => $opd_id, 'tahun' => $tahun]);
    }

    public function get_sub_kegiatan_rkpd($opd_id, $tahun, $urusan_id, $program_id, $kegiatan_id)
    {
        $get_sub_kegiatan_rkpds = SubKegiatan::whereHas('kegiatan', function($q) use ($opd_id, $tahun, $urusan_id, $program_id, $kegiatan_id) {
            $q->where('kegiatan_id', $kegiatan_id);
            $q->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id) {
                    $q->where('opd_id', $opd_id);
                });
            });

            $q->whereHas('program', function($q) use ($opd_id, $tahun, $urusan_id, $program_id) {
                $q->where('id', $program_id);

                $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                    $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                        $q->where('opd_id', $opd_id);
                    });
                });

                $q->whereHas('urusan', function($q) use ($opd_id, $tahun, $urusan_id){
                    $q->where('id', $urusan_id);
                    $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($opd_id, $tahun, $urusan_id) {
                        $q->where('urusan_id', $urusan_id);
                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($opd_id, $tahun) {
                            $q->where('opd_id', $opd_id);
                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                $q->where('tahun', $tahun);
                            });
                        });
                    });
                });
            });
        })->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                $q->where('opd_id', $opd_id);
            });
        })->get();

        $sub_kegiatan_rkpds = [];

        foreach ($get_sub_kegiatan_rkpds as $get_sub_kegiatan_rkpd) {
            $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan_rkpd->id)->latest()->first();
            if($cek_perubahan_sub_kegiatan)
            {
                $sub_kegiatan_rkpds[] = [
                    'id' => $cek_perubahan_sub_kegiatan->sub_kegiatan_id,
                    'deskripsi' => $cek_perubahan_sub_kegiatan->deskripsi
                ];
            } else {
                $sub_kegiatan_rkpds[] = [
                    'id' => $get_sub_kegiatan_rkpd->id,
                    'deskripsi' => $get_sub_kegiatan_rkpd->deskripsi
                ];
            }
        }

        return response()->json($sub_kegiatan_rkpds);
    }

    public function data_per_opd_atur_sub_kegiatan_store(Request $request)
    {
        $tahun = $request->rkpd_tahun_pembangunan_sub_kegiatan_tahun;
        $opd_id = $request->rkpd_tahun_pembangunan_sub_kegiatan_opd_id;
        $urusan_id = $request->rkpd_tahun_pembangunan_sub_kegiatan_urusan_id;
        $program_id = $request->rkpd_tahun_pembangunan_sub_kegiatan_program_id;
        $kegiatan_id = $request->rkpd_tahun_pembangunan_sub_kegiatan_kegiatan_id;

        $opd = MasterOpd::find($opd_id);

        $cek_rkpd_tahun_pembangunan_kegiatan = RkpdTahunPembangunanKegiatan::where('kegiatan_id', $kegiatan_id)
                                                ->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($tahun, $opd_id, $urusan_id, $program_id){
                                                    $q->where('program_id', $program_id);
                                                    $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd_id, $urusan_id){
                                                        $q->where('urusan_id', $urusan_id);
                                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd_id){
                                                            $q->where('opd_id', $opd_id);
                                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                                $q->where('tahun', $tahun);
                                                            });
                                                        });
                                                    });
                                                })->first();
        if($cek_rkpd_tahun_pembangunan_kegiatan)
        {
            $sub_kegiatan_id = $request->rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id;
            for ($i=0; $i < count($sub_kegiatan_id); $i++) {
                $cek_rkpd_tahun_pembangunan_sub_kegiatan = RkpdTahunPembangunanSubKegiatan::where('sub_kegiatan_id', $sub_kegiatan_id[$i])
                                                            ->where('rkpd_tahun_pembangunan_kegiatan_id', $cek_rkpd_tahun_pembangunan_kegiatan->id)->first();
                if(!$cek_rkpd_tahun_pembangunan_sub_kegiatan)
                {
                    $rkpd_tahun_pembangunan_sub_kegiatan = new RkpdTahunPembangunanSubKegiatan;
                    $rkpd_tahun_pembangunan_sub_kegiatan->rkpd_tahun_pembangunan_kegiatan_id = $cek_rkpd_tahun_pembangunan_kegiatan->id;
                    $rkpd_tahun_pembangunan_sub_kegiatan->sub_kegiatan_id = $sub_kegiatan_id[$i];
                    $rkpd_tahun_pembangunan_sub_kegiatan->save();
                }
            }
        }

        Alert::success('Berhasil', 'Berhasil Mengatur Kegiatan untuk OPD '.$opd->nama);
        return redirect()->route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur', ['opd_id' => $opd_id, 'tahun' => $tahun]);
    }

    public function data_per_opd_filter(Request $request)
    {
        $opd_id = $request->opd_id;
        $tahun = $request->tahun;

        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun){
            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                $q->where('tahun', $tahun);
            });
        });
        if($opd_id)
        {
            $opds = $opds->where('id', $opd_id);
        }
        $opds = $opds->get();

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="16%">OPD</th>
                                    <th width="16%">Urusan</th>
                                    <th width="16%">Program</th>
                                    <th width="16%">Kegiatan</th>
                                    <th width="16%">Sub Kegiatan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';

                                    $get_urusans = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                $q->where('tahun', $tahun);
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

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
                                    $html .= '<td><ul>';
                                        foreach ($urusans as $urusan) {
                                            $html .= '<li>'.$urusan['kode'].'. '.$urusan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';


                                    $get_programs = Program::whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                    $q->where('tahun', $tahun);
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $programs = [];

                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
                                    $html .= '<td><ul>';
                                        foreach ($programs as $program) {
                                            $html .= '<li>'.$program['kode'].'. '.$program['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_kegiatans = Kegiatan::whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                        $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $kegiatans = [];

                                    foreach ($get_kegiatans as $get_kegiatan) {
                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                                    $html .= '<td><ul>';
                                        foreach ($kegiatans as $kegiatan) {
                                            $html .= '<li>'.$kegiatan['kode'].'.'.$kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_sub_kegiatans = SubKegiatan::whereHas('rkpd_tahun_pembangunan_sub_kegiatan', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                            $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $sub_kegiatans = [];

                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->latest()->first();
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
                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                            ];
                                        }
                                    }

                                    $html .= '<td><ul>';
                                        foreach ($sub_kegiatans as $sub_kegiatan) {
                                            $html .= '<li>'.$sub_kegiatan['kode'].'.'.$sub_kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';
                                    $html .= '<td>
                                                <a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'"
                                                    class="btn btn-icon btn-warning waves-effect waves-light mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-icon btn-danger waves-effect waves-light btn-hapus-rkpd-opd-tahun-pembangunan" data-opd-id="'.$opd->id.'" data-tahun="'.$tahun.'"><i class="fas fa-trash"></i></button>
                                            </td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['html' => $html]);
    }

    public function data_per_opd_reset(Request $request)
    {
        $tahun = $request->tahun;

        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun){
            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                $q->where('tahun', $tahun);
            });
        })->get();

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="16%">OPD</th>
                                    <th width="16%">Urusan</th>
                                    <th width="16%">Program</th>
                                    <th width="16%">Kegiatan</th>
                                    <th width="16%">Sub Kegiatan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';

                                    $get_urusans = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                $q->where('tahun', $tahun);
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

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
                                    $html .= '<td><ul>';
                                        foreach ($urusans as $urusan) {
                                            $html .= '<li>'.$urusan['kode'].'. '.$urusan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';


                                    $get_programs = Program::whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                    $q->where('tahun', $tahun);
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $programs = [];

                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
                                    $html .= '<td><ul>';
                                        foreach ($programs as $program) {
                                            $html .= '<li>'.$program['kode'].'. '.$program['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_kegiatans = Kegiatan::whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                        $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $kegiatans = [];

                                    foreach ($get_kegiatans as $get_kegiatan) {
                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                                    $html .= '<td><ul>';
                                        foreach ($kegiatans as $kegiatan) {
                                            $html .= '<li>'.$kegiatan['kode'].'.'.$kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_sub_kegiatans = SubKegiatan::whereHas('rkpd_tahun_pembangunan_sub_kegiatan', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                            $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $sub_kegiatans = [];

                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->latest()->first();
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
                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                            ];
                                        }
                                    }

                                    $html .= '<td><ul>';
                                        foreach ($sub_kegiatans as $sub_kegiatan) {
                                            $html .= '<li>'.$sub_kegiatan['kode'].'.'.$sub_kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';
                                    $html .= '<td>
                                                    <a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'"
                                                        class="btn btn-icon btn-warning waves-effect waves-light mr-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-icon btn-danger waves-effect waves-light btn-hapus-rkpd-opd-tahun-pembangunan"
                                                        data-opd-id="'.$opd->id.'"
                                                        data-tahun="'.$tahun.'">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['html' => $html]);
    }

    public function data_per_opd_destroy(Request $request)
    {
        $opd_id = $request->opd_id;
        $tahun = $request->tahun;

        $rkpd_opd_tahun_pembangunan = RkpdOpdTahunPembangunan::where('opd_id', $opd_id)->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
            $q->where('tahun', $tahun);
        })->first();

        $rkpd_tahun_pembangunan_urusans = RkpdTahunPembangunanUrusan::where('rkpd_opd_tahun_pembangunan_id', $rkpd_opd_tahun_pembangunan->id)->get();

        foreach ($rkpd_tahun_pembangunan_urusans as $rkpd_tahun_pembangunan_urusan) {
            $rkpd_tahun_pembangunan_programs = RkpdTahunPembangunanProgram::where('rkpd_tahun_pembangunan_urusan_id', $rkpd_tahun_pembangunan_urusan->id)->get();
            foreach ($rkpd_tahun_pembangunan_programs as $rkpd_tahun_pembangunan_program) {
                $rkpd_tahun_pembangunan_kegiatans = RkpdTahunPembangunanKegiatan::where('rkpd_tahun_pembangunan_program_id', $rkpd_tahun_pembangunan_program->id)->get();
                foreach ($rkpd_tahun_pembangunan_kegiatans as $rkpd_tahun_pembangunan_kegiatan) {
                    $rkpd_tahun_pembangunan_sub_kegiatans = RkpdTahunPembangunanSubKegiatan::where('rkpd_tahun_pembangunan_kegiatan_id', $rkpd_tahun_pembangunan_kegiatan->id)->get();
                    foreach ($rkpd_tahun_pembangunan_sub_kegiatans as $rkpd_tahun_pembangunan_sub_kegiatan) {
                        RkpdTahunPembangunanSubKegiatan::find($rkpd_tahun_pembangunan_sub_kegiatan->id)->delete();
                    }

                    RkpdTahunPembangunanKegiatan::find($rkpd_tahun_pembangunan_kegiatan->id)->delete();
                }

                RkpdTahunPembangunanProgram::find($rkpd_tahun_pembangunan_program->id)->delete();
            }

            RkpdTahunPembangunanUrusan::find($rkpd_tahun_pembangunan_urusan->delete());
        }

        RkpdOpdTahunPembangunan::find($rkpd_opd_tahun_pembangunan->id)->delete();

        $opd_id = $request->rkpd_filter_opd;
        $tahun = $request->nav_rkpd_tahun;

        $opds = MasterOpd::whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun){
            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                $q->where('tahun', $tahun);
            });
        });
        if($opd_id)
        {
            $opds = $opds->where('id', $opd_id);
        }
        $opds = $opds->get();

        $html = '<div class="data-table-rows slim">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="16%">OPD</th>
                                    <th width="16%">Urusan</th>
                                    <th width="16%">Program</th>
                                    <th width="16%">Kegiatan</th>
                                    <th width="16%">Sub Kegiatan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';

                                    $get_urusans = Urusan::whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                        $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                            $q->where('opd_id', $opd->id);
                                            $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                $q->where('tahun', $tahun);
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

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
                                    $html .= '<td><ul>';
                                        foreach ($urusans as $urusan) {
                                            $html .= '<li>'.$urusan['kode'].'. '.$urusan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';


                                    $get_programs = Program::whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                            $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                $q->where('opd_id', $opd->id);
                                                $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                    $q->where('tahun', $tahun);
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $programs = [];

                                    foreach ($get_programs as $get_program) {
                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
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
                                    $html .= '<td><ul>';
                                        foreach ($programs as $program) {
                                            $html .= '<li>'.$program['kode'].'. '.$program['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_kegiatans = Kegiatan::whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                        $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                            $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                    $q->where('opd_id', $opd->id);
                                                    $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                        $q->where('tahun', $tahun);
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $kegiatans = [];

                                    foreach ($get_kegiatans as $get_kegiatan) {
                                        $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
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

                                    $html .= '<td><ul>';
                                        foreach ($kegiatans as $kegiatan) {
                                            $html .= '<li>'.$kegiatan['kode'].'.'.$kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';

                                    $get_sub_kegiatans = SubKegiatan::whereHas('rkpd_tahun_pembangunan_sub_kegiatan', function($q) use ($opd, $tahun){
                                        $q->whereHas('rkpd_tahun_pembangunan_kegiatan', function($q) use ($opd, $tahun) {
                                            $q->whereHas('rkpd_tahun_pembangunan_program', function($q) use ($opd, $tahun){
                                                $q->whereHas('rkpd_tahun_pembangunan_urusan', function($q) use ($tahun, $opd){
                                                    $q->whereHas('rkpd_opd_tahun_pembangunan', function($q) use ($tahun, $opd){
                                                        $q->where('opd_id', $opd->id);
                                                        $q->whereHas('rkpd_tahun_pembangunan', function($q) use ($tahun){
                                                            $q->where('tahun', $tahun);
                                                        });
                                                    });
                                                });
                                            });
                                        });
                                    })->orderBy('kode', 'asc')->get();

                                    $sub_kegiatans = [];

                                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                                        $cek_perubahan_sub_kegiatan = PivotPerubahanSubKegiatan::where('sub_kegiatan_id', $get_sub_kegiatan->id)->latest()->first();
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
                                                'deskripsi' => $get_sub_kegiatan->deskripsi,
                                            ];
                                        }
                                    }

                                    $html .= '<td><ul>';
                                        foreach ($sub_kegiatans as $sub_kegiatan) {
                                            $html .= '<li>'.$sub_kegiatan['kode'].'.'.$sub_kegiatan['deskripsi'].'</li>';
                                        }
                                    $html .= '</ul></td>';
                                    $html .= '<td>
                                                <a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'"
                                                    class="btn btn-icon btn-warning waves-effect waves-light mr-1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-icon btn-danger waves-effect waves-light btn-hapus-rkpd-opd-tahun-pembangunan" data-opd-id="'.$opd->id.'" data-tahun="'.$tahun.'"><i class="fas fa-trash"></i></button>
                                            </td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['success' => 'Berhasil menghapus opd pada tema pembangunan tahun '.$tahun,'html' => $html]);
    }
}
