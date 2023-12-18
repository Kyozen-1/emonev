<?php

namespace App\Http\Controllers\Admin\Perencanaan\Rpjmd;

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
use App\Models\SasaranTwRealisasi;

class TujuanController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_target_satuan_rp_realisasi_id' => 'required',
            'tw_id' => 'required',
            'realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_tw_realisasi = new TujuanTwRealisasi;
        $tujuan_tw_realisasi->tujuan_target_satuan_rp_realisasi_id = $request->tujuan_target_satuan_rp_realisasi_id;
        $tujuan_tw_realisasi->tw_id = $request->tw_id;
        $tujuan_tw_realisasi->realisasi = $request->realisasi;
        $tujuan_tw_realisasi->save();

        // Table Content
        $tws = MasterTw::all();

        $tahun = $request->tahun;
        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('id', $request->tujuan_target_satuan_rp_realisasi_id)->where('tahun', $tahun)->first();
        $tujuan_indikator_kinerja = TujuanIndikatorKinerja::find($cek_tujuan_target_satuan_rp_realisasi->tujuan_indikator_kinerja_id);

        $html = '<tr>';
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

        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html]);
    }

    public function ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_tw_realisasi_id' => 'required',
            'tujuan_edit_realisasi' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_tw_realisasi = TujuanTwRealisasi::find($request->tujuan_tw_realisasi_id);
        $tujuan_tw_realisasi->realisasi = $request->tujuan_edit_realisasi;
        $tujuan_tw_realisasi->save();

        // Table Content
        $tws = MasterTw::all();

        $tahun = $request->tujuan_tw_tahun;
        $cek_tujuan_target_satuan_rp_realisasi = TujuanTargetSatuanRpRealisasi::where('id', $request->tujuan_tw_tujuan_target_satuan_rp_realisasi_id)->where('tahun', $tahun)->first();
        $tujuan_indikator_kinerja = TujuanIndikatorKinerja::find($cek_tujuan_target_satuan_rp_realisasi->tujuan_indikator_kinerja_id);

        $html = '<tr>';
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

        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html]);
    }
}
