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

        Alert::success('Berhasil', 'Berhasil Mengatur OPD untuk tahun pembangunan '.$request->rkpd_opd_tahun_pembangunan_tahun);
        return redirect()->route('admin.perencanaan.index');
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
                                    <th>No</th>
                                    <th>OPD</th>
                                    <th>Urusan</th>
                                    <th>Program</th>
                                    <th>Kegiatan</th>
                                    <th>Sub Kegiatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>';
                            $a = 1;
                            foreach ($opds as $opd) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$a++.'</td>';
                                    $html .= '<td>'.$opd->nama.'</td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td><a href="/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/'.$opd->id.'/'.$tahun.'" class="btn btn-icon btn-warning waves-effect waves-light"><i class="fas fa-edit"></i></a></td>';
                                $html .= '</tr>';
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['html' => $html]);
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

        return view('admin.perencanaan.rkpd.edit', [
            'opd' => $opd,
            'urusans' => $urusans,
            'tahun' => $tahun
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

        Alert::success('Berhasil', 'Berhasil Mengatur Urusan untuk OPD '.$opd->nama);
        return redirect()->route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur', ['opd_id' => $opd_id, 'tahun' => $tahun]);
    }
}
