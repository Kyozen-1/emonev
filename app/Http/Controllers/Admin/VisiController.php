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
use App\Models\PivotPerubahanTujuan;
use App\Models\Tujuan;
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\TujuanPdRealisasiRenja;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\TujuanTwRealisasi;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\SasaranTwRealisasi;
use App\Models\SasaranPd;
use App\Models\SasaranTargetSatuanRpRealisasi;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranIndikatorKinerja;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\ProgramRpjmd;
use App\Models\TahunPeriode;
use App\Models\SasaranPdProgramRpjmd;

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
            $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
            $tahun_awal = $get_periode->tahun_awal;
            $get_visis = Visi::latest()->get();
            $data = [];
            foreach ($get_visis as $get_visi) {
                $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id);
                if(request()->filter_tahun)
                {
                    $cek_perubahan_visi = $cek_perubahan_visi->where('tahun_perubahan', request()->filter_tahun);
                } else {
                    $cek_perubahan_visi = $cek_perubahan_visi->where('tahun_perubahan', $tahun_awal);
                }
                $cek_perubahan_visi = $cek_perubahan_visi->latest()->first();
                if($cek_perubahan_visi)
                {
                    $data[] = [
                        'id' => $get_visi->id,
                        'deskripsi' => $cek_perubahan_visi->deskripsi,
                        'kode' => $cek_perubahan_visi->kode
                    ];
                } else {
                    $data[] = [
                        'id' => $get_visi->id,
                        'deskripsi' => $get_visi->deskripsi,
                        'kode' => $get_visi->kode
                    ];
                }
            }
            $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="visi_detail" id="'.$data['id'].'" class="visi_detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="visi_edit" id="'.$data['id'].'"
                    class="visi_edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('deskripsi', function($data) use ($tahun_sekarang){
                    $cek_perubahan = PivotPerubahanVisi::where('visi_id',$data['id'])->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data['deskripsi'];
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

    public function get_visi($tahun)
    {
        if(request()->ajax())
        {
            $get_visis = Visi::latest()->get();
            $data = [];
            foreach ($get_visis as $get_visi) {
                $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id);
                $cek_perubahan_visi = $cek_perubahan_visi->where('tahun_perubahan', $tahun);
                $cek_perubahan_visi = $cek_perubahan_visi->latest()->first();
                if($cek_perubahan_visi)
                {
                    $data[] = [
                        'id' => $get_visi->id,
                        'deskripsi' => $cek_perubahan_visi->deskripsi,
                        'kode' => $cek_perubahan_visi->kode
                    ];
                } else {
                    $data[] = [
                        'id' => $get_visi->id,
                        'deskripsi' => $get_visi->deskripsi,
                        'kode' => $get_visi->kode
                    ];
                }
            }
            $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="visi_detail" id="'.$data['id'].'" class="visi_detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="visi_edit" id="'.$data['id'].'"
                    class="visi_edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    $button_delete = '<button type="button" name="delete" id="'.$data['id'].'" class="visi_delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    // $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('deskripsi', function($data) use ($tahun_sekarang){
                    $cek_perubahan = PivotPerubahanVisi::where('visi_id',$data['id'])->where('tahun_perubahan', $tahun_sekarang)->latest()->first();
                    if($cek_perubahan)
                    {
                        return $cek_perubahan->deskripsi;
                    } else {
                        return $data['deskripsi'];
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
    public function destroy(Request $request)
    {
        DB::transaction(function () use ($request){
            $getMisis = Misi::where('visi_id', $request->id)->get();
            foreach ($getMisis as $getMisi) {
                $getTujuans = Tujuan::where('misi_id', $getMisi->id)->get();
                foreach ($getTujuans as $getTujuan) {
                    $getSasarans = Sasaran::where('tujuan_id', $getTujuan->id)->get();
                    foreach ($getSasarans as $getSasaran) {

                        $getSasaranIndikatorKinerjas = SasaranIndikatorKinerja::where('sasaran_id', $getSasaran->id)->get();
                        foreach ($getSasaranIndikatorKinerjas as $getSasaranIndikatorKinerja) {
                            $getPivotSasaranIndikatorProgramRpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $getSasaranIndikatorKinerja->id)->get();
                            foreach ($getPivotSasaranIndikatorProgramRpjmds as $getPivotSasaranIndikatorProgramRpjmd) {
                                PivotSasaranIndikatorProgramRpjmd::find($getPivotSasaranIndikatorProgramRpjmd->id)->delete();
                            }

                            $getSasaranTargetSatuanRpRealisasis = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $getSasaranIndikatorKinerja->id)->get();
                            foreach ($getSasaranTargetSatuanRpRealisasis as $getSasaranTargetSatuanRpRealisasi) {
                                $getSasaranTwRealisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $getSasaranTargetSatuanRpRealisasi->id)->get();
                                foreach ($getSasaranTwRealisasis as $getSasaranTwRealisasi) {
                                    SasaranTwRealisasi::find($getSasaranTwRealisasi->id)->delete();
                                }

                                SasaranTargetSatuanRpRealisasi::find($getSasaranTargetSatuanRpRealisasi->id)->delete();
                            }

                            SasaranIndikatorKinerja::find($getSasaranIndikatorKinerja->id)->delete();
                        }

                        $getSasaranPds = SasaranPd::where('sasaran_id', $getSasaran->id)->get();
                        foreach ($getSasaranPds as $getSasaranPd) {
                            $getSasaranPdProgramRpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $getSasaranPd->id)->get();
                            foreach ($getSasaranPdProgramRpjmds as $getSasaranPdProgramRpjmd) {
                                SasaranPdProgramRpjmd::find($getSasaranPdProgramRpjmd->id)->delete();
                            }

                            $getSasaranPdIndikatorKinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $getSasaranPd->id)->get();
                            foreach ($getSasaranPdIndikatorKinerjas as $getSasaranPdIndikatorKinerja) {
                                $getSasaranPdTargetSatuanRpRealisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $getSasaranPdIndikatorKinerja->id)->get();
                                foreach ($getSasaranPdTargetSatuanRpRealisasis as $getSasaranPdTargetSatuanRpRealisasi) {
                                    $getSasaranPdRealisasiRenjas = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $getSasaranPdTargetSatuanRpRealisasi->id)->get();
                                    foreach ($getSasaranPdRealisasiRenjas as $getSasaranPdRealisasiRenja) {
                                        SasaranPdRealisasiRenja::find($getSasaranPdRealisasiRenja->id)->delete();
                                    }

                                    SasaranPdTargetSatuanRpRealisasi::find($getSasaranPdTargetSatuanRpRealisasi->id)->delete();
                                }

                                SasaranPdIndikatorKinerja::find($getSasaranPdIndikatorKinerja->id)->delete();
                            }

                            $getPivotPerubahanSasaranPds = PivotPerubahanSasaranPd::where('sasaran_pd_id', $getSasaranPd->id)->get();
                            foreach ($getPivotPerubahanSasaranPds as $getPivotPerubahanSasaranPd) {
                                PivotPerubahanSasaranPd::find($getPivotPerubahanSasaranPd->id)->delete();
                            }

                            SasaranPd::find($getSasaranPd->id)->delete();
                        }

                        $getPivotPerubahanSasarans = PivotPerubahanSasaran::where('sasaran_id', $getSasaran->id)->get();
                        foreach ($getPivotPerubahanSasarans as $getPivotPerubahanSasaran) {
                            PivotPerubahanSasaran::find($getPivotPerubahanSasaran->id)->delete();
                        }

                        Sasaran::find($getSasaran->id)->delete();
                    }

                    $getTujuanIndikatorKinerjas = TujuanIndikatorKinerja::where('tujuan_id', $getTujuan->id)->get();
                    foreach ($getTujuanIndikatorKinerjas as $getTujuanIndikatorKinerja) {
                        $getTujuanTargetSatuanRpRealisasis = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $getTujuanIndikatorKinerja->id)->get();
                        foreach ($getTujuanTargetSatuanRpRealisasis as $getTujuanTargetSatuanRpRealisasi) {
                            $getTujuanTwRealisasis = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $getTujuanTargetSatuanRpRealisasi->id)->get();
                            foreach ($getTujuanTwRealisasis as $getTujuanTwRealisasi) {
                                TujuanTwRealisasi::find($getTujuanTwRealisasi->id)->delete();
                            }

                            TujuanTargetSatuanRpRealisasi::find($getTujuanTargetSatuanRpRealisasi->id)->delete();
                        }

                        TujuanIndikatorKinerja::find($getTujuanIndikatorKinerja->id)->delete();
                    }

                    $getTujuanPds = TujuanPd::where('tujuan_id', $getTujuan->id)->get();
                    foreach ($getTujuanPds as $getTujuanPd) {
                        $getTujuanPdIndikatorKinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $getTujuanPd->id)->get();
                        foreach ($getTujuanPdIndikatorKinerjas as $getTujuanPdIndikatorKinerja) {

                            $getTujuanPdTargetSatuanRpRealisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $getTujuanPdIndikatorKinerja->id)->get();
                            foreach ($getTujuanPdTargetSatuanRpRealisasis as $getTujuanPdTargetSatuanRpRealisasi) {
                                $getTujuanPdRealisasiRenjas = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $getTujuanPdTargetSatuanRpRealisasi->id)->get();
                                foreach ($getTujuanPdRealisasiRenjas as $getTujuanPdRealisasiRenja) {
                                    TujuanPdRealisasiRenja::find($getTujuanPdRealisasiRenja->id)->delete();
                                }
                                TujuanPdTargetSatuanRpRealisasi::find($getTujuanPdTargetSatuanRpRealisasi->id)->delete();
                            }
                            TujuanPdIndikatorKinerja::find($getTujuanPdIndikatorKinerja->id)->delete();
                        }
                        $getPivotPerubahanTujuanPds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $getTujuanPd->id)->get();
                        foreach ($getPivotPerubahanTujuanPds as $getPivotPerubahanTujuanPd) {
                            PivotPerubahanTujuanPd::find($getPivotPerubahanTujuanPd->id)->delete();
                        }
                        TujuanPd::find($getTujuanPd->id)->delete();
                    }

                    $getPivotPerubahanTujuans = PivotPerubahanTujuan::where('tujuan_id', $getTujuan->id)->get();
                    foreach ($getPivotPerubahanTujuans as $getPivotPerubahanTujuan) {
                        PivotPerubahanTujuan::find($getPivotPerubahanTujuan->id)->delete();
                    }
                    Tujuan::find($getTujuan->id)->delete();
                }

                $getPivotPerubahanMisis = PivotPerubahanMisi::where('misi_id', $getMisi->id)->get();
                foreach ($getPivotPerubahanMisis as $getPivotPerubahanMisi) {
                    PivotPerubahanMisi::find($getPivotPerubahanMisi->id)->delete();
                }
                Misi::find($getMisi->id)->delete();
            }

            $getPivotPerubahanVisis = PivotPerubahanVisi::where('visi_id', $request->id)->get();
            foreach ($getPivotPerubahanVisis as $getPivotPerubahanVisi) {
                PivotPerubahanVisi::find($getPivotPerubahanVisi->id)->delete();
            }

            Visi::find($request->id)->delete();
        });

        return response()->json(['success' => 'Berhasil menghapus']);
    }
}
