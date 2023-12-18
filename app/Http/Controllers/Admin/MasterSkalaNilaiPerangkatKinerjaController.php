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
use Carbon\Carbon;
use App\Models\MasterSkalaNilaiPerangkatKinerja;
use App\Models\PivotTahunMasterSkalaNilaiPeringkatKinerja;

class MasterSkalaNilaiPerangkatKinerjaController extends Controller
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
            $data = MasterSkalaNilaiPerangkatKinerja::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    return $button;
                })
                ->addColumn('tahun', function($data){
                    $pivot_tahuns = $data->pivot_tahun_master_skala_nilai_peringkat_kinerja;
                    $html = '<ul>';
                    foreach ($pivot_tahuns as $pivot_tahun) {
                        $html .= '<li>'.$pivot_tahun->tahun.'</li>';
                    }
                    $html .='</ul>';
                    return $html;
                })
                ->rawColumns(['aksi', 'tahun'])
                ->make(true);
        }
        return view('admin.master-skala-nilai-perangkat-kinerja.index');
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
            'terkecil' => 'required',
            'terbesar' => 'required',
            'kriteria' => 'required',
            'tahun' => 'required | array',
            'tahun.*' => 'required | distinct',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $master_skala_nilai_perangkat_kinerja = new MasterSkalaNilaiPerangkatKinerja;
        $master_skala_nilai_perangkat_kinerja->terkecil = $request->terkecil;
        $master_skala_nilai_perangkat_kinerja->terbesar = $request->terbesar;
        $master_skala_nilai_perangkat_kinerja->kriteria = $request->kriteria;
        $master_skala_nilai_perangkat_kinerja->save();

        $tahuns = $request->tahun;
        for ($i=0; $i < count($tahuns); $i++) {
            $pivot = new PivotTahunMasterSkalaNilaiPeringkatKinerja;
            $pivot->master_skala_nilai_peringkat_kinerja_id = $master_skala_nilai_perangkat_kinerja->id;
            $pivot->tahun = $tahuns[$i];
            $pivot->save();
        }

        return response()->json(['success' => 'Berhasil Menambahkan Skala Nilai Perangkat Kinerja']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = MasterSkalaNilaiPerangkatKinerja::find($id);

        $pivot_tahuns = $data->pivot_tahun_master_skala_nilai_peringkat_kinerja;
        $html = '<ul>';
        foreach ($pivot_tahuns as $pivot_tahun) {
            $html .= '<li>'.$pivot_tahun->tahun.'</li>';
        }
        $html .='</ul>';

        $array = [
            'terkecil' => $data->terkecil,
            'terbesar' => $data->terbesar,
            'kriteria' => $data->kriteria,
            'tahun' => $html
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
        $data = MasterSkalaNilaiPerangkatKinerja::find($id);

        $pivot_tahuns = $data->pivot_tahun_master_skala_nilai_peringkat_kinerja;
        $tahuns = [];
        foreach ($pivot_tahuns as $pivot_tahun) {
            $tahuns[] = $pivot_tahun->tahun;
        }

        $array = [
            'terkecil' => $data->terkecil,
            'terbesar' => $data->terbesar,
            'kriteria' => $data->kriteria,
            'tahun' => $tahuns
        ];

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
            'terkecil' => 'required',
            'terbesar' => 'required',
            'kriteria' => 'required',
            'tahun' => 'required | array',
            'tahun.*' => 'required | distinct',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $master_skala_nilai_perangkat_kinerja = MasterSkalaNilaiPerangkatKinerja::find($request->hidden_id);
        $master_skala_nilai_perangkat_kinerja->terkecil = $request->terkecil;
        $master_skala_nilai_perangkat_kinerja->terbesar = $request->terbesar;
        $master_skala_nilai_perangkat_kinerja->kriteria = $request->kriteria;
        $master_skala_nilai_perangkat_kinerja->save();

        $getPivots = $master_skala_nilai_perangkat_kinerja->pivot_tahun_master_skala_nilai_peringkat_kinerja;
        foreach ($getPivots as $getPivot) {
            PivotTahunMasterSkalaNilaiPeringkatKinerja::find($getPivot->id)->delete();
        }

        $tahuns = $request->tahun;
        for ($i=0; $i < count($tahuns); $i++) {
            $pivot = new PivotTahunMasterSkalaNilaiPeringkatKinerja;
            $pivot->master_skala_nilai_peringkat_kinerja_id = $master_skala_nilai_perangkat_kinerja->id;
            $pivot->tahun = $tahuns[$i];
            $pivot->save();
        }

        return response()->json(['success' => 'Berhasil Merubah Skala Nilai Perangkat Kinerja']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = MasterSkalaNilaiPerangkatKinerja::find($id);

        $getPivots = $data->pivot_tahun_master_skala_nilai_peringkat_kinerja;
        foreach ($getPivots as $getPivot) {
            PivotTahunMasterSkalaNilaiPeringkatKinerja::find($getPivot->id)->delete();
        }

        $data->delete();
    }
}
