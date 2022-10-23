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
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;

class VisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            $data = Visi::latest()->get();
            $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="visi_detail" id="'.$data->id.'" class="visi_detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="visi_edit" id="'.$data->id.'"
                    class="visi_edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('deskripsi', function($data) use ($tahun_sekarang){
                    $cek_perubahan = PivotPerubahanVisi::where('visi_id',$data->id)->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
                // ->editColumn('tahun_perubahan', function($data) use ($tahun_sekarang){
                //     $cek_perubahan = PivotPerubahanVisi::where('visi_id', $data->id)->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                //     if($cek_perubahan)
                //     {
                //         return $cek_perubahan->tahun_perubahan;
                //     } else {
                //         return $data->tahun_perubahan;
                //     }
                // })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        return view('admin.visi.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'visi_deskripsi' => 'required',
            'visi_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        // $cek_visi = Visi::where('tahun_perubahan', $request->visi_tahun_perubahan)->first();
        // if($cek_visi)
        // {
        //     $pivot = new PivotPerubahanVisi;
        //     $pivot->visi_id = $cek_visi->id;
        //     $pivot->deskripsi = $request->visi_deskripsi;
        //     $pivot->tahun_perubahan = $request->visi_tahun_perubahan;
        //     $pivot->kabupaten_id = 62;
        //     $pivot->save();
        // } else {

        // }
        $visi = new Visi;
        $visi->deskripsi = $request->visi_deskripsi;
        $visi->tahun_perubahan = $request->visi_tahun_perubahan;
        $visi->kabupaten_id = 62;
        $visi->save();

        return response()->json(['success' => 'Berhasil Menambahkan Visi']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Visi::find($id);
        $cek_perubahan = PivotPerubahanVisi::where('visi_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $deskripsi = $cek_perubahan->deskripsi;
            $tahun_perubahan = $cek_perubahan->tahun_perubahan;
            $get_perubahans = PivotPerubahanVisi::where('visi_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li>'.$data->deskripsi.', Tahun Perubahan: '.$data->tahun_perubahan.' (Sebelum Perubahan)</li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $html .= '<li>'.$get_perubahan->deskripsi.', Tahun Perubahan'.$get_perubahan->tahun_perubahan.' (Perubahan '.$a++.'), '.$get_perubahan->created_at.'</li>';
            }
            $html .= '</ul>';
        } else {
            $deskripsi = $data->deskripsi;
            $tahun_perubahan = $data->tahun_perubahan;
            $html .= '<p>Tidak ada</p>';
        }

        $html .='</div>';
        $array = [
            'deskripsi' => $deskripsi,
            'tahun_perubahan' => $tahun_perubahan,
            'pivot_perubahan_visi' => $html
        ];

        return response()->json(['result' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $id)->latest()->first();
        if($cek_perubahan_visi)
        {
            $array = [
                'deskripsi' => $cek_perubahan_visi->deskripsi,
                'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
            ];
        } else {
            $data = Visi::find($id);

            $array = [
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan,
            ];
        }

        return response()->json(['result' => $array]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'visi_deskripsi' => 'required',
            'visi_tahun_perubahan' => 'required',
            'visi_hidden_id' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_visi = new PivotPerubahanVisi;
        $pivot_perubahan_visi->visi_id = $request->visi_hidden_id;
        $pivot_perubahan_visi->deskripsi = $request->visi_deskripsi;
        $pivot_perubahan_visi->tahun_perubahan = $request->visi_tahun_perubahan;
        $pivot_perubahan_visi->kabupaten_id = 62;
        $pivot_perubahan_visi->save();

        return response()->json(['success' => 'Berhasil Merubah Visi']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
