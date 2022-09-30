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
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanVisi::where('visi_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data->deskripsi;
                    }
                })
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
            'deskripsi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $visi = new Visi;
        $visi->deskripsi = $request->deskripsi;
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
            $get_perubahans = PivotPerubahanVisi::where('visi_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li>'.$data->deskripsi.' (Sebelum Perubahan)</li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $html .= '<li>'.$get_perubahan->deskripsi.' (Perubahan '.$a++.'), '.$get_perubahan->created_at.'</li>';
            }
            $html .= '</ul>';
        } else {
            $deskripsi = $data->deskripsi;
            $html .= '<p>Tidak ada</p>';
        }

        $html .='</div>';
        $array = [
            'deskripsi' => $deskripsi,
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
                'deskripsi' => $cek_perubahan_visi->deskripsi
            ];
        } else {
            $data = Visi::find($id);

            $array = [
                'deskripsi' => $data->deskripsi
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
            'deskripsi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_visi = new PivotPerubahanVisi;
        $pivot_perubahan_visi->visi_id = $request->hidden_id;
        $pivot_perubahan_visi->deskripsi = $request->deskripsi;
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
