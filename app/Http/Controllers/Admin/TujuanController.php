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
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\PivotTujuanIndikator;
use App\Imports\TujuanImport;

class TujuanController extends Controller
{
    public function index()
    {
        if(request()->ajax())
        {
            $data = Tujuan::latest()->get();
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
                ->editColumn('misi_id', function($data){
                    if($data->misi_id)
                    {
                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $data->id)->latest()->first();
                        if($cek_perubahan_tujuan)
                        {
                            $misi_id = $cek_perubahan_tujuan->misi_id;
                        } else {
                            $misi_id = $data->misi_id;
                        }
                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
                        if($cek_perubahan_misi)
                        {
                            return $cek_perubahan_misi->kode;
                        } else {
                            $misi = Misi::find($misi_id);
                            return $misi->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $data->id)->latest()->first();
                    if($cek_perubahan_tujuan)
                    {
                        return $cek_perubahan_tujuan->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanTujuan::where('tujuan_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return strip_tags(substr($cek_perubahan->deskripsi,0, 40)) . '...';
                    } else {
                        return strip_tags(substr($data->deskripsi,0, 40)) . '...';
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotTujuanIndikator::whereHas('tujuan', function($q) use ($data){
                        $q->where('tujuan_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/tujuan/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
                })
                ->rawColumns(['aksi', 'indikator'])
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
        return view('admin.tujuan.index', [
            'visis' => $visis
        ]);
    }

    public function get_misi(Request $request)
    {
        $get_misis = Misi::select('id', 'deskripsi')->where('visi_id', $request->id)->get();
        $misi = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::select('misi_id', 'deskripsi')->where('misi_id', $get_misi->id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $misi[] = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'deskripsi' => $cek_perubahan_misi->deskripsi
                ];
            } else {
                $misi[] = [
                    'id' => $get_misi->id,
                    'deskripsi' => $get_misi->deskripsi
                ];
            }
        }
        return response()->json($misi);
    }

    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_misi_id' => 'required',
            'tujuan_kode' => 'required',
            'tujuan_deskripsi' => 'required',
            'tujuan_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_tujuan = Tujuan::where('kode', $request->tujuan_kode)
                        ->where('misi_id', $request->tujuan_misi_id)
                        ->first();
        if($cek_tujuan)
        {
            $pivot = new PivotPerubahanTujuan;
            $pivot->tujuan_id = $cek_tujuan->id;
            $pivot->misi_id = $request->tujuan_misi_id;
            $pivot->kode = $request->tujuan_kode;
            $pivot->deskripsi = $request->tujuan_deskripsi;
            $pivot->kabupaten_id = 62;
            $pivot->tahun_perubahan = $request->tujuan_tahun_perubahan;
            $pivot->save();
        } else {
            $tujuan = new Tujuan;
            $tujuan->misi_id = $request->tujuan_misi_id;
            $tujuan->kode = $request->tujuan_kode;
            $tujuan->deskripsi = $request->tujuan_deskripsi;
            $tujuan->kabupaten_id = 62;
            $tujuan->tahun_perubahan = $request->tujuan_tahun_perubahan;
            $tujuan->save();
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->orderBy('tahun_perubahan', 'desc')
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->visi_id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="85%">Visi</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($visis as $visi) {
                            $html .= '<tr>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                                                            ->orderBy('tahun_perubahan', 'desc')
                                                                            ->latest()
                                                                            ->first();
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
                                                    $html .= '<tr>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                        <td width="50%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>
                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                        </td>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">2020</td>
                                                        <td>
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_tujuan.xlsx').'" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="'.$misi['id'].'"><i class="fas fa-file-import"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="accordian-body collapse" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->orderBy('tahun_perubahan', 'desc')
                                                                                                    ->latest()
                                                                                                    ->first();
                                                                        if($cek_perubahan_tujuan)
                                                                        {
                                                                            $tujuans[] = [
                                                                                'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                'kode' => $cek_perubahan_tujuan->kode,
                                                                                'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                                                                            ];
                                                                        } else {
                                                                            $tujuans[] = [
                                                                                'id' => $get_tujuan->id,
                                                                                'kode' => $get_tujuan->kode,
                                                                                'deskripsi' => $get_tujuan->deskripsi,
                                                                                'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                                                                            ];
                                                                        }
                                                                    }
                                                                    foreach ($tujuans as $tujuan) {
                                                                        $html .= '<tr>
                                                                            <td width="15%">'.$tujuan['kode'].'</td>
                                                                            <td width="50%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                            </td>
                                                                            <td width="15%">'.$tujuan['tahun_perubahan'].'</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
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

    public function show($id)
    {
        $data = Tujuan::find($id);
        $deskripsi_misi = '';
        $deskripsi_visi = '';

        $cek_perubahan = PivotPerubahanTujuan::where('tujuan_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $cek_perubahan->misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $kode_misi = $cek_perubahan_misi->kode;
                $visi_id = $cek_perubahan_misi->visi_id;
            } else {
                $misi = Misi::find($cek_perubahan->misi_id);
                $kode_misi = $misi->kode;
                $visi_id = $misi->visi_id;
            }
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }
            $get_perubahans = PivotPerubahanTujuan::where('tujuan_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li><p>
                            Deskripsi Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$kode_misi.' <br>
                            Kode: '.$data->kode.'<br>
                            Deskripsi: '.$data->deskripsi.'<br>
                            Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                            Status: <span class="text-primary">Sebelum Perubahan</span>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_perubahan->misi_id)->latest()->first();
                if($cek_perubahan_misi)
                {
                    $kode_misi = $cek_perubahan_misi->kode;
                    $visi_id = $cek_perubahan_misi->visi_id;
                    $deskripsi_misi = $cek_perubahan_misi->deskripsi;
                } else {
                    $misi = Misi::find($get_perubahan->misi_id);
                    $kode_misi = $misi->kode;
                    $visi_id = $misi->visi_id;
                    $deskripsi_misi = $misi->deskripsi;
                }
                $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
                if($cek_perubahan_visi)
                {
                    $kode_urusan = $cek_perubahan_visi->kode;
                    $deskripsi_visi = $cek_perubahan_visi->deskripsi;
                } else {
                    $visi = Visi::find($visi_id);
                    $deskripsi_visi = $visi->deskripsi;
                }
                $html .= '<li><p>
                            Deskripsi Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$kode_misi.' <br>
                            Kode: '.$get_perubahan->kode.'<br>
                            Deskripsi: '.$get_perubahan->deskripsi.'<br>
                            Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';

            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $cek_perubahan->misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $deskripsi_misi = $cek_perubahan_misi->deskripsi;
                $visi_id = $cek_perubahan_misi->visi_id;
            } else {
                $misi = Misi::find($cek_perubahan->misi_id);
                $deskripsi_misi = $misi->deskripsi;
                $visi_id = $misi->visi_id;
            }
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }

            $kode_tujuan = $cek_perubahan->kode;
            $deskripsi_tujuan = $cek_perubahan->deskripsi;
            $tahun_perubahan_tujuan = $cek_perubahan->tahun_perubahan;
        } else {
            $html .= '<p>Tidak ada</p>';
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $data->misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $visi_id = $cek_perubahan_misi->visi_id;
                $deskripsi_misi = $cek_perubahan_misi->deskripsi;
            } else {
                $misi = Misi::find($data->misi_id);
                $visi_id = $misi->visi_id;
                $deskripsi_misi = $misi->deskripsi;
            }
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }

            $kode_tujuan = $data->kode;
            $deskripsi_tujuan = $data->deskripsi;
            $tahun_perubahan_tujuan = $data->tahun_perubahan;
        }

        $html .='</div>';

        // $cek_indikator = PivotTujuanIndikator::where('tujuan_id', $id)->first();
        // $indikator = '<div>';

        // if($cek_indikator){
        //     $get_indikators = PivotTujuanIndikator::where('tujuan_id', $id)->get();
        //     $indikator .= '<ul>';
        //     foreach ($get_indikators as $get_indikator) {
        //         $indikator .= '<li>'.$get_indikator->indikator.'</li>';
        //     }
        //     $indikator .= '</ul>';
        // } else {
        //     $indikator .= '<p>Tidak ada</p>';
        // }

        // $indikator .='</div>';

        $array = [
            'visi' => $deskripsi_visi,
            'misi' => $deskripsi_misi,
            'kode' => $kode_tujuan,
            'deskripsi' => $deskripsi_tujuan,
            'tahun_perubahan' => $tahun_perubahan_tujuan,
            'pivot_perubahan_tujuan' => $html
        ];

        return response()->json(['result' => $array]);
    }

    public function edit($id)
    {
        $data = Tujuan::find($id);

        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $id)->latest()->first();
        if($cek_perubahan_tujuan)
        {
            $array = [
                'id' => $cek_perubahan_tujuan->tujuan_id,
                'kode' => $cek_perubahan_tujuan->kode,
                'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
            ];
        } else {
            $array = [
                'id' => $data->id,
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_misi_id' => 'required',
            'tujuan_kode' => 'required',
            'tujuan_deskripsi' => 'required',
            'tujuan_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_tujuan = new PivotPerubahanTujuan;
        $pivot_perubahan_tujuan->tujuan_id = $request->tujuan_hidden_id;
        $pivot_perubahan_tujuan->misi_id = $request->tujuan_misi_id;
        $pivot_perubahan_tujuan->kode = $request->tujuan_kode;
        $pivot_perubahan_tujuan->deskripsi = $request->tujuan_deskripsi;
        $pivot_perubahan_tujuan->tahun_perubahan = $request->tujuan_tahun_perubahan;
        $pivot_perubahan_tujuan->kabupaten_id = 62;
        $pivot_perubahan_tujuan->save();

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->orderBy('tahun_perubahan', 'desc')
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->visi_id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }
        $html = '<div class="data-table-rows slim" id="tujuan_div_table">
                    <div class="data-table-responsive-wrapper">
                        <table class="table table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">Kode</th>
                                    <th width="85%">Visi</th>
                                </tr>
                            </thead>
                            <tbody>';
                        foreach ($visis as $visi) {
                            $html .= '<tr>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="tujuan_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                $get_misis = Misi::where('visi_id', $visi['id'])->get();
                                                $misis = [];
                                                foreach ($get_misis as $get_misi) {
                                                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                                                            ->orderBy('tahun_perubahan', 'desc')
                                                                            ->latest()
                                                                            ->first();
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
                                                    $html .= '<tr>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                        <td width="50%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">
                                                            '.$misi['deskripsi'].'
                                                            <br>
                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                            <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                        </td>
                                                        <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi'.$misi['id'].'" class="accordion-toggle">2020</td>
                                                        <td>
                                                            <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTujuanModal" title="Tambah Data Misi" data-misi-id="'.$misi['id'].'"><i class="fas fa-plus"></i></button>
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_tujuan.xlsx').'" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="'.$misi['id'].'"><i class="fas fa-file-import"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" class="hiddenRow">
                                                            <div class="accordian-body collapse" id="tujuan_misi'.$misi['id'].'">
                                                                <table class="table table-striped">
                                                                    <tbody>';
                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                    $tujuans = [];
                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                                                                                    ->orderBy('tahun_perubahan', 'desc')
                                                                                                    ->latest()
                                                                                                    ->first();
                                                                        if($cek_perubahan_tujuan)
                                                                        {
                                                                            $tujuans[] = [
                                                                                'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                'kode' => $cek_perubahan_tujuan->kode,
                                                                                'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                                                                            ];
                                                                        } else {
                                                                            $tujuans[] = [
                                                                                'id' => $get_tujuan->id,
                                                                                'kode' => $get_tujuan->kode,
                                                                                'deskripsi' => $get_tujuan->deskripsi,
                                                                                'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                                                                            ];
                                                                        }
                                                                    }
                                                                    foreach ($tujuans as $tujuan) {
                                                                        $html .= '<tr>
                                                                            <td width="15%">'.$tujuan['kode'].'</td>
                                                                            <td width="50%">
                                                                                '.$tujuan['deskripsi'].'
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                            </td>
                                                                            <td width="15%">'.$tujuan['tahun_perubahan'].'</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
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

    public function impor(Request $request)
    {
        $misi_id = $request->tujuan_impor_misi_id;
        $file = $request->file('impor_tujuan');
        Excel::import(new TujuanImport($misi_id), $file->store('temp'));
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
