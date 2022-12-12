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
use App\Models\PivotOpdProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\TargetRpPertahunProgram;
use App\Models\RenstraKegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SasaranIndikatorKinerja;
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\MasterTw;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\TujuanPdRealisasiRenja;
use App\Models\SasaranPdRealisasiRenja;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranTargetSatuanRpRealisasi;

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
        return response()->json(['success' => 'Berhasil']);
    }

    public function show($id)
    {
        $data = Tujuan::find($id);
        $deskripsi_misi = '';
        $deskripsi_visi = '';

        $cek_perubahan = PivotPerubahanTujuan::where('tujuan_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
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

        $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
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
        return response()->json(['success' => 'berhasil']);
    }

    public function impor(Request $request)
    {
        $file = $request->file('impor_tujuan');
        Excel::import(new TujuanImport, $file->store('temp'));
        $msg = [session('import_status'), session('import_message')];
        if ($msg[0]) {
            Alert::success('Berhasil', $msg[1]);
            return back();
        } else {
            Alert::error('Gagal', $msg[1]);
            return back();
        }
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_tujuan_tujuan_id' => 'required',
            'indikator_kinerja_tujuan_deskripsi' => 'required',
            'indikator_kinerja_tujuan_satuan' => 'required',
            'indikator_kinerja_tujuan_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $indikator_kinerja = new TujuanIndikatorKinerja;
        $indikator_kinerja->tujuan_id = $request->indikator_kinerja_tujuan_tujuan_id;
        $indikator_kinerja->deskripsi = $request->indikator_kinerja_tujuan_deskripsi;
        $indikator_kinerja->satuan = $request->indikator_kinerja_tujuan_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_tujuan_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil Menambahkan Indikator Kinerja untuk Tujuan');
        return redirect()->route('admin.perencanaan.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = TujuanIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_indikator_kinerja_tujuan_id' => 'required',
            'edit_indikator_kinerja_tujuan_deskripsi' => 'required',
            'edit_indikator_kinerja_tujuan_satuan' => 'required',
            'edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $indikator_kinerja = TujuanIndikatorKinerja::find($request->edit_indikator_kinerja_tujuan_id);
        $indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_tujuan_deskripsi;
        $indikator_kinerja->satuan = $request->edit_indikator_kinerja_tujuan_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        Alert::success('Berhasil', 'Berhasil Merubah Indikator Kinerja untuk Tujuan');
        return redirect()->route('admin.perencanaan.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $tujuan_target_satuan_rp_realisasies = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $request->tujuan_indikator_kinerja_id)->get();
        foreach ($tujuan_target_satuan_rp_realisasies as $tujuan_target_satuan_rp_realisasi) {
            TujuanTargetSatuanRpRealisasi::find($tujuan_target_satuan_rp_realisasi->id)->delete();
        }
        TujuanIndikatorKinerja::find($request->tujuan_indikator_kinerja_id)->delete();

        return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk Tujuan']);
    }

    public function store_tujuan_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'tujuan_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_target_satuan_rp_realisasi = new TujuanTargetSatuanRpRealisasi;
        $tujuan_target_satuan_rp_realisasi->tujuan_indikator_kinerja_id = $request->tujuan_indikator_kinerja_id;
        $tujuan_target_satuan_rp_realisasi->target = $request->target;
        $tujuan_target_satuan_rp_realisasi->tahun = $request->tahun;
        $tujuan_target_satuan_rp_realisasi->save();

        return response()->json(['success' => 'Berhasil menambahkan data']);
    }

    public function update_tujuan_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_target_satuan_rp_realisasi' => 'required',
            'tujuan_edit_target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::find($request->tujuan_target_satuan_rp_realisasi);
        $tujuan_target_satuan_rp_realisasi->target = $request->tujuan_edit_target;
        $tujuan_target_satuan_rp_realisasi->save();

        Alert::success('Berhasil', 'Berhasil Merubahan Target Tujuan');
        return redirect()->route('admin.perencanaan.index');
    }

    public function hapus(Request $request)
    {
        if($request->hapus_tujuan_tahun == 'semua')
        {
            // Hapus Pivot Perubahan Tujuan
            $get_perubahan_tujuans = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->get();
            foreach ($get_perubahan_tujuans as $get_perubahan_tujuan) {
                PivotPerubahanTujuan::find($get_perubahan_tujuan->id)->delete();
            }

            // Hapus Tujuan Indikator
            $get_tujuan_indikators = TujuanIndikatorKinerja::where('tujuan_id', $request->hapus_tujuan_id)->get();
            foreach ($get_tujuan_indikators as $get_tujuan_indikator) {
                $get_tujuan_target_satuan_rp_realisasies = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $get_tujuan_indikator->id)->get();
                foreach ($get_tujuan_target_satuan_rp_realisasies as $get_tujuan_target_satuan_rp_realisasi) {
                    TujuanTargetSatuanRpRealisasi::find($get_tujuan_target_satuan_rp_realisasi->id)->delete();
                }

                TujuanTargetSatuanRpRealisasi::find($get_tujuan_indikator->id)->delete();
            }

            // Hapus Tujuan Pd
            $get_tujuan_pds = TujuanPd::where('tujuan_id', $request->hapus_tujuan_id)->get();
            foreach ($get_tujuan_pds as $get_tujuan_pd) {
                $cek_perubahan_tujuan_pds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                foreach ($cek_perubahan_tujuan_pds as $cek_perubahan_tujuan_pd) {
                    PivotPerubahanTujuanPd::find($cek_perubahan_tujuan_pd->id)->delete();
                }

                $get_tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                foreach ($get_tujuan_pd_indikator_kinerjas as $get_tujuan_pd_indikator_kinerja) {
                    $tujuan_pd_target_satuan_rp_realisasies = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $get_tujuan_pd_indikator_kinerja->id)->get();
                    foreach ($tujuan_pd_target_satuan_rp_realisasies as $tujuan_pd_target_satuan_rp_realisasi) {
                        $tujuan_pd_realisasi_renjas = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $tujuan_pd_target_satuan_rp_realisasi->id)->get();
                        foreach ($tujuan_pd_realisasi_renjas as $tujuan_pd_realisasi_renja) {
                            TujuanPdRealisasiRenja::find($tujuan_pd_realisasi_renja->id)->delete();
                        }

                        TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
                    }

                    TujuanPdIndikatorKinerja::find($get_tujuan_pd_indikator_kinerja->id)->delete();
                }

                TujuanPd::find($get_tujuan_pd->id)->delete();
            }

            // Hapus Sasaran
            $get_sasarans = Sasaran::where('tujuan_id', $request->hapus_tujuan_id)->get();

            foreach ($get_sasarans as $get_sasaran) {
                $pivot_perubahan_sasarans = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->get();
                foreach ($pivot_perubahan_sasarans as $pivot_perubahan_sasaran) {
                    PivotPerubahanSasaran::find($pivot_perubahan_sasaran->id)->delete();
                }

                $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $get_sasaran->id)->get();
                foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                    $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                    foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                        PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                    }

                    $sasaran_target_satuan_rp_realisasies = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                    foreach ($sasaran_target_satuan_rp_realisasies as $sasaran_target_satuan_rp_realisasi) {
                        SasaranTargetSatuanRpRealisasi::find($sasaran_target_satuan_rp_realisasi->id)->delete();
                    }

                    SasaranIndikatorKinerja::find($sasaran_indikator_kinerja->id)->delete();
                }

                $sasaran_pds = SasaranPd::where('sasaran_id', $get_sasaran->id)->get();
                foreach ($sasaran_pds as $sasaran_pd) {
                    $pivot_perubahan_sasaran_pds = PivotPerubahanSasaranPd::where('sasaran_id', $sasaran_pd->id)->get();
                    foreach ($pivot_perubahan_sasaran_pds as $pivot_perubahan_sasaran_pd) {
                        PivotPerubahanSasaranPd::find($pivot_perubahan_sasaran_pd->id)->delete();
                    }

                    $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $sasaran_pd_target_satuan_rp_realisasies = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->get();
                        foreach ($sasaran_pd_target_satuan_rp_realisasies as $sasaran_pd_target_satuan_rp_realisasi) {
                            $sasaran_pd_realisasi_renjas = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $sasaran_pd_target_satuan_rp_realisasi->id)->get();
                            foreach ($sasaran_pd_realisasi_renjas as $sasaran_pd_realisasi_renja) {
                                SasaranPdRealisasiRenja::find($sasaran_pd_realisasi_renja->id)->delete();
                            }

                            SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
                        }

                        SasaranPdIndikatorKinerja::find($sasaran_pd_indikator_kinerja->id)->delete();
                    }

                    $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd->id)->get();
                    foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                        SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                    }

                    SasaranPd::find($sasaran_pd->id)->delete();
                }

                Sasaran::find($get_sasaran->id)->delete();
            }

            Tujuan::find($request->hapus_tujuan_id)->delete();
        } else {
            $cek_perubahan_tujuan_1 = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->where('tahun_perubahan', $request->hapus_tujuan_tahun)->first();
            if($cek_perubahan_tujuan_1)
            {
                PivotPerubahanTujuan::find($cek_perubahan_tujuan_1->id)->delete();
            } else {
                $cek_perubahan_tujuan_2 = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->first();
                if(!$cek_perubahan_tujuan_2)
                {
                    // Hapus Pivot Perubahan Tujuan
                    $get_perubahan_tujuans = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->get();
                    foreach ($get_perubahan_tujuans as $get_perubahan_tujuan) {
                        PivotPerubahanTujuan::find($get_perubahan_tujuan->id)->delete();
                    }

                    // Hapus Tujuan Indikator
                    $get_tujuan_indikators = TujuanIndikatorKinerja::where('tujuan_id', $request->hapus_tujuan_id)->get();
                    foreach ($get_tujuan_indikators as $get_tujuan_indikator) {
                        $get_tujuan_target_satuan_rp_realisasies = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $get_tujuan_indikator->id)->get();
                        foreach ($get_tujuan_target_satuan_rp_realisasies as $get_tujuan_target_satuan_rp_realisasi) {
                            TujuanTargetSatuanRpRealisasi::find($get_tujuan_target_satuan_rp_realisasi->id)->delete();
                        }

                        TujuanIndikatorKinerja::find($get_tujuan_indikator->id)->delete();
                    }

                    // Hapus Tujuan Pd
                    $get_tujuan_pds = TujuanPd::where('tujuan_id', $request->hapus_tujuan_id)->get();
                    foreach ($get_tujuan_pds as $get_tujuan_pd) {
                        $cek_perubahan_tujuan_pds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                        foreach ($cek_perubahan_tujuan_pds as $cek_perubahan_tujuan_pd) {
                            PivotPerubahanTujuanPd::find($cek_perubahan_tujuan_pd->id)->delete();
                        }

                        $get_tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $get_tujuan_pd->id)->get();
                        foreach ($get_tujuan_pd_indikator_kinerjas as $get_tujuan_pd_indikator_kinerja) {
                            $tujuan_pd_target_satuan_rp_realisasies = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $get_tujuan_pd_indikator_kinerja->id)->get();
                            foreach ($tujuan_pd_target_satuan_rp_realisasies as $tujuan_pd_target_satuan_rp_realisasi) {
                                $tujuan_pd_realisasi_renjas = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $tujuan_pd_target_satuan_rp_realisasi->id)->get();
                                foreach ($tujuan_pd_realisasi_renjas as $tujuan_pd_realisasi_renja) {
                                    TujuanPdRealisasiRenja::find($tujuan_pd_realisasi_renja->id)->delete();
                                }

                                TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
                            }

                            TujuanPdIndikatorKinerja::find($get_tujuan_pd_indikator_kinerja->id)->delete();
                        }

                        TujuanPd::find($get_tujuan_pd->id)->delete();
                    }

                    // Hapus Sasaran
                    $get_sasarans = Sasaran::where('tujuan_id', $request->hapus_tujuan_id)->get();

                    foreach ($get_sasarans as $get_sasaran) {
                        $pivot_perubahan_sasarans = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->get();
                        foreach ($pivot_perubahan_sasarans as $pivot_perubahan_sasaran) {
                            PivotPerubahanSasaran::find($pivot_perubahan_sasaran->id)->delete();
                        }

                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $get_sasaran->id)->get();
                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                            $pivot_sasaran_indikator_program_rpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                            foreach ($pivot_sasaran_indikator_program_rpjmds as $pivot_sasaran_indikator_program_rpjmd) {
                                PivotSasaranIndikatorProgramRpjmd::find($pivot_sasaran_indikator_program_rpjmd->id)->delete();
                            }

                            $sasaran_target_satuan_rp_realisasies = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)->get();
                            foreach ($sasaran_target_satuan_rp_realisasies as $sasaran_target_satuan_rp_realisasi) {
                                SasaranTargetSatuanRpRealisasi::find($sasaran_target_satuan_rp_realisasi->id)->delete();
                            }

                            SasaranIndikatorKinerja::find($sasaran_indikator_kinerja->id)->delete();
                        }

                        $sasaran_pds = SasaranPd::where('sasaran_id', $get_sasaran->id)->get();
                        foreach ($sasaran_pds as $sasaran_pd) {
                            $pivot_perubahan_sasaran_pds = PivotPerubahanSasaranPd::where('sasaran_id', $sasaran_pd->id)->get();
                            foreach ($pivot_perubahan_sasaran_pds as $pivot_perubahan_sasaran_pd) {
                                PivotPerubahanSasaranPd::find($pivot_perubahan_sasaran_pd->id)->delete();
                            }

                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd->id)->get();
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $sasaran_pd_target_satuan_rp_realisasies = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->get();
                                foreach ($sasaran_pd_target_satuan_rp_realisasies as $sasaran_pd_target_satuan_rp_realisasi) {
                                    $sasaran_pd_realisasi_renjas = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $sasaran_pd_target_satuan_rp_realisasi->id)->get();
                                    foreach ($sasaran_pd_realisasi_renjas as $sasaran_pd_realisasi_renja) {
                                        SasaranPdRealisasiRenja::find($sasaran_pd_realisasi_renja->id)->delete();
                                    }

                                    SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
                                }

                                SasaranPdIndikatorKinerja::find($sasaran_pd_indikator_kinerja->id)->delete();
                            }

                            $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd->id)->get();
                            foreach ($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd) {
                                SasaranPdProgramRpjmd::find($sasaran_pd_program_rpjmd->id)->delete();
                            }

                            SasaranPd::find($sasaran_pd->id)->delete();
                        }

                        Sasaran::find($get_sasaran->id)->delete();
                    }

                    Tujuan::find($request->hapus_tujuan_id)->delete();
                }
            }
        }

        Alert::success('Berhasil', 'Berhasil Menghapus Tujuan');
        return redirect()->route('admin.perencanaan.index');
    }
}
