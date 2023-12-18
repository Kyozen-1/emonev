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
use App\Models\SasaranTwRealisasi;

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
            $idSasaran = $pivot->sasaran_id;
        } else {
            $sasaran = new Sasaran;
            $sasaran->tujuan_id = $request->sasaran_tujuan_id;
            $sasaran->kode = $request->sasaran_kode;
            $sasaran->deskripsi = $request->sasaran_deskripsi;
            $sasaran->kabupaten_id = 62;
            $sasaran->tahun_perubahan = $request->sasaran_tahun_perubahan;
            $sasaran->save();
            $idSasaran = $sasaran->id;
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();
        $getSasaran = Sasaran::find($idSasaran);

        $get_tujuans = Tujuan::where('id', $getSasaran->tujuan_id)->get();

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $getSasaran->tujuan->misi_id)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan sasaran','html' => $html]);
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
                            Tahun: '.$data->tahun_perubahan.'<br>
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
                            Kode Tujuan: '.$kode_tujuan.' <br>
                            Kode: '.$get_perubahan->kode.'<br>
                            Deskripsi: '.$get_perubahan->deskripsi.'<br>
                            Tahun: '.$get_perubahan->tahun_perubahan.'<br>
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
        $cek_pivot = PivotPerubahanSasaran::where('sasaran_id', $request->sasaran_hidden_id)
                        ->where('tujuan_id', $request->sasaran_tujuan_id)
                        ->where('kode', $request->sasaran_kode)
                        ->where('tahun_perubahan', $request->tahun_perubahan)
                        ->first();
        if($cek_pivot)
        {
            $pivot_perubahan_sasaran = new PivotPerubahanSasaran;
            $pivot_perubahan_sasaran->sasaran_id = $request->sasaran_hidden_id;
            $pivot_perubahan_sasaran->tujuan_id = $request->sasaran_tujuan_id;
            $pivot_perubahan_sasaran->kode = $request->sasaran_kode;
            $pivot_perubahan_sasaran->deskripsi = $request->sasaran_deskripsi;
            $pivot_perubahan_sasaran->kabupaten_id = 62;
            $pivot_perubahan_sasaran->tahun_perubahan = $request->sasaran_tahun_perubahan;
            $pivot_perubahan_sasaran->save();

            PivotPerubahanSasaran::find($cek_pivot->id)->delete();
        } else {
            $pivot_perubahan_sasaran = new PivotPerubahanSasaran;
            $pivot_perubahan_sasaran->sasaran_id = $request->sasaran_hidden_id;
            $pivot_perubahan_sasaran->tujuan_id = $request->sasaran_tujuan_id;
            $pivot_perubahan_sasaran->kode = $request->sasaran_kode;
            $pivot_perubahan_sasaran->deskripsi = $request->sasaran_deskripsi;
            $pivot_perubahan_sasaran->kabupaten_id = 62;
            $pivot_perubahan_sasaran->tahun_perubahan = $request->sasaran_tahun_perubahan;
            $pivot_perubahan_sasaran->save();
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();
        $getSasaran = Sasaran::find($request->sasaran_hidden_id);

        $get_tujuans = Tujuan::where('id', $getSasaran->tujuan_id)->get();
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $getSasaran->tujuan->misi_id)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah sasaran','html' => $html]);
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

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sasaran_sasaran_id' => 'required',
            'indikator_kinerja_sasaran_deskripsi' => 'required',
            'indikator_kinerja_sasaran_satuan' => 'required',
            'indikator_kinerja_sasaran_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $indikator_kinerja = new SasaranIndikatorKinerja;
        $indikator_kinerja->sasaran_id = $request->indikator_kinerja_sasaran_sasaran_id;
        $indikator_kinerja->deskripsi = $request->indikator_kinerja_sasaran_deskripsi;
        $indikator_kinerja->satuan = $request->indikator_kinerja_sasaran_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_sasaran_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();
        $getSasaran = Sasaran::find($indikator_kinerja->sasaran_id);

        $get_tujuans = Tujuan::where('id', $getSasaran->tujuan_id)->get();
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $getSasaran->tujuan->misi_id)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }

        return response()->json(['success' => 'Berhasil menambah indikator sasaran','html' => $html, 'tujuan_id' => $tujuan['id']]);
    }

    public function indikator_kinerja_edit($id)
    {
        $data = SasaranIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_indikator_kinerja_sasaran_id' => 'required',
            'edit_indikator_kinerja_sasaran_deskripsi' => 'required',
            'edit_indikator_kinerja_sasaran_satuan' => 'required',
            'edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $indikator_kinerja = SasaranIndikatorKinerja::find($request->edit_indikator_kinerja_sasaran_id);
        $indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_sasaran_deskripsi;
        $indikator_kinerja->satuan = $request->edit_indikator_kinerja_sasaran_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();
        $getSasaran = Sasaran::find($indikator_kinerja->sasaran_id);

        $get_tujuans = Tujuan::where('id', $getSasaran->tujuan_id)->get();
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $getSasaran->tujuan->misi_id)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }
        return response()->json(['success' => 'Berhasil merubah indikator sasaran','html' => $html, 'tujuan_id' => $tujuan['id']]);
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $idSasaran = SasaranIndikatorKinerja::find($request->sasaran_indikator_kinerja_id)->sasaran_id;
        $sasaran_target_satuan_rp_realisasies = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $request->sasaran_indikator_kinerja_id)->get();
        foreach ($sasaran_target_satuan_rp_realisasies as $sasaran_target_satuan_rp_realisasi) {
            SasaranTargetSatuanRpRealisasi::find($sasaran_target_satuan_rp_realisasi->id)->delete();
        }
        SasaranIndikatorKinerja::find($request->sasaran_indikator_kinerja_id)->delete();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();
        $getSasaran = Sasaran::find($idSasaran);

        $get_tujuans = Tujuan::where('id', $getSasaran->tujuan_id)->get();
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $getSasaran->tujuan->misi_id)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah indikator sasaran','html' => $html, 'tujuan_id' => $tujuan['id']]);
    }

    public function store_sasaran_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'sasaran_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_target_satuan_rp_realisasi = new SasaranTargetSatuanRpRealisasi;
        $sasaran_target_satuan_rp_realisasi->sasaran_indikator_kinerja_id = $request->sasaran_indikator_kinerja_id;
        $sasaran_target_satuan_rp_realisasi->target = $request->target;
        $sasaran_target_satuan_rp_realisasi->tahun = $request->tahun;
        $sasaran_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();
        // Table Of Content
        $getSasaranIndikatorKinerja = SasaranIndikatorKinerja::find($request->sasaran_indikator_kinerja_id);
        $get_sasarans = Sasaran::where('id', $getSasaranIndikatorKinerja->sasaran_id);
        $get_sasarans = $get_sasarans->get();

        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id);
            if($request->nav_rpjmd_sasaran_tahun != 'semua')
            {
                $cek_perubahan_sasaran = $cek_perubahan_sasaran->where('tahun_perubahan', $request->nav_rpjmd_sasaran_tahun);
            }
            $cek_perubahan_sasaran = $cek_perubahan_sasaran->orderBy('created_at', 'desc');
            $cek_perubahan_sasaran = $cek_perubahan_sasaran->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                ];
            } else {
                $sasaran = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                ];
            }
        }
        $html = '';

        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
        $no_sasaran_indikator_kinerja = 1;
        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
            $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_sasaran_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target Kinerja</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi Kinerja</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                $html .= '</tr>';
                                            $html .='</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target Kinerja</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi Kinerja</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                $html .= '</tr>';
                                            $html .='</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil merubah indikator sasaran','html' => $html,'sasaran_id' => $sasaran['id']]);
    }

    public function update_sasaran_target_satuan_rp_realisasi(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_target_satuan_rp_realisasi' => 'required',
            'sasaran_edit_target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::find($request->sasaran_target_satuan_rp_realisasi);
        $sasaran_target_satuan_rp_realisasi->target = $request->sasaran_edit_target;
        $sasaran_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tws = MasterTw::all();
        // Table Of Content
        $getSasaranIndikatorKinerja = SasaranIndikatorKinerja::find($request->sasaran_indikator_kinerja_id);
        $get_sasarans = Sasaran::where('id', $getSasaranIndikatorKinerja->sasaran_id);
        $get_sasarans = $get_sasarans->get();

        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id);
            if($request->nav_rpjmd_sasaran_tahun != 'semua')
            {
                $cek_perubahan_sasaran = $cek_perubahan_sasaran->where('tahun_perubahan', $request->nav_rpjmd_sasaran_tahun);
            }
            $cek_perubahan_sasaran = $cek_perubahan_sasaran->orderBy('created_at', 'desc');
            $cek_perubahan_sasaran = $cek_perubahan_sasaran->first();
            if($cek_perubahan_sasaran)
            {
                $sasaran = [
                    'id' => $cek_perubahan_sasaran->sasaran_id,
                    'kode' => $cek_perubahan_sasaran->kode,
                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan,
                ];
            } else {
                $sasaran = [
                    'id' => $get_sasaran->id,
                    'kode' => $get_sasaran->kode,
                    'deskripsi' => $get_sasaran->deskripsi,
                    'tahun_perubahan' => $get_sasaran->tahun_perubahan,
                ];
            }
        }
        $html = '';

        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
        $no_sasaran_indikator_kinerja = 1;
        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
            $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_sasaran_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target Kinerja</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi Kinerja</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                $html .= '</tr>';
                                            $html .='</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target Kinerja</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi Kinerja</th>
                                                    <th width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        } else {
                                                            $html .= '<tr>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_sasaran_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                                $html .= '</tr>';
                                            $html .='</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil merubah indikator sasaran','html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function sasaran_hapus(Request $request)
    {
        $idTujuan = Sasaran::find($request->hapus_sasaran_id)->tujuan_id;
        $idMisi = Sasaran::find($request->hapus_sasaran_id)->tujuan->misi_id;
        if($request->hapus_sasaran_tahun == 'semua')
        {
            $pivot_perubahan_sasarans = PivotPerubahanSasaran::where('sasaran_id', $request->hapus_sasaran_id)->get();
            foreach ($pivot_perubahan_sasarans as $pivot_perubahan_sasaran) {
                PivotPerubahanSasaran::find($pivot_perubahan_sasaran->id)->delete();
            }

            $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $request->hapus_sasaran_id)->get();
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

            $sasaran_pds = SasaranPd::where('sasaran_id', $request->hapus_sasaran_id)->get();
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

            Sasaran::find($request->hapus_sasaran_id)->delete();
        } else {
            $cek_perubahan_sasaran_1 = PivotPerubahanSasaran::where('sasaran_id', $request->hapus_sasaran_id)->where('tahun_perubahan', $request->hapus_sasaran_tahun)->first();
            if($cek_perubahan_sasaran_1)
            {
                PivotPerubahanSasaran::find($cek_perubahan_sasaran_1->id)->delete();
            } else {
                $cek_perubahan_sasaran = Sasaran::where('tahun_perubahan', $request->hapus_sasaran_tahun)->where('id', $request->hapus_sasaran_id)->first();
                if($cek_perubahan_sasaran)
                {
                    $pivot_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $request->hapus_sasaran_id)->first();
                    if($pivot_perubahan_sasaran)
                    {
                        $edit_sasaran = Sasaran::find($cek_sasaran->id);
                        $edit_sasaran->tahun_perubahan = $pivot_perubahan_sasaran->tahun_perubahan;
                        $edit_sasaran->save();

                        PivotPerubahanSasaran::find($pivot_perubahan_sasaran->id)->delete();
                    } else {
                        return response()->json(['errors' => 'Pilih Pilihan Hapus Semua!']);
                    }
                }

                $cek_perubahan_sasaran_2 = PivotPerubahanSasaran::where('tujuan_id', $request->hapus_sasaran_id)->first();
                if(!$cek_perubahan_sasaran_2)
                {
                    $pivot_perubahan_sasarans = PivotPerubahanSasaran::where('sasaran_id', $request->hapus_sasaran_id)->get();
                    foreach ($pivot_perubahan_sasarans as $pivot_perubahan_sasaran) {
                        PivotPerubahanSasaran::find($pivot_perubahan_sasaran->id)->delete();
                    }

                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $request->hapus_sasaran_id)->get();
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

                    $sasaran_pds = SasaranPd::where('sasaran_id', $request->hapus_sasaran_id)->get();
                }
            }
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table of Content
        $html = '';
        $tws = MasterTw::all();

        $get_tujuans = Tujuan::where('id', $idTujuan)->get();
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan
                ];
            }
        }

        $get_misis = Misi::where('id', $idMisi)->get();
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
        $sasarans = [];

        foreach ($get_sasarans as $get_sasaran)
        {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();

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

        foreach ($sasarans as $sasaran)
        {
            $html .= '<tr>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="5%">'.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</td>
                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_indikator'.$sasaran['id'].'" class="accordion-toggle" width="45%">
                            '.$sasaran['deskripsi'].'
                            <br>';
                            $html .= '<span class="badge bg-primary text-uppercase sasaran-tagging">Visi</span>';
                            $html .= ' <span class="badge bg-warning text-uppercase sasaran-tagging">Misi '.$misi['kode'].'</span>
                            <span class="badge bg-secondary text-uppercase sasaran-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                            <span class="badge bg-danger text-uppercase sasaran-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                        </td>';
                        $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                        // $html .= '<td width="30%"><ul>';
                        //     foreach($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja)
                        //     {
                        //         $html .= '<li class="mb-2">'.$sasaran_indikator_kinerja->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"></button></li>';
                        //     }
                        // $html .= '</ul></td>';
                        $html .= '<td width="28%"><table>
                                    <tbody>';
                                        foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                            $html .= '<tr>';
                                                $html .= '<td width="75%">'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                                $html .= '<td width="25%">
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-id="'.$sasaran['id'].'" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                                </td>';
                                            $html .= '</tr>';
                                        }
                                    $html .= '</tbody>
                                </table></td>';
                        $html .='<td width="22%">
                            <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light edit-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-misi-id="'.$misi['id'].'" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-indikator-kinerja" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Tambah Sasaran Indikator Kinerja"><i class="fas fa-lock"></i></button>
                            <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran" data-sasaran-id="'.$sasaran['id'].'" data-tahun="semua" type="button" title="Hapus Sasaran "><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="hiddenRow">
                            <div class="collapse accordion-body" id="sasaran_indikator'.$sasaran['id'].'">
                                <table class="table table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Indikator</th>
                                            <th width="17%">Kondisi Kinerja Awal</th>
                                            <th width="12%">Target</th>
                                            <th width="12%">Satuan</th>
                                            <th width="12%">Tahun</th>
                                            <th width="12%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodySasaranSasaran'.$sasaran['id'].'">';
                                    $sasaran_indikator_kinerjas = SasaranIndikatorKinerja::where('sasaran_id', $sasaran['id'])->get();
                                    $no_sasaran_indikator_kinerja = 1;
                                    foreach ($sasaran_indikator_kinerjas as $sasaran_indikator_kinerja) {
                                        $html .= '<tr id="trSasaranIndikatorKinerja'.$sasaran_indikator_kinerja->id.'">';
                                            $html .= '<td>'.$no_sasaran_indikator_kinerja++.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->deskripsi.'</td>';
                                            $html .= '<td>'.$sasaran_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                            $b = 1;
                                            foreach ($tahuns as $tahun) {
                                                $cek_sasaran_target_satuan_rp_realisasi = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $sasaran_indikator_kinerja->id)
                                                                                                    ->where('tahun', $tahun)->first();
                                                if($cek_sasaran_target_satuan_rp_realisasi)
                                                {
                                                    if($b == 1)
                                                    {
                                                        $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            data-tahun="'.$tahun.'"
                                                                            data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            value="close"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                            class="accordion-toggle">
                                                                                <i class="fas fa-chevron-right"></i>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><span class="sasaran-span-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'">'.$cek_sasaran_target_satuan_rp_realisasi->target.'</span></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-edit-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-target-satuan-rp-realisasi="'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                            </button>
                                                                            <button type="button"
                                                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-sasaran-tw-realisasi '.$tahun.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                data-tahun="'.$tahun.'"
                                                                                data-sasaran-target-satuan-rp-realisasi-id="'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                value="close"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'"
                                                                                class="accordion-toggle">
                                                                                    <i class="fas fa-chevron-right"></i>
                                                                            </button>
                                                                        </td>';
                                                        $html .='</tr>
                                                        <tr>
                                                            <td colspan="10" class="hiddenRow">
                                                                <div class="collapse accordion-body" id="sasaran_indikator_'.$cek_sasaran_target_satuan_rp_realisasi->id.'">
                                                                    <table class="table table-striped table-condesed">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Target Kinerja</th>
                                                                                <th>Satuan</th>
                                                                                <th>Tahun</th>
                                                                                <th>TW</th>
                                                                                <th>Realisasi Kinerja</th>
                                                                                <th width="10%">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tbodySasaranTwRealisasi'.$cek_sasaran_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                            $html .= '<tr>';
                                                                                $html .= '<td>'.$cek_sasaran_target_satuan_rp_realisasi->target.'</td>';
                                                                                $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                                                $html .= '<td>'.$tahun.'</td>';
                                                                                $d = 1;
                                                                                foreach ($tws as $tw) {
                                                                                    if($d == 1)
                                                                                    {
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    } else {
                                                                                        $html .= '<tr>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td></td>';
                                                                                            $html .= '<td>'.$tw->nama.'</td>';
                                                                                            $cek_sasaran_tw_realisasi = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $cek_sasaran_target_satuan_rp_realisasi->id)
                                                                                                                                ->where('tw_id', $tw->id)->first();
                                                                                            if($cek_sasaran_tw_realisasi)
                                                                                            {
                                                                                                $html .= '<td><span class="span-sasaran-tw-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.' data-sasaran-tw-realisasi-'.$cek_sasaran_tw_realisasi->id.'">'.$cek_sasaran_tw_realisasi->realisasi.'</span></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-sasaran-tw-realisasi-id="'.$cek_sasaran_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            } else {
                                                                                                $html .= '<td><input type="number" class="form-control input-sasaran-tw-realisasi-realisasi '.$tw->id.' data-sasaran-target-satuan-rp-realisasi-'.$cek_sasaran_target_satuan_rp_realisasi->id.'"></td>';
                                                                                                $html .= '<td>
                                                                                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-sasaran-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-sasaran-target-satuan-rp-realisasi-id = "'.$cek_sasaran_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                                            </button>
                                                                                                        </td>';
                                                                                            }
                                                                                        $html .= '</tr>';
                                                                                    }
                                                                                    $d++;
                                                                                }
                                                                            $html .= '</tr>';
                                                                        $html .='</tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    $b++;
                                                } else {
                                                    if($b == 1)
                                                    {
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    } else {
                                                        $html .= '<tr>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            $html .= '<td><input type="number" class="form-control sasaran-add-target '.$tahun.' data-sasaran-indikator-kinerja-'.$sasaran_indikator_kinerja->id.'"></td>';
                                                            $html .= '<td>'.$sasaran_indikator_kinerja->satuan.'</td>';
                                                            $html .= '<td>'.$tahun.'</td>';
                                                            $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-target-satuan-rp-realisasi" type="button" data-sasaran-indikator-kinerja-id="'.$sasaran_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                        </button>
                                                                    </td>';
                                                        $html .='</tr>';
                                                    }
                                                    $b++;
                                                }
                                            }
                                    }
                                    $html .= '</tbody>
                                </table>
                            </div>
                        </td>
                    </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah indikator sasaran','html' => $html, 'tujuan_id' => $tujuan['id']]);
    }
}
