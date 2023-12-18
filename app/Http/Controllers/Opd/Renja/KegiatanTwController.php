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

class KegiatanTwController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_target_satuan_rp_realisasi_id' => 'required',
            'tw_id' => 'required',
            'realisasi' => 'required',
            'realisasi_rp' => 'required',
            'sasaran_id' => 'required',
            'program_id' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan_tw_realisasi = new KegiatanTwRealisasi;
        $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi_id = $request->kegiatan_target_satuan_rp_realisasi_id;
        $kegiatan_tw_realisasi->tw_id = $request->tw_id;
        $kegiatan_tw_realisasi->realisasi = $request->realisasi;
        $kegiatan_tw_realisasi->realisasi_rp = $request->realisasi_rp;
        $kegiatan_tw_realisasi->sasaran_id = $request->sasaran_id;
        $kegiatan_tw_realisasi->program_id = $request->program_id;
        $kegiatan_tw_realisasi->save();

        $tahun = $request->tahun;
        $html = '';
        $tws = MasterTw::all();
        $idKegiatan = $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->kegiatan_id;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)->whereHas('kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }
        $getKegiatan = Kegiatan::find($kegiatan['id']);
        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
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

        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('id', $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->id)->where('kegiatan_id', $kegiatan['id'])->first();

        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
        })->where('tahun', $tahun)->first();

        $html .= '<tr>';
        $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
        $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
        $html .= '<td>'.$tahun.'</td>';
        $d = 1;
        foreach ($tws as $tw) {
            if($d == 1)
            {
                    $html .= '<td>'.$tw->nama.'</td>';
                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                        ->where('sasaran_id', $sasaran['id'])
                                                        ->where('program_id', $program['id'])
                                                        ->where('tw_id', $tw->id)->first();
                    if($cek_kegiatan_tw_realisasi_renja)
                    {
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                    </button>
                                </td>';
                    } else {
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
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
                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                        ->where('sasaran_id', $sasaran['id'])
                                                        ->where('program_id', $program['id'])
                                                        ->where('tw_id', $tw->id)->first();
                    if($cek_kegiatan_tw_realisasi_renja)
                    {
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                    </button>
                                </td>';
                    } else {
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                    </button>
                                </td>';
                    }
                $html .= '</tr>';
            }
            $d++;
        }

        $getKegiatanTargetSatuanRpRealisasi = KegiatanTargetSatuanRpRealisasi::find($kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi_id);

        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html, 'kegiatan_target_satuan_rp_realisasi_id' => $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi_id, 'tahun' => $tahun]);
    }

    public function ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_tw_realisasi_id' => 'required',
            'kegiatan_edit_realisasi' => 'required',
            'kegiatan_edit_realisasi_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renja.index');
        }

        $kegiatan_tw_realisasi = KegiatanTwRealisasi::find($request->kegiatan_tw_realisasi_id);
        $kegiatan_tw_realisasi->realisasi = $request->kegiatan_edit_realisasi;
        $kegiatan_tw_realisasi->realisasi_rp = $request->kegiatan_edit_realisasi_rp;
        $kegiatan_tw_realisasi->save();

        $tahun = $request->kegiatan_tw_tahun;
        $html = '';
        $tws = MasterTw::all();
        $idKegiatan = $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->kegiatan_id;

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)->whereHas('kegiatan_indikator_kinerja', function($q){
            $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }
        $getKegiatan = Kegiatan::find($kegiatan['id']);
        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
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

        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('id', $kegiatan_tw_realisasi->kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->id)->where('kegiatan_id', $kegiatan['id'])->first();

        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
            $q->where('opd_id', Auth::user()->opd->opd_id);
            $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
        })->where('tahun', $tahun)->first();

        $html .= '<tr>';
        $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
        $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
        $html .= '<td>'.$tahun.'</td>';
        $d = 1;
        foreach ($tws as $tw) {
            if($d == 1)
            {
                    $html .= '<td>'.$tw->nama.'</td>';
                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                        ->where('sasaran_id', $sasaran['id'])
                                                        ->where('program_id', $program['id'])
                                                        ->where('tw_id', $tw->id)->first();
                    if($cek_kegiatan_tw_realisasi_renja)
                    {
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                    </button>
                                </td>';
                    } else {
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
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
                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                        ->where('sasaran_id', $sasaran['id'])
                                                        ->where('program_id', $program['id'])
                                                        ->where('tw_id', $tw->id)->first();
                    if($cek_kegiatan_tw_realisasi_renja)
                    {
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                        $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                    </button>
                                </td>';
                    } else {
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                        $html .= '<td>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                    </button>
                                </td>';
                    }
                $html .= '</tr>';
            }
            $d++;
        }

        return response()->json(['success' => 'Berhasil Merubah Realisasi Kegiatan', 'html' => $html, 'kegiatan_target_satuan_rp_realisasi_id' => $request->kegiatan_tw_kegiatan_target_satuan_rp_realisasi_id, 'tahun' => $tahun]);
        // Alert::success('Berhasil', 'Berhasil Merubah Realisasi Kegiatan');
        // return redirect()->route('opd.renja.index');
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::find($request->kegiatan_target_satuan_rp_realisasi_id);
        $kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan = $request->kegiatan_edit_target_anggaran_perubahan;
        $kegiatan_target_satuan_rp_realisasi->target_rp_renja = $request->kegiatan_edit_target_rp_renja;
        $kegiatan_target_satuan_rp_realisasi->save();

        $html = '';
        $tws = MasterTw::all();

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->kegiatan_id)
                            ->whereHas('kegiatan_indikator_kinerja', function($q){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                });
                            })->get();
        $kegiatan = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatan = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatan = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }

        $idProgram = Kegiatan::find($kegiatan['id'])->program_id;

        $get_programs = Program::where('id', $idProgram)->whereHas('program_rpjmd', function($q){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) {
                $q->whereHas('sasaran_indikator_kinerja', function($q){
                    $q->whereHas('sasaran');
                });
            });
        })->whereHas('program_indikator_kinerja', function($q){
            $q->whereHas('opd_program_indikator_kinerja', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();
        $program = [];
        foreach($get_programs as $get_program)
        {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
            if($cek_perubahan_program)
            {
                $program = [
                    'id' => $cek_perubahan_program->program_id,
                    'kode' => $cek_perubahan_program->kode,
                    'deskripsi' => $cek_perubahan_program->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan
                ];
            } else {
                $program = [
                    'id' => $get_program->id,
                    'kode' => $get_program->kode,
                    'deskripsi' => $get_program->deskripsi,
                    'tahun_perubahan' => $get_program->tahun_perubahan
                ];
            }
        }

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($program){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($program){
                $q->whereHas('program_rpjmd', function($q) use ($program){
                    $q->whereHas('program', function($q) use ($program){
                        $q->where('id', $program['id']);
                        $q->whereHas('program_indikator_kinerja', function($q){
                            $q->whereHas('opd_program_indikator_kinerja', function($q){
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $sasaran = [];
        foreach ($get_sasarans as $get_sasaran) {
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->where('tahun_perubahan', $tahun_sekarang)
                                        ->latest()->first();
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

        $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
        $no_kegiatan_indikator_kinerja = 1;
        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun)
                {
                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                        $q->where('opd_id', Auth::user()->opd->opd_id);
                        $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                    })->where('tahun', $tahun)->first();
                    if($cek_kegiatan_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2, ',', '.').'</td>';
                                $html .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja,2, ',', '.').'</td>';
                                $html .= '<td>Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2, ',', '.').'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-kegiatan-edit-target-satuan-rp-realisasi"
                                                    type="button"
                                                    data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-target-anggaran-perubahan="'.$cek_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-target-rp-renja="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja.'"
                                                    title="Edit Anggaran Perubahan">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-tw-realisasi '.$tahun.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                                </button>
                                        </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target Kinerja</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi Kinerja</th>
                                                    <th>Realisasi Anggaran</th>
                                                    <th width="5%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyKegiatanIndikator'.$cek_kegiatan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('sasaran_id', $sasaran['id'])
                                                                                                    ->where('program_id', $program['id'])
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
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
                                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('sasaran_id', $sasaran['id'])
                                                                                                    ->where('program_id', $program['id'])
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                            $html .= '</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2, ',', '.').'</td>';
                                $html .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja,2, ',', '.').'</td>';
                                $html .= '<td>Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan,2, ',', '.').'</td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mr-2 button-kegiatan-edit-target-satuan-rp-realisasi"
                                                    type="button"
                                                    data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"
                                                    data-tahun="'.$tahun.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-target-anggaran-perubahan="'.$cek_kegiatan_target_satuan_rp_realisasi->target_anggaran_perubahan.'"
                                                    data-kegiatan-target-satuan-rp-realisasi-target-rp-renja="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp_renja.'"
                                                    title="Edit Anggaran Perubahan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                            <button type="button"
                                                class="btn btn-icon btn-primary waves-effect waves-light btn-open-kegiatan-tw-realisasi '.$tahun.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                data-tahun="'.$tahun.'"
                                                data-kegiatan-target-satuan-rp-realisasi-id="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                value="close"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'"
                                                class="accordion-toggle">
                                                    <i class="fas fa-chevron-right"></i>
                                            </button>
                                            </td>';
                            $html .='</tr>
                            <tr>
                                <td colspan="10" class="hiddenRow">
                                    <div class="collapse accordion-body" id="kegiatan_indikator_'.$cek_kegiatan_target_satuan_rp_realisasi->id.'">
                                        <table class="table table-striped table-condesed">
                                            <thead>
                                                <tr>
                                                    <th>Target</th>
                                                    <th>Satuan</th>
                                                    <th>Tahun</th>
                                                    <th>TW</th>
                                                    <th>Realisasi</th>
                                                    <th>Realisasi Rp</th>
                                                    <th width="5%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyKegiatanIndikator'.$cek_kegiatan_target_satuan_rp_realisasi->id.''.$tahun.'">';
                                                $html .= '<tr>';
                                                    $html .= '<td>'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $d = 1;
                                                    foreach ($tws as $tw) {
                                                        if($d == 1)
                                                        {
                                                                $html .= '<td>'.$tw->nama.'</td>';
                                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('sasaran_id', $sasaran['id'])
                                                                                                    ->where('program_id', $program['id'])
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
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
                                                                $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                                                    ->where('sasaran_id', $sasaran['id'])
                                                                                                    ->where('program_id', $program['id'])
                                                                                                    ->where('tw_id', $tw->id)->first();
                                                                if($cek_kegiatan_tw_realisasi_renja)
                                                                {
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi.'</span></td>';
                                                                    $html .= '<td><span class="span-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' data-kegiatan-tw-realisasi-renja-'.$cek_kegiatan_tw_realisasi_renja->id.'">'.$cek_kegiatan_tw_realisasi_renja->realisasi_rp.'</span></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-edit-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-kegiatan-tw-realisasi-renja-id="'.$cek_kegiatan_tw_realisasi_renja->id.'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                } else {
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td><input type="number" class="form-control input-kegiatan-tw-realisasi-renja-realisasi-rp '.$tw->id.' data-kegiatan-target-satuan-rp-realisasi-'.$cek_kegiatan_target_satuan_rp_realisasi->id.' '.$sasaran['id'].' '.$program['id'].'"></td>';
                                                                    $html .= '<td>
                                                                                <button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-add-kegiatan-target-satuan-rp-realisasi" type="button" data-tw-id = "'.$tw->id.'" data-kegiatan-target-satuan-rp-realisasi-id = "'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'" data-program-id="'.$program['id'].'" data-tahun="'.$tahun.'">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                                </button>
                                                                            </td>';
                                                                }
                                                            $html .= '</tr>';
                                                        }
                                                        $d++;
                                                    }
                                            $html .= '</tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td>
                                            <button class="btn btn-danger btn-icon waves-effect waves-light" type="button"><i class="fas fa-xmark"></i></button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
            $html .= '</tr>';
        }

        return response()->json(['success' => 'Berhasil Merubah Data', 'html' => $html, 'kegiatan_id' => $kegiatan['id']]);
        // Alert::success('Berhasil', 'Berhasil Merubah Data');
        // return redirect()->route('opd.renja.index');
    }
}
