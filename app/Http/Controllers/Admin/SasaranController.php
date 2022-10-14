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
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;
use App\Imports\SasaranImport;

class SasaranController extends Controller
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
            $data = Sasaran::latest()->get();
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
                ->addColumn('misi', function($data){
                    if($data->tujuan_id)
                    {
                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $data->id)->latest()->first();
                        if($cek_perubahan_sasaran)
                        {
                            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
                        } else {
                            $sasaran = Sasaran::find($data->id);
                            $tujuan_id = $sasaran->tujuan_id;
                        }
                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
                        if($cek_perubahan_tujuan)
                        {
                            $misi_id = $cek_perubahan_tujuan->misi_id;
                        } else {
                            $tujuan = Tujuan::find($tujuan_id);
                            $misi_id = $tujuan->misi_id;
                        }
                        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
                        if($cek_perubahan_misi)
                        {
                            return $cek_perubahan_misi->kode;
                        } else {
                            $misi = Misi::find($misi_id);
                            return $misi_id->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->addColumn('tujuan_id', function($data){
                    if($data->tujuan_id)
                    {
                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $data->id)->latest()->first();
                        if($cek_perubahan_sasaran)
                        {
                            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
                        } else {
                            $sasaran = Sasaran::find($data->id);
                            $tujuan_id = $sasaran->tujuan_id;
                        }
                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
                        if($cek_perubahan_tujuan)
                        {
                            return $cek_perubahan_tujuan->kode;
                        } else {
                            $tujuan = Tujuan::find($tujuan_id);
                            return $tujuan->kode;
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('kode', function($data){
                    $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $data->id)->latest()->first();
                    if($cek_perubahan_sasaran)
                    {
                        return $cek_perubahan_sasaran->kode;
                    } else {
                        return $data->kode;
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan = PivotPerubahanSasaran::where('sasaran_id',$data->id)->latest()->first();
                    if($cek_perubahan)
                    {
                        return strip_tags(substr($cek_perubahan->deskripsi,0, 40)) . '...';
                    } else {
                        return strip_tags(substr($data->deskripsi,0, 40)) . '...';
                    }
                })
                ->addColumn('indikator', function($data){
                    $jumlah = PivotSasaranIndikator::whereHas('sasaran', function($q) use ($data){
                        $q->where('sasaran_id', $data->id);
                    })->count();

                    return '<a href="'.url('/admin/sasaran/'.$data->id.'/indikator').'" >'.$jumlah.'</a>';;
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
        return view('admin.sasaran.index', [
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

    public function get_tujuan(Request $request)
    {
        $get_tujuans = Tujuan::select('id', 'deskripsi')->where('misi_id', $request->id)->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::select('tujuan_id', 'deskripsi')->where('tujuan_id', $get_tujuan->id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan[] = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi
                ];
            } else {
                $tujuan[] = [
                    'id' => $get_tujuan->id,
                    'deskripsi' => $get_tujuan->deskripsi
                ];
            }
        }
        return response()->json($tujuan);
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
            'sasaran_tujuan_id' => 'required',
            'sasaran_kode' => 'required',
            'sasaran_deskripsi' => 'required',
            'sasaran_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_sasaran = Sasaran::where('kode', $request->sasaran_kode)
                        ->where('tujuan_id', $request->sasaran_tujuan_id)
                        ->first();
        if($cek_sasaran)
        {
            $pivot = new PivotPerubahanSasaran;
            $pivot->sasaran_id = $cek_sasaran->id;
            $pivot->tujuan_id = $request->sasaran_tujuan_id;
            $pivot->kode = $request->sasaran_kode;
            $pivot->deskripsi = $request->sasaran_deskripsi;
            $pivot->kabupaten_id = 62;
            $pivot->tahun_perubahan = $request->sasaran_tahun_perubahan;
            $pivot->save();
        } else {
            $sasaran = new Sasaran;
            $sasaran->tujuan_id = $request->sasaran_tujuan_id;
            $sasaran->kode = $request->sasaran_kode;
            $sasaran->deskripsi = $request->sasaran_deskripsi;
            $sasaran->kabupaten_id = 62;
            $sasaran->tahun_perubahan = $request->sasaran_tahun_perubahan;
            $sasaran->save();
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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
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
                                                                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                                                            ];
                                                        } else {
                                                            $misis[] = [
                                                                'id' => $get_misi->id,
                                                                'kode' => $get_misi->kode,
                                                                'deskripsi' => $get_misi->deskripsi,
                                                                'tahun_perubahan' => $get_misi->tahun_perubahan,
                                                            ];
                                                        }
                                                    }
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="70%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>
                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['tahun_perubahan'].'</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="accordian-body collapse" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->orderBy('tahun_perubahan','desc')
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="15%">'.$tujuan['kode'].'</td>
                                                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>
                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                    </td>
                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$tujuan['tahun_perubahan'].'</td>
                                                                                                    <td>
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sasaran.xlsx').'" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="accordian-body collapse" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                    ->latest()->first();
                                                                                                                        if($cek_perubahan_sasaran)
                                                                                                                        {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                                'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $get_sasaran->id,
                                                                                                                                'kode' => $get_sasaran->kode,
                                                                                                                                'deskripsi' => $get_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sasarans as $sasaran) {
                                                                                                                        $html .= '<tr>
                                                                                                                                    <td width="15%">'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="50%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="15%">'.$sasaran['tahun_perubahan'].'</td>
                                                                                                                                    <td width="20%">
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
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
                            $html .='</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['success' => $html]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Sasaran::find($id);
        $deskripsi_sasaran = '';
        $deskripsi_misi = '';
        $deskripsi_visi = '';

        $cek_perubahan = PivotPerubahanSasaran::where('sasaran_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan)
        {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $cek_perubahan->tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $kode_tujuan = $cek_perubahan_tujuan->kode;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($cek_perubahan->tujuan_id);
                $kode_tujuan = $tujuan->kode;
                $misi_id = $tujuan->misi_id;
            }
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
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
            $get_perubahans = PivotPerubahanSasaran::where('sasaran_id', $id)->get();
            $html .= '<ul>';
            $html .= '<li><p>
                            Deskripsi Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$kode_misi.' <br>
                            Kode Tujuan: '.$kode_tujuan.' <br>
                            Kode: '.$data->kode.'<br>
                            Deskripsi: '.$data->deskripsi.'<br>
                            Tahun Perubahan: '.$data->tahun_perubahan.'<br>
                            Status: <span class="text-primary">Sebelum Perubahan</span>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_perubahan->tujuan_id)->latest()->first();
                if($cek_perubahan_tujuan)
                {
                    $kode_tujuan = $cek_perubahan_tujuan->kode;
                    $misi_id = $cek_perubahan_tujuan->misi_id;
                    $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                } else {
                    $tujuan = Tujuan::find($get_perubahan->tujuan_id);
                    $kode_tujuan = $tujuan->kode;
                    $misi_id = $tujuan->misi_id;
                    $deskripsi_tujuan = $tujuan->deskripsi;
                }
                $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
                if($cek_perubahan_misi)
                {
                    $kode_misi = $cek_perubahan_misi->kode;
                    $visi_id = $cek_perubahan_misi->visi_id;
                    $deskripsi_misi = $cek_perubahan_misi->deskripsi;
                } else {
                    $misi = Misi::find($misi_id);
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
                            Kode Misi: '.$kode_tujuan.' <br>
                            Kode: '.$get_perubahan->kode.'<br>
                            Deskripsi: '.$get_perubahan->deskripsi.'<br>
                            Tahun Perubahan: '.$get_perubahan->tahun_perubahan.'<br>
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';

            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $cek_perubahan->tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                $kode_tujuan = $cek_perubahan_tujuan->kode;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($cek_perubahan->tujuan_id);
                $kode_tujuan = $tujuan->kode;
                $deskripsi_tujuan = $tujuan->deskripsi;
                $misi_id = $tujuan->misi_id;
            }
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $deskripsi_misi = $cek_perubahan_misi->deskripsi;
                $kode_misi = $cek_perubahan_misi->kode;
                $visi_id = $cek_perubahan_misi->visi_id;
            } else {
                $misi = Misi::find($cek_perubahan->misi_id);
                $deskripsi_misi = $misi->deskripsi;
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

            $kode_sasaran = $cek_perubahan->kode;
            $deskripsi_sasaran = $cek_perubahan->deskripsi;
            $tahun_perubahan_sasaran = $cek_perubahan->tahun_perubahan;
        } else {
            $html .= '<p>Tidak ada</p>';
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $data->tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                $kode_tujuan = $cek_perubahan_tujuan->kode;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($data->tujuan_id);
                $deskripsi_tujuan = $tujuan->deskripsi;
                $kode_tujuan = $tujuan->kode;
                $misi_id = $tujuan->misi_id;
            }
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $visi_id = $cek_perubahan_misi->visi_id;
                $deskripsi_misi = $cek_perubahan_misi->deskripsi;
                $kode_misi = $cek_perubahan_misi->kode;
            } else {
                $misi = Misi::find($data->misi_id);
                $visi_id = $misi->visi_id;
                $deskripsi_misi = $misi->deskripsi;
                $kode_misi = $misi->kode;
            }
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }

            $kode_sasaran = $data->kode;
            $deskripsi_sasaran = $data->deskripsi;
            $tahun_perubahan_sasaran = $data->tahun_perubahan;
        }

        $html .='</div>';

        // $cek_indikator = PivotSasaranIndikator::where('sasaran_id', $id)->first();
        // $indikator = '<div>';

        // if($cek_indikator){
        //     $get_indikators = PivotSasaranIndikator::where('sasaran_id', $id)->get();
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
            'kode_misi' => $kode_misi,
            'tujuan' => $deskripsi_tujuan,
            'kode_tujuan' => $kode_tujuan,
            'kode' => $kode_sasaran,
            'deskripsi' => $deskripsi_sasaran,
            'tahun_perubahan' => $tahun_perubahan_sasaran,
            'pivot_perubahan_sasaran' => $html
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
        $data = Sasaran::find($id);

        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $id)->latest()->first();
        if($cek_perubahan_sasaran)
        {
            $array = [
                'kode' => $cek_perubahan_sasaran->kode,
                'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan
            ];
        } else {
            $array = [
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan
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
            'sasaran_tujuan_id' => 'required',
            'sasaran_kode' => 'required',
            'sasaran_deskripsi' => 'required',
            'sasaran_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_sasaran = new PivotPerubahanSasaran;
        $pivot_perubahan_sasaran->sasaran_id = $request->sasaran_hidden_id;
        $pivot_perubahan_sasaran->tujuan_id = $request->sasaran_tujuan_id;
        $pivot_perubahan_sasaran->kode = $request->sasaran_kode;
        $pivot_perubahan_sasaran->deskripsi = $request->sasaran_deskripsi;
        $pivot_perubahan_sasaran->kabupaten_id = 62;
        $pivot_perubahan_sasaran->tahun_perubahan = $request->sasaran_tahun_perubahan;
        $pivot_perubahan_sasaran->save();

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

        $html = '<div class="data-table-rows slim" id="sasaran_div_table">
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
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle"></td>
                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi'.$visi['id'].'" class="accordion-toggle">
                                        '.$visi['deskripsi'].'
                                        <br>
                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="hiddenRow">
                                        <div class="accordian-body collapse" id="sasaran_visi'.$visi['id'].'">
                                            <table class="table table-striped">
                                                <tbody>';
                                                    $get_misis = Misi::where('visi_id', $visi['id'])->get();
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
                                                                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                                                            ];
                                                        } else {
                                                            $misis[] = [
                                                                'id' => $get_misi->id,
                                                                'kode' => $get_misi->kode,
                                                                'deskripsi' => $get_misi->deskripsi,
                                                                'tahun_perubahan' => $get_misi->tahun_perubahan,
                                                            ];
                                                        }
                                                    }
                                                    foreach ($misis as $misi) {
                                                        $html .= '<tr>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['kode'].'</td>
                                                                    <td width="70%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">
                                                                        '.$misi['deskripsi'].'
                                                                        <br>
                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                    </td>
                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi'.$misi['id'].'" class="accordion-toggle">'.$misi['tahun_perubahan'].'</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="hiddenRow">
                                                                        <div class="accordian-body collapse" id="sasaran_misi'.$misi['id'].'">
                                                                            <table class="table table-striped">
                                                                                <tbody>';
                                                                                    $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                    $tujuans = [];
                                                                                    foreach ($get_tujuans as $get_tujuan) {
                                                                                        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->orderBy('tahun_perubahan','desc')
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
                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle" width="15%">'.$tujuan['kode'].'</td>
                                                                                                    <td width="50%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                                                                                                        '.$tujuan['deskripsi'].'
                                                                                                        <br>
                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                    </td>
                                                                                                    <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$tujuan['tahun_perubahan'].'</td>
                                                                                                    <td>
                                                                                                        <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditSasaranModal" title="Tambah Data Sasaran" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-plus"></i></button>
                                                                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="'.asset('template/template_impor_sasaran.xlsx').'" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-tujuan-id="'.$tujuan['id'].'"><i class="fas fa-file-import"></i></button>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                <tr>
                                                                                                    <td colspan="4" class="hiddenRow">
                                                                                                        <div class="accordian-body collapse" id="sasaran_tujuan'.$tujuan['id'].'">
                                                                                                            <table class="table table-striped">
                                                                                                                <tbody>';
                                                                                                                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                                                                                    $sasarans = [];
                                                                                                                    foreach ($get_sasarans as $get_sasaran) {
                                                                                                                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                    ->latest()->first();
                                                                                                                        if($cek_perubahan_sasaran)
                                                                                                                        {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                                'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                                'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        } else {
                                                                                                                            $sasarans[] = [
                                                                                                                                'id' => $get_sasaran->id,
                                                                                                                                'kode' => $get_sasaran->kode,
                                                                                                                                'deskripsi' => $get_sasaran->deskripsi,
                                                                                                                                'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                                                                                                                            ];
                                                                                                                        }
                                                                                                                    }
                                                                                                                    foreach ($sasarans as $sasaran) {
                                                                                                                        $html .= '<tr>
                                                                                                                                    <td width="15%">'.$sasaran['kode'].'</td>
                                                                                                                                    <td width="50%">
                                                                                                                                        '.$sasaran['deskripsi'].'
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                                        <span class="badge bg-warning text-uppercase">'.$misi['kode'].' Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">'.$tujuan['kode'].' Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">'.$sasaran['kode'].' Sasaran</span>
                                                                                                                                    </td>
                                                                                                                                    <td width="15%">'.$sasaran['tahun_perubahan'].'</td>
                                                                                                                                    <td width="20%">
                                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
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
                            $html .='</tbody>
                        </table>
                    </div>
                </div>';

        return response()->json(['success' => $html]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function impor(Request $request)
    {
        $tujuan_id = $request->sasaran_impor_tujuan_id;
        $file = $request->file('impor_sasaran');
        Excel::import(new SasaranImport($tujuan_id), $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function destroy($id)
    {
        //
    }
}
