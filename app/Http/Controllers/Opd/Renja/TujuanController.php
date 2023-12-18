<?php

namespace App\Http\Controllers\Opd\Renja;

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

class TujuanController extends Controller
{
    public function realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_target_satuan_rp_realisasi_id' => 'required',
            'realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            // return back()->with('errors', $errors->errors()->all())->withInput();
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $tujuan_pd_realisasi_renja = new TujuanPdRealisasiRenja;
        $tujuan_pd_realisasi_renja->tujuan_pd_target_satuan_rp_realisasi_id = $request->tujuan_pd_target_satuan_rp_realisasi_id;
        $tujuan_pd_realisasi_renja->realisasi = $request->realisasi;
        $tujuan_pd_realisasi_renja->save();

        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan_pd_realisasi_renja->tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id)->where('opd_id', Auth::user()->opd->opd_id)->get();
        $tujuan_pd = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pd = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_pd_id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
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
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
        {
            $html .= '<tr>';
                $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun)
                {
                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                } else {
                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
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
                                $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                } else {
                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
            $html .= '</tr>';
        }

        return response()->json(['success' => 'Berhasil menambah realisasi', 'html' => $html, 'tujuan_pd_id' => $tujuan_pd['id']]);
    }

    public function realisasi_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tujuan_pd_realisasi_renja_id' => 'required',
            'tujuan_pd_edit_realisasi' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renja.index');
        }

        $tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::find($request->tujuan_pd_realisasi_renja_id);
        $tujuan_pd_realisasi_renja->realisasi = $request->tujuan_pd_edit_realisasi;
        $tujuan_pd_realisasi_renja->save();

        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_tujuan_pds = TujuanPd::where('tujuan_id', $tujuan_pd_realisasi_renja->tujuan_pd_target_satuan_rp_realisasi->tujuan_pd_indikator_kinerja->tujuan_pd->tujuan_id)->where('opd_id', Auth::user()->opd->opd_id)->get();
        $tujuan_pd = [];
        foreach ($get_tujuan_pds as $get_tujuan_pd) {
            $cek_perubahan_tujuan_opd = PivotPerubahanTujuanPd::where('tujuan_pd_id', $get_tujuan_pd->id)->latest()->first();
            if($cek_perubahan_tujuan_opd)
            {
                $tujuan_pd = [
                    'id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
                    'tujuan_pd_id' => $cek_perubahan_tujuan_opd->tujuan_pd_id,
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
        foreach ($tujuan_pd_indikator_kinerjas as $tujuan_pd_indikator_kinerja)
        {
            $html .= '<tr>';
                $html .= '<td>'.$no_tujuan_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$tujuan_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun)
                {
                    $cek_tujuan_pd_target_satuan_rp_realisasi = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $tujuan_pd_indikator_kinerja->id)
                                                        ->where('tahun', $tahun)->first();
                    if($cek_tujuan_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                } else {
                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
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
                                $html .= '<td>'.$cek_tujuan_pd_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $cek_tujuan_pd_realisasi_renja = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $cek_tujuan_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><span class="tujuan-pd-span-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.' data-tujuan-pd-realisasi-renja-'.$cek_tujuan_pd_realisasi_renja->id.'">'.$cek_tujuan_pd_realisasi_renja->realisasi.'</span></td>';
                                } else {
                                    $html .= '<td><input type="number" class="form-control tujuan-pd-add-realisasi '.$tahun.' data-tujuan-pd-indikator-kinerja-'.$tujuan_pd_indikator_kinerja->id.' data-tujuan-pd-target-satuan-rp-realisasi-'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'"></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($cek_tujuan_pd_realisasi_renja)
                                {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-tujuan-pd-edit-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'" data-tujuan-pd-realisasi-renja-id="'.$cek_tujuan_pd_realisasi_renja->id.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-tujuan-pd-realisasi-renja" type="button" data-tujuan-pd-indikator-kinerja-id="'.$tujuan_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-tujuan-pd-target-satuan-rp-realisasi-id="'.$cek_tujuan_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tujuan_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td></td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
            $html .= '</tr>';
        }

        return response()->json(['success' => 'Berhasil merubah realisasi Tujuan PD', 'html' => $html, 'tujuan_pd_id' => $tujuan_pd['id']]);

        // Alert::success('Berhasil', 'Berhasil merubah realisasi Tujuan PD');
        // return redirect()->route('opd.renja.index');
    }
}
