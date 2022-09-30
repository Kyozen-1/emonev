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
            'tujuan_id' => 'required',
            'kode' => 'required',
            'deskripsi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran = new Sasaran;
        $sasaran->tujuan_id = $request->tujuan_id;
        $sasaran->kode = $request->kode;
        $sasaran->deskripsi = $request->deskripsi;
        $sasaran->kabupaten_id = 62;
        $sasaran->save();

        return response()->json(['success' => 'Berhasil Menambahkan Sasaran']);
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
                            Status: <span class="text-warning">Perubahan '.$a++.'</span>
                        </p></li>';
            }
            $html .= '</ul>';

            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $cek_perubahan->tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($cek_perubahan->tujuan_id);
                $deskripsi_tujuan = $tujuan->deskripsi;
                $misi_id = $tujuan->misi_id;
            }
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
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

            $kode_sasaran = $cek_perubahan->kode;
            $deskripsi_sasaran = $cek_perubahan->deskripsi;
        } else {
            $html .= '<p>Tidak ada</p>';
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $data->tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($data->tujuan_id);
                $deskripsi_tujuan = $tujuan->deskripsi;
                $misi_id = $tujuan->misi_id;
            }
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
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

            $kode_sasaran = $data->kode;
            $deskripsi_sasaran = $data->deskripsi;
        }

        $html .='</div>';

        $cek_indikator = PivotSasaranIndikator::where('sasaran_id', $id)->first();
        $indikator = '<div>';

        if($cek_indikator){
            $get_indikators = PivotSasaranIndikator::where('sasaran_id', $id)->get();
            $indikator .= '<ul>';
            foreach ($get_indikators as $get_indikator) {
                $indikator .= '<li>'.$get_indikator->indikator.'</li>';
            }
            $indikator .= '</ul>';
        } else {
            $indikator .= '<p>Tidak ada</p>';
        }

        $indikator .='</div>';

        $array = [
            'visi' => $deskripsi_visi,
            'misi' => $deskripsi_misi,
            'tujuan' => $deskripsi_tujuan,
            'kode' => $kode_sasaran,
            'deskripsi' => $deskripsi_sasaran,
            'pivot_sasaran_indikator' => $indikator,
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
            $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
            if ($cek_perubahan_tujuan) {
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($tujuan_id);
                $tujuan_id = $tujuan->misi_id;
            }

            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $visi_id = $cek_perubahan_misi->visi_id;
            } else {
                $misi = Misi::find($misi_id);
                $visi_id = $misi->visi_id;
            }

            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visi_id = $cek_perubahan_visi->visi_id;
            } else {
                $visi = Visi::find($visi_id);
                $visi_id = $visi->visi_id;
            }

            $array = [
                'visi_id' => $visi_id,
                'misi_id' => $misi_id,
                'tujuan_id' => $tujuan_id,
                'kode' => $cek_perubahan_sasaran->kode,
                'deskripsi' => $cek_perubahan_sasaran->deskripsi
            ];
        } else {
            $tujuan_id = $data->tujuan_id;
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
                $visi_id = $cek_perubahan_misi->visi_id;
            } else {
                $kegiatan = Misi::find($misi_id);
                $visi_id = $kegiatan->visi_id;
            }

            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visi_id = $cek_perubahan_visi->visi_id;
            } else {
                $visi = Visi::find($visi_id);

                $visi_id = $visi->visi_id;
            }

            $array = [
                'visi_id' => $visi_id,
                'misi_id' => $misi_id,
                'tujuan_id' => $tujuan_id,
                'kode' => $data->kode,
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
            'tujuan_id' => 'required',
            'kode' => 'required',
            'deskripsi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan_sasaran = new PivotPerubahanSasaran;
        $pivot_perubahan_sasaran->sasaran_id = $request->hidden_id;
        $pivot_perubahan_sasaran->tujuan_id = $request->tujuan_id;
        $pivot_perubahan_sasaran->kode = $request->kode;
        $pivot_perubahan_sasaran->deskripsi = $request->deskripsi;
        $pivot_perubahan_sasaran->kabupaten_id = 62;
        $pivot_perubahan_sasaran->save();

        return response()->json(['success' => 'Berhasil Menambahkan Sasaran']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function impor(Request $request)
    {
        $file = $request->file('impor_sasaran');
        Excel::import(new SasaranImport, $file->store('temp'));
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
