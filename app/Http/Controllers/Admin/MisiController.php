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
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;

class MisiController extends Controller
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
            $data = Misi::latest()->get();
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
                ->editColumn('visi_id', function($data){
                    if($data->visi_id)
                    {
                        $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $data->visi_id)->latest()->first();
                        if($cek_perubahan_visi)
                        {
                            return strip_tags(substr($cek_perubahan_visi->deskripsi,0, 40)) . '...';
                        } else {
                            return strip_tags(substr($data->visi->deskripsi,0, 40)) . '...';
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id',$data->id)->latest()->first();
                    if($cek_perubahan_misi)
                    {
                        return strip_tags(substr($cek_perubahan_misi->deskripsi,0, 40)) . '...';
                    } else {
                        return strip_tags(substr($data->deskripsi,0, 40)) . '...';;
                    }
                })
                ->rawColumns(['aksi', 'visi_id', 'deskripsi'])
                ->make(true);
        }
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi
                ];
            }
        }
        return view('admin.misi.index', [
            'visis' => $visis
        ]);
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
            'misi_visi_id' => 'required',
            'misi_kode' => 'required',
            'misi_deskripsi' => 'required',
            'misi_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_misi = Misi::where('kode', $request->misi_kode)->where('visi_id', $request->misi_visi_id)->first();
        if($cek_misi)
        {
            $pivot = new PivotPerubahanMisi;
            $pivot->misi_id = $cek_misi->id;
            $pivot->visi_id = $request->misi_visi_id;
            $pivot->kode = $request->misi_kode;
            $pivot->deskripsi = $request->misi_deskripsi;
            $pivot->kabupaten_id = 62;
            $pivot->tahun_perubahan = $request->misi_tahun_perubahan;
            $pivot->save();
        } else {
            $misi = new Misi;
            $misi->visi_id = $request->misi_visi_id;
            $misi->kode = $request->misi_kode;
            $misi->deskripsi = $request->misi_deskripsi;
            $misi->kabupaten_id = 62;
            $misi->tahun_perubahan = $request->misi_tahun_perubahan;
            $misi->save();
        }

        return response()->json(['success' => 'berhasil']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Misi::find($id);

        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan_misi)
        {
            $get_perubahans = PivotPerubahanMisi::where('misi_id', $id)->get();
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $cek_perubahan_misi->visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($cek_perubahan_misi->visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }

            $html .= '<ul>';
            $html .= '<li><p>
                            Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$data->kode.'<br>
                            Misi: '.$data->deskripsi.'<br>
                            Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                            Status: <span class="text-primary">Sebelum Perubahan</span>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_perubahan->visi_id)->latest()->first();
                if($cek_perubahan_visi)
                {
                    $deskripsi_visi = $cek_perubahan_visi->deskripsi;
                } else {
                    $visi = Visi::find($get_perubahan->visi_id);
                    $deskripsi_visi = $visi->deskripsi;
                }
                $html .= '<li><p>
                            Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$get_perubahan->kode.'<br>
                            Misi: '.$get_perubahan->deskripsi.'<br>
                            Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';
            $kode_misi = $cek_perubahan_misi->kode;
            $deskripsi_misi = $cek_perubahan_misi->deskripsi;
        } else {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $data->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($data->id);
                $deskripsi_visi = $visi->deskripsi;
            }
            $html .= '<p>Tidak ada</p>';

            $kode_misi = $data->kode;
            $deskripsi_misi = $data->deskripsi;
        }

        $html .='</div>';

        $array = [
            'visi' => $deskripsi_visi,
            'kode' => $kode_misi,
            'deskripsi' => $deskripsi_misi,
            'pivot_perubahan_misi' => $html
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
        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $id)->latest()->first();
        if($cek_perubahan_misi)
        {
            $array = [
                'visi_id' => $cek_perubahan_misi->visi_id,
                'kode' => $cek_perubahan_misi->kode,
                'deskripsi' => $cek_perubahan_misi->deskripsi,
                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
            ];
        } else {
            $misi = Misi::find($id);
            $array = [
                'visi_id' => $misi->visi_id,
                'kode' => $misi->kode,
                'deskripsi' => $misi->deskripsi,
                'tahun_perubahan' => $misi->tahun_perubahan,
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
            'misi_visi_id' => 'required',
            'misi_kode' => 'required',
            'misi_deskripsi' => 'required',
            'misi_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan = new PivotPerubahanMisi;
        $pivot_perubahan->misi_id = $request->misi_hidden_id;
        $pivot_perubahan->visi_id = $request->misi_visi_id;
        $pivot_perubahan->kode = $request->misi_kode;
        $pivot_perubahan->deskripsi = $request->misi_deskripsi;
        $pivot_perubahan->tahun_perubahan = $request->misi_tahun_perubahan;
        $pivot_perubahan->kabupaten_id = 62;
        $pivot_perubahan->save();

        return response()->json(['success' => 'berhasil']);
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
