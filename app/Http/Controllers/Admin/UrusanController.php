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
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Imports\UrusanImport;
use App\Models\TahunPeriode;

class UrusanController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = Urusan::orderBy('id', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        return $cek_perubahan_urusan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanUrusan::where('urusan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                ->editColumn('tahun_perubahan', function($data){
                    $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->latest()->first();
                    if($cek_perubahan_urusan)
                    {
                        return $cek_perubahan_urusan->tahun_perubahan;
                    } else {
                        return $data->tahun_perubahan;
                    }
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        return view('admin.urusan.index', [
            'tahuns' => $tahuns
        ]);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kode' => 'required',
            'deskripsi' => 'required',
            'tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $urusan = new Urusan;
        $urusan->kode = $request->kode;
        $urusan->deskripsi = $request->deskripsi;
        $urusan->tahun_perubahan = $request->tahun_perubahan;
        $urusan->kabupaten_id = 62;
        $urusan->save();

        return response()->json(['success' => 'Berhasil Menambahkan Urusan']);
    }

    public function show($id)
    {
        $data = Urusan::find($id);

        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $get_perubahans = PivotPerubahanUrusan::where('urusan_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li>'.$data->deskripsi.', Tahun Perubahan '.$data->tahun_perubahan.' (Sebelum Perubahan)</li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $html .= '<li>'.$get_perubahan->deskripsi.', Tahun Perubahan '.$get_perubahan->tahun_perubahan.' (Perubahan '.$a++.'), '.$get_perubahan->tanggal.'</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>Tidak ada</p>';
        }

        $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->latest()->first();
        if($cek_perubahan_urusan)
        {
            $tahun_perubahan = $cek_perubahan_urusan->tahun_perubahan;
        } else {
            $tahun_perubahan = $data->tahun_perubahan;
        }

        $html .='</div>';

        $array = [
            'kode' => $data->kode,
            'deskripsi' => $data->deskripsi,
            'tahun_perubahan' => $tahun_perubahan,
            'pivot_perubahan_urusan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    public function edit($id)
    {
        $data = Urusan::find($id);
        $cek_perubahan = PivotPerubahanUrusan::where('urusan_id', $id)->latest()->first();
        if($cek_perubahan)
        {
            $deskripsi = $cek_perubahan->deskripsi;
        } else {
            $deskripsi = $data->deskripsi;
        }

        $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $data->id)->latest()->first();
        if($cek_perubahan_urusan)
        {
            $tahun_perubahan = $cek_perubahan_urusan->tahun_perubahan;
        } else {
            $tahun_perubahan = $data->tahun_perubahan;
        }

        $array = [
            'id' => $data->id,
            'kode' => $data->kode,
            'deskripsi' => $deskripsi,
            'tahun_perubahan' => $tahun_perubahan
        ];

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kode' => 'required',
            'deskripsi' => 'required',
            'tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot = new PivotPerubahanUrusan;
        $pivot->urusan_id = $request->hidden_id;
        $pivot->kode = $request->kode;
        $pivot->deskripsi = $request->deskripsi;
        $pivot->tahun_perubahan = $request->tahun_perubahan;
        $pivot->tanggal = Carbon::now();
        $pivot->save();

        return response()->json(['success' => 'Berhasil Merubah Data']);
    }

    public function impor(Request $request)
    {
        $file = $request->file('impor_urusan');
        Excel::import(new UrusanImport, $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }
}
