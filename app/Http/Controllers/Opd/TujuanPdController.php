<?php

namespace App\Http\Controllers\Opd;

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
use Auth;
use Carbon\Carbon;
use App\Models\TahunPeriode;
use App\Models\TujuanPd;
use App\Models\PivotPerubahanTujuanPd;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\TujuanPdIndikatorKinerja;
use App\Models\TujuanPdTargetSatuanRpRealisasi;
use App\Models\TujuanPdRealisasiRenja;

class TujuanPdController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_tujuan_pd_tujuan_id' => 'required',
            'tambah_tujuan_pd_kode' => 'required',
            'tambah_tujuan_pd_deskripsi' => 'required',
            'tambah_tujuan_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $tujuan_pd = new TujuanPd;
        $tujuan_pd->tujuan_id = $request->tambah_tujuan_pd_tujuan_id;
        $tujuan_pd->kode = $request->tambah_tujuan_pd_kode;
        $tujuan_pd->deskripsi = $request->tambah_tujuan_pd_deskripsi;
        $tujuan_pd->opd_id = Auth::user()->opd->opd_id;
        $tujuan_pd->tahun_perubahan = $request->tambah_tujuan_pd_tahun_perubahan;
        $tujuan_pd->save();

        $idTujuan = $tujuan_pd->tujuan_id;
        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $idTujuan)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan Indikator Kinerja Tujuan PD', 'html' => $html, 'tujuan_id' => $idTujuan]);
        // Alert::success('Berhasil', 'Berhasil menambahkan Tujuan PD');
        // return redirect()->route('opd.renstra.index');
    }

    public function edit($id)
    {
        $data = TujuanPd::find($id);
        $array = [];
        $cek_perubahan_tujuan_pd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $id)->latest()->first();
        if($cek_perubahan_tujuan_pd)
        {
            $array = [
                'kode' => $cek_perubahan_tujuan_pd->kode,
                'deskripsi' => $cek_perubahan_tujuan_pd->deskripsi,
                'tahun_perubahan' => $cek_perubahan_tujuan_pd->tahun_perubahan,
            ];
        } else {
            $array = [
                'kode' => $data->kode,
                'deskripsi' => $data->deskripsi,
                'tahun_perubahan' => $data->tahun_perubahan,
            ];
        }

        return response()->json(['result' => $array]);
    }

    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'edit_tujuan_pd_tujuan_pd_id' => 'required',
            'edit_tujuan_pd_tujuan_id' => 'required',
            'edit_tujuan_pd_kode' => 'required',
            'edit_tujuan_pd_deskripsi' => 'required',
            'edit_tujuan_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $cek_tujuan_pd = TujuanPd::where('id', $request->edit_tujuan_pd_tujuan_pd_id)
                            ->where('tahun_perubahan', $request->edit_tujuan_pd_tahun_perubahan)
                            ->first();
        if($cek_tujuan_pd)
        {
            $update_tujuan_pd = TujuanPd::find($request->edit_tujuan_pd_tujuan_pd_id);
            $update_tujuan_pd->kode = $request->edit_tujuan_pd_kode;
            $update_tujuan_pd->deskripsi = $request->edit_tujuan_pd_deskripsi;
            $update_tujuan_pd->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
            $update_tujuan_pd->save();

            $idTujuan = $update_tujuan_pd->tujuan_id;
        } else {
            $cek_pivot = PivotPerubahanTujuanPd::where('tujuan_pd_id', $request->edit_tujuan_pd_tujuan_pd_id)
                            ->where('tahun_perubahan', $request->edit_tujuan_pd_tahun_perubahan)->first();
            if($cek_pivot)
            {
                PivotPerubahanTujuanPd::find($cek_pivot->id)->delete();

                $pivot = new PivotPerubahanTujuanPd;
                $pivot->tujuan_pd_id = $request->edit_tujuan_pd_tujuan_pd_id;
                $pivot->tujuan_id = $request->edit_tujuan_pd_tujuan_id;
                $pivot->kode = $request->edit_tujuan_pd_kode;
                $pivot->deskripsi = $request->edit_tujuan_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
                $pivot->save();
                $idTujuan = $pivot->tujuan_id;
            } else {
                $pivot = new PivotPerubahanTujuanPd;
                $pivot->tujuan_pd_id = $request->edit_tujuan_pd_tujuan_pd_id;
                $pivot->tujuan_id = $request->edit_tujuan_pd_tujuan_id;
                $pivot->kode = $request->edit_tujuan_pd_kode;
                $pivot->deskripsi = $request->edit_tujuan_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_tujuan_pd_tahun_perubahan;
                $pivot->save();
                $idTujuan = $pivot->tujuan_id;
            }
        }

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $idTujuan)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan Indikator Kinerja Tujuan PD', 'html' => $html, 'tujuan_id' => $idTujuan]);

        // Alert::success('Berhasil', 'Berhasil merubah data Tujuan PD');
        // return redirect()->route('opd.renstra.index');
    }

    public function hapus(Request $request)
    {
        $idTujuan = TujuanPd::find($request->tujuan_pd_id)->tujuan_id;
        $pivot_perubahan_tujuan_pds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $request->tujuan_pd_id)->get();
        foreach ($pivot_perubahan_tujuan_pds as $pivot_perubahan_tujuan_pd) {
            PivotPerubahanTujuanPd::find($pivot_perubahan_tujuan_pd->id)->delete();
        }

        $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $request->tujuan_pd_id)->get();
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
            $tujuan_pd_target_satuan_rp_realisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)->get();
            foreach ($tujuan_pd_target_satuan_rp_realisasis as $tujuan_pd_target_satuan_rp_realisasi) {
                TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
            }

            TujuanPdIndikatorKinerja::find($tujuan_pd_indikator_kinerja->id)->delete();
        }

        TujuanPd::find($request->tujuan_pd_id)->delete();

        // return response()->json(['success' => 'Berhasil menghapus data']);

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $idTujuan)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menghapus data', 'html' => $html, 'tujuan_id' => $idTujuan]);
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_tujuan_pd_tujuan_pd_id' => 'required',
            'indikator_kinerja_tujuan_pd_deskripsi' => 'required',
            'indikator_kinerja_tujuan_pd_satuan' => 'required',
            'indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal' => 'required',
            'indikator_kinerja_tujuan_pd_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $tujuan_pd_indikator_kinerja = new TujuanPdIndikatorKinerja;
        $tujuan_pd_indikator_kinerja->tujuan_pd_id = $request->indikator_kinerja_tujuan_pd_tujuan_pd_id;
        $tujuan_pd_indikator_kinerja->deskripsi = $request->indikator_kinerja_tujuan_pd_deskripsi;
        $tujuan_pd_indikator_kinerja->satuan = $request->indikator_kinerja_tujuan_pd_satuan;
        $tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal;
        $tujuan_pd_indikator_kinerja->status_indikator = $request->indikator_kinerja_tujuan_pd_status_indikator;
        $tujuan_pd_indikator_kinerja->save();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan Indikator Kinerja Tujuan PD', 'html' => $html, 'tujuan_id' => $tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id]);

        // Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Tujuan PD');
        // return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_edit($id)
    {
        $data = TujuanPdIndikatorKinerja::find($id);
        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_tujuan_pd_id' => 'required',
            'edit_indikator_kinerja_tujuan_pd_deskripsi' => 'required',
            'edit_indikator_kinerja_tujuan_pd_satuan' => 'required',
            'edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal' => 'required',
            'edit_indikator_kinerja_tujuan_pd_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $tujuan_pd_indikator_kinerja = TujuanPdIndikatorKinerja::find($request->indikator_kinerja_tujuan_pd_id);
        $tujuan_pd_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_tujuan_pd_deskripsi;
        $tujuan_pd_indikator_kinerja->satuan = $request->edit_indikator_kinerja_tujuan_pd_satuan;
        $tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal;
        $tujuan_pd_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_tujuan_pd_status_indikator;
        $tujuan_pd_indikator_kinerja->save();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil Merubah Indikator Kinerja Tujuan PD', 'html' => $html, 'tujuan_id' => $tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id]);
        // Alert::success('Berhasil', 'Berhasil Merubah Indikator Kinerja Tujuan PD');
        // return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $idTujuan = TujuanPdIndikatorKinerja::find($request->tujuan_pd_indikator_kinerja_id)->tujuan_pd->tujuan_id;
        $tujuan_pd_target_satuan_rp_realisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $request->tujuan_pd_indikator_kinerja_id)->get();
        foreach ($tujuan_pd_target_satuan_rp_realisasis as $tujuan_pd_target_satuan_rp_realisasi) {
            TujuanPdTargetSatuanRpRealisasi::find($tujuan_pd_target_satuan_rp_realisasi->id)->delete();
        }

        TujuanPdIndikatorKinerja::find($request->tujuan_pd_indikator_kinerja_id)->delete();

        // return response()->json(['success' => 'Berhasil menghapus data']);
        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_tujuans = Tujuan::where('id', $idTujuan)->whereHas('sasaran', function($q){
            $q->whereHas('sasaran_indikator_kinerja', function($q){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q){
                    $q->whereHas('program_rpjmd', function($q){
                        $q->whereHas('program', function($q){
                            $q->whereHas('program_indikator_kinerja', function($q){
                                $q->whereHas('opd_program_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            });
                        });
                    });
                });
            });
        })->orderBy('kode', 'asc')->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)
                                        ->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()
                                        ->first();
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

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();

        $tujuan_pds = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pds[] = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pds[] = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($tujuan_pds as $tujuan_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'" class="accordion-toggle">'.$tujuan_pd['deskripsi'].'</td>';
                $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.' (';
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>'.')';
                                }
                                if($tujuan_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>'.')';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($tujuan_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-tujuan-indikator-kinerja mr-1" data-id="'.$tujuan_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Tujuan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-tujuan-indikator-kinerja" type="button" title="Hapus Indikator" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td><button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Edit Tujuan PD"><i class="fas fa-edit"></i></button>
                <button class="btn btn-icon btn-danger waves-effect waves-light hapus-tujuan-pd" data-tujuan-pd-id="'.$tujuan_pd['id'].'" data-tujuan-id="'.$tujuan['id'].'" type="button" title="Hapus Tujuan PD"><i class="fas fa-trash"></i></button>
                <button class="btn btn-icon btn-warning waves-effect waves-light tambah-tujuan-pd-indikator-kinerja" data-tujuan-pd-id="'.$tujuan_pd['id'].'" type="button" title="Tambah Tujuan PD Indikator Kinerja"><i class="fas fa-lock"></i></button></td>';
            $html .= '</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="tujuan_tujuan_pd_indikator_kinerja'.$tujuan_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator Kinerja</th>
                                    <th width="10%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="10%">Kondisi Kinerja Akhir</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyTujuanTujuanPdIndikatorKinerja'.$tujuan_pd['id'].'">';
                            $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
                            $no_tujuan_pd_indikator_kinerja = 1;
                            foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_tujuan_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_tujuan_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($b == count($tahuns))
                                                    {
                                                        if($cek_tujuan_pd_realisasi_renja)
                                                        {
                                                            $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                                        } else {
                                                            $html .= '<td></td>';
                                                        }
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td></td>';
                                                    if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                        }
                                        $b++;
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil Merubah Indikator Kinerja Tujuan PD', 'html' => $html, 'tujuan_id' => $idTujuan]);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'tujuan_pd_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_pd_target_satuan_rp_realisasi = new TujuanPdTargetSatuanRpRealisasi;
        $tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja_id = $request->tujuan_pd_indikator_kinerja_id;
        $tujuan_pd_target_satuan_rp_realisasi->target = $request->target;
        $tujuan_pd_target_satuan_rp_realisasi->tahun = $request->tahun;
        $tujuan_pd_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $html = '';

        $get_tujuan_pds = TujuanPd::where('id', $tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja->tujuan_pd_id)->get();

        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pd = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pd = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }

        $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
        $no_tujuan_pd_indikator_kinerja = 1;
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($b == count($tahuns))
                                {
                                    if($cek_tujuan_pd_realisasi_renja)
                                    {
                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                    } else {
                                        $html .= '<td></td>';
                                    }
                                } else {
                                    $html .= '<td></td>';
                                }
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        }
                    }
                    $b++;
                }
        }

        return response()->json(['success' => 'Berhasil menambah target', 'html' => $html, 'tujuan_pd_id' => $tujuan_pd['id']]);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_target_satuan_rp_realisasi' => 'required',
            'tujuan_pd_edit_target' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::find($request->tujuan_pd_target_satuan_rp_realisasi);
        $tujuan_pd_target_satuan_rp_realisasi->target = $request->tujuan_pd_edit_target;
        $tujuan_pd_target_satuan_rp_realisasi->save();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $html = '';

        $get_tujuan_pds = TujuanPd::where('id', $tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja->tujuan_pd_id)->get();

        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pd = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_id' => $cek_perubahan_tujuan_opd->tujuan_id,
                    'kode' => $cek_perubahan_tujuan_opd->kode,
                    'deskripsi' => $cek_perubahan_tujuan_opd->deskripsi,
                    'opd_id' => $cek_perubahan_tujuan_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_tujuan_opd->tahun_perubahan,
                ];
            } else {
                $tujuan_pd = [
                    'id' => $get_tujuan_pd->id,
                    'tujuan_id' => $get_tujuan_pd->tujuan_id,
                    'kode' => $get_tujuan_pd->kode,
                    'deskripsi' => $get_tujuan_pd->deskripsi,
                    'opd_id' => $get_tujuan_pd->opd_id,
                    'tahun_perubahan' => $get_tujuan_pd->tahun_perubahan,
                ];
            }
        }

        $tujuan_pd_indikator_kinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $tujuan_pd['id'])->get();
        $no_tujuan_pd_indikator_kinerja = 1;
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="tujuan-pd-span-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'">'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($b == count($tahuns))
                                {
                                    if($cek_tujuan_pd_realisasi_renja)
                                    {
                                        $html .= '<td>'.$cek_tujuan_pd_realisasi_renja->realisasi.'</td>';
                                    } else {
                                        $html .= '<td></td>';
                                    }
                                } else {
                                    $html .= '<td></td>';
                                }
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control tujuan-pd-add-target '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                                if($tujuan_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-target-satuan-rp-realisasi" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        }
                    }
                    $b++;
                }
        }

        return response()->json(['success' => 'Berhasil mengubah target', 'html' => $html, 'tujuan_pd_id' => $tujuan_pd['id']]);

        // Alert::success('Berhasil', 'Berhasil Merubah Target Tujuan PD');
        // return redirect()->route('opd.renstra.index');
    }
}
