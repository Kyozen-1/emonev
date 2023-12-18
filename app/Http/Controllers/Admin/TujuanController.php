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
use App\Models\TujuanTwRealisasi;

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
            $idTujuan = $pivot->tujuan_id;
        } else {
            $tujuan = new Tujuan;
            $tujuan->misi_id = $request->tujuan_misi_id;
            $tujuan->kode = $request->tujuan_kode;
            $tujuan->deskripsi = $request->tujuan_deskripsi;
            $tujuan->kabupaten_id = 62;
            $tujuan->tahun_perubahan = $request->tujuan_tahun_perubahan;
            $tujuan->save();
            $idTujuan = $tujuan->id;
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table Of Content
        $html = '';
        $tws = MasterTw::all();
        $getTujuan = Tujuan::find($idTujuan);

        $get_misis = Misi::where('id', $getTujuan->misi_id)->get();
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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
        $tujuans = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan tujuan', 'html' => $html]);
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

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

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
        $getTujuan = Tujuan::find($request->tujuan_hidden_id);

        $get_misis = Misi::where('id', $getTujuan->misi_id)->get();
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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
        $tujuans = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah tujuan', 'html' => $html]);
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
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $indikator_kinerja = new TujuanIndikatorKinerja;
        $indikator_kinerja->tujuan_id = $request->indikator_kinerja_tujuan_tujuan_id;
        $indikator_kinerja->deskripsi = $request->indikator_kinerja_tujuan_deskripsi;
        $indikator_kinerja->satuan = $request->indikator_kinerja_tujuan_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_tujuan_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table Of Content

        $html = '';
        $tws = MasterTw::all();
        $getTujuan = Tujuan::find($request->indikator_kinerja_tujuan_tujuan_id);

        $get_misis = Misi::where('id', $getTujuan->misi_id)->get();
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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
        $tujuans = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan indikator tujuan', 'html' => $html, 'misi_id' => $misi['id']]);
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
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $indikator_kinerja = TujuanIndikatorKinerja::find($request->edit_indikator_kinerja_tujuan_id);
        $indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_tujuan_deskripsi;
        $indikator_kinerja->satuan = $request->edit_indikator_kinerja_tujuan_satuan;
        $indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal;
        $indikator_kinerja->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

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
        $getTujuan = Tujuan::find($indikator_kinerja->tujuan_id);

        $get_misis = Misi::where('id', $getTujuan->misi_id)->get();
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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
        $tujuans = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah indikator tujuan', 'html' => $html, 'misi_id' => $misi['id']]);
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $idMisi = Tujuan::find($request->tujuan_id)->misi_id;
        $tujuan_target_satuan_rp_realisasies = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $request->tujuan_indikator_kinerja_id)->get();
        foreach ($tujuan_target_satuan_rp_realisasies as $tujuan_target_satuan_rp_realisasi) {
            TujuanTargetSatuanRpRealisasi::find($tujuan_target_satuan_rp_realisasi->id)->delete();
        }
        TujuanIndikatorKinerja::find($request->tujuan_indikator_kinerja_id)->delete();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();

        $tujuans = [];

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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

        foreach ($tujuans as $tujuan)
        {
            $html .= '<tr>
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menghapus indikator tujuan', 'html' => $html, 'misi_id' => $misi['id']]);
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

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        // Table Of Content
        $tws = MasterTw::all();

        $getTujuanIndikatorKinerja = TujuanIndikatorKinerja::find($request->tujuan_indikator_kinerja_id);
        $get_tujuans = Tujuan::where('id', $getTujuanIndikatorKinerja->tujuan_id);
        $get_tujuans = $get_tujuans->get();

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id);
            if($request->nav_rpjmd_tujuan_tahun != 'semua')
            {
                $cek_perubahan_tujuan = $cek_perubahan_tujuan->where('tahun_perubahan', $request->nav_rpjmd_tujuan_tahun);
            }
            $cek_perubahan_tujuan = $cek_perubahan_tujuan->orderBy('created_at', 'desc');
            $cek_perubahan_tujuan = $cek_perubahan_tujuan->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                ];
            }
        }
        $html = '';
        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
        $no_tujuan_indikator_kinerja = 1;
        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
            $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $c = 1;
                foreach ($tahuns as $tahun) {
                    $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_target_satuan_rp_realisasi)
                    {
                        if($c == 1)
                        {
                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                            <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                            <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                        $c++;
                    } else {
                        if($c == 1)
                        {
                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $c++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil menambahkan target', 'html' => $html, 'tujuan_id' => $tujuan['id']]);
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

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        // Table of Content
        $getTujuanIndikatorKinerja = TujuanIndikatorKinerja::find($request->tujuan_indikator_kinerja_id);
        $get_tujuans = Tujuan::where('id', $getTujuanIndikatorKinerja->tujuan_id);
        $get_tujuans = $get_tujuans->get();

        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id);
            if($request->nav_rpjmd_tujuan_tahun != 'semua')
            {
                $cek_perubahan_tujuan = $cek_perubahan_tujuan->where('tahun_perubahan', $request->nav_rpjmd_tujuan_tahun);
            }
            $cek_perubahan_tujuan = $cek_perubahan_tujuan->orderBy('created_at', 'desc');
            $cek_perubahan_tujuan = $cek_perubahan_tujuan->first();
            if($cek_perubahan_tujuan)
            {
                $tujuan = [
                    'id' => $cek_perubahan_tujuan->tujuan_id,
                    'kode' => $cek_perubahan_tujuan->kode,
                    'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan,
                ];
            } else {
                $tujuan = [
                    'id' => $get_tujuan->id,
                    'kode' => $get_tujuan->kode,
                    'deskripsi' => $get_tujuan->deskripsi,
                    'tahun_perubahan' => $get_tujuan->tahun_perubahan,
                ];
            }
        }
        $html = '';
        $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
        $no_tujuan_indikator_kinerja = 1;
        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
            $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $c = 1;
                foreach ($tahuns as $tahun) {
                    $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_target_satuan_rp_realisasi)
                    {
                        if($c == 1)
                        {
                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                            <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    value="close"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                    class="accordion-toggle">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                            <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_tujuan_tw_realisasi)
                                                                {
                                                                    $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                        $c++;
                    } else {
                        if($c == 1)
                        {
                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $c++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil merubah nilai target', 'html' => $html, 'tujuan_id' => $tujuan['id']]);
    }

    public function hapus(Request $request)
    {
        $idMisi = Tujuan::find($request->hapus_tujuan_id)->misi_id;
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
        } else {
            $cek_perubahan_tujuan_1 = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->where('tahun_perubahan', $request->hapus_tujuan_tahun)->first();
            if($cek_perubahan_tujuan_1)
            {
                PivotPerubahanTujuan::find($cek_perubahan_tujuan_1->id)->delete();
            } else {
                // Logika jika malah tahun ada di tujuan bukan di pivot perubahan tujuan
                $cek_tujuan = Tujuan::where('tahun_perubahan', $request->hapus_tujuan_tahun)->where('id', $request->hapus_tujuan_id)->first();
                if($cek_tujuan)
                {
                    $pivot_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $request->hapus_tujuan_id)->first();
                    if($pivot_perubahan_kegiatan)
                    {
                        $edit_tujuan = Tujuan::find($cek_tujuan->id);
                        $edit_tujuan->tahun_perubahan = $pivot_perubahan_kegiatan->tahun_perubahan;
                        $edit_tujuan->save();
                    } else {
                        return response()->json(['errors' => 'Pilih Pilihan Hapus Semua!']);
                    }
                } else {
                    // Pengecekan jika menjadi satu - satunya
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
        }

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;

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

        $get_tujuans = Tujuan::where('misi_id', $misi['id'])->orderBy('kode', 'asc')->get();
        $tujuans = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
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
                <td width="5%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">'.$misi['kode'].'.'.$tujuan['kode'].'</td>
                <td width="45%" data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan'.$tujuan['id'].'" class="accordion-toggle">
                    '.$tujuan['deskripsi'].'
                    <br>';
                    $html .= '<span class="badge bg-primary text-uppercase tujuan-tagging">Visi</span>';
                    $html .= ' <span class="badge bg-warning text-uppercase tujuan-tagging">Misi '.$misi['kode'].'</span>
                    <span class="badge bg-secondary text-uppercase tujuan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                </td>';
                $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                $html .= '<td width="28%"><table>
                    <tbody>';
                        foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-id="'.$tujuan['id'].'" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="22%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light edit-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-indikator-kinerja" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Tambah Tujuan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan" data-tujuan-id="'.$tujuan['id'].'" data-tahun="semua" type="button" title="Hapus Tujuan "><i class="fas fa-trash"></i></button>
                </td>
            </tr>';
            $html .= '<tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan'.$tujuan['id'].'">
                        <table class="table table-striped table-condesed">
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
                            <tbody id="tbodyTujuanTujuan'.$tujuan['id'].'">';
                            $tujuan_indikator_kinerjas = TujuanIndikatorKinerja::where('tujuan_id', $tujuan['id'])->get();
                            $no_tujuan_indikator_kinerja = 1;
                            foreach ($tujuan_indikator_kinerjas as $tujuan_indikator_kinerja) {
                                $html .= '<tr id="trTujuanIndikatorKinerja'.$tujuan_indikator_kinerja->id.'">';
                                    $html .= '<td>'.$no_tujuan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $c = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $tujuan_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_target_satuan_rp_realisasi)
                                        {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    data-tahun="'.$tahun.'"
                                                                    data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    value="close"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                    class="accordion-toggle">
                                                                        <i class="fas fa-chevron-right"></i>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><span class="tujuan-span-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'">'.$cek_tujuan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-edit-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-target-satuan-rp-realisasi="'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-icon btn-primary waves-effect waves-light btn-open-tujuan-tw-realisasi '.$tahun.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        data-tahun="'.$tahun.'"
                                                                        data-tujuan-target-satuan-rp-realisasi-id="'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        value="close"
                                                                        data-bs-toggle="collapse"
                                                                        data-bs-target="#tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'"
                                                                        class="accordion-toggle">
                                                                            <i class="fas fa-chevron-right"></i>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>
                                                <tr>
                                                    <td colspan="10" class="hiddenRow">
                                                        <div class="collapse accordion-body" id="tujuan_indikator_'.$cek_tujuan_target_satuan_rp_realisasi->id.'">
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
                                                                <tbody id="tbodyTujuanTwRealisasi'.$cek_tujuan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                                    $html .= '<tr>';
                                                                        $html .= '<td>'.$cek_tujuan_target_satuan_rp_realisasi->target.'</td>';
                                                                        $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                                        $html .= '<td>'.$tahun.'</td>';
                                                                        $d = 1;
                                                                        foreach ($tws as $tw) {
                                                                            if($d == 1)
                                                                            {
                                                                                    $html .= '<td>'.$tw->nama.'</td>';
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                                                                    $cek_tujuan_tw_realisasi = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $cek_tujuan_target_satuan_rp_realisasi->id)
                                                                                                                        ->where('tw_id', $tw->id)->first();
                                                                                    if($cek_tujuan_tw_realisasi)
                                                                                    {
                                                                                        $html .= '<td><span class="span-tujuan-tw-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.' data-tujuan-tw-realisasi-'.$cek_tujuan_tw_realisasi->id.'">'.$cek_tujuan_tw_realisasi->realisasi.'</span></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tujuan-tw-realisasi-id="'.$cek_tujuan_tw_realisasi->id.'" data-tahun="'.$tahun.'">
                                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                                    </button>
                                                                                                </td>';
                                                                                    } else {
                                                                                        $html .= '<td><input type="number" class="form-control input-tujuan-tw-realisasi-realisasi '.$tw->id.' data-tujuan-target-satuan-rp-realisasi-'.$cek_tujuan_target_satuan_rp_realisasi->id.'"></td>';
                                                                                        $html .= '<td>
                                                                                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-tujuan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-tujuan-target-satuan-rp-realisasi-id = "'.$cek_tujuan_target_satuan_rp_realisasi->id.'" data-tahun="'.$tahun.'">
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
                                            $c++;
                                        } else {
                                            if($c == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-add-target '.$tahun.' data-tujuan-indikator-kinerja-'.$tujuan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-target-satuan-rp-realisasi" type="button" data-tujuan-indikator-kinerja-id="'.$tujuan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menghapus tujuan', 'html' => $html, 'misi_id' => $misi['id']]);
    }
}
