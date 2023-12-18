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
use App\Models\OpdKegiatanIndikatorKinerja;

class KegiatanController extends Controller
{
    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_kegiatan_kegiatan_id' => 'required',
            'indikator_kinerja_kegiatan_deskripsi' => 'required',
            'indikator_kinerja_kegiatan_satuan' => 'required',
            'indikator_kinerja_kegiatan_kondisi_target_kinerja_awal' => 'required',
            'indikator_kinerja_kegiatan_kondisi_target_anggaran_awal' => 'required',
            'indikator_kinerja_kegiatan_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $kegiatan_indikator_kinerja = new KegiatanIndikatorKinerja;
        $kegiatan_indikator_kinerja->kegiatan_id = $request->indikator_kinerja_kegiatan_kegiatan_id;
        $kegiatan_indikator_kinerja->deskripsi = $request->indikator_kinerja_kegiatan_deskripsi;
        $kegiatan_indikator_kinerja->satuan = $request->indikator_kinerja_kegiatan_satuan;
        $kegiatan_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_kegiatan_kondisi_target_kinerja_awal;
        $kegiatan_indikator_kinerja->kondisi_target_anggaran_awal = $request->indikator_kinerja_kegiatan_kondisi_target_anggaran_awal;
        $kegiatan_indikator_kinerja->status_indikator = $request->indikator_kinerja_kegiatan_status_indikator;
        $kegiatan_indikator_kinerja->save();

        $opd = new OpdKegiatanIndikatorKinerja;
        $opd->kegiatan_indikator_kinerja_id = $kegiatan_indikator_kinerja->id;
        $opd->opd_id = Auth::user()->opd->opd_id;
        $opd->save();

        // Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Kegiatan');
        // return redirect()->route('opd.renstra.index');
        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $kegiatan_indikator_kinerja->kegiatan_id)->get();
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }

        $get_programs = Program::where('id', $kegiatan_indikator_kinerja->kegiatan->program_id)
                        ->whereHas('program_indikator_kinerja', function($q){
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

        $get_tujuans = Tujuan::whereHas('sasaran', function($q) use ($sasaran){
            $q->where('id', $sasaran['id']);
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
        })->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_sekarang)
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

        $get_misis = Misi::whereHas('tujuan', function($q) use ($tujuan){
            $q->where('id', $tujuan['id']);
            $q->whereHas('sasaran', function($q){
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
            });
        })->get();
        $misi = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan,
                ];
            }
        }

        $get_visis = Visi::whereHas('misi', function($q) use ($misi){
            $q->where('id', $misi['id']);
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
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
                });
            });
        })->where('tahun_periode_id', $get_periode->id)->get();
        $visi = [];

        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visi = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visi = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        foreach($kegiatans as $kegiatan)
        {
            $html .= '<tr>';
                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
                $html .= '<br>';
                $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>';
                $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span></td>';
                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                })->get();
                $html .= '<td width="30%"><table class="table table-bordered">
                    <tbody>';
                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$kegiatan_indikator_kinerja->deskripsi . ' (';
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                                $html .= '</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-kegiatan-indikator-kinerja mr-1" data-id="'.$kegiatan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Kegiatan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-kegiatan-indikator-kinerja" type="button" title="Hapus Indikator" data-kegiatan-id="'.$kegiatan['id'].'" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="20%">
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-kegiatan-indikator-kinerja" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Tambah Kegiatan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                </td>';
            $html .='</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Indikator</th>
                                    <th>Kondisi Kinerja Awal</th>
                                    <th>Target Anggaran Awal</th>
                                    <th>Target Kinerja</th>
                                    <th>Satuan</th>
                                    <th>Target Anggaran</th>
                                    <th>Realisasi Kinerja</th>
                                    <th>Realisasi Anggaran</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProgramKegiatan'.$kegiatan['id'].'">';
                            $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                            $no_kegiatan_indikator_kinerja = 1;
                            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $html .= '<td> Rp. '.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                    $a = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                            $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                        })->where('tahun', $tahun)->first();
                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                        {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        } else {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menambahkan Indikator Kinerja Kegiatan', 'html' => $html, 'program_id' => $program['id']]);
    }

    public function indikator_kinerja_edit($id)
    {
        $data = KegiatanIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_kegiatan_id' => 'required',
            'edit_indikator_kinerja_kegiatan_deskripsi' => 'required',
            'edit_indikator_kinerja_kegiatan_satuan' => 'required',
            'edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal' => 'required',
            'edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal' => 'required',
            'edit_indikator_kinerja_kegiatan_status_indikator' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::find($request->indikator_kinerja_kegiatan_id);
        $kegiatan_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_kegiatan_deskripsi;
        $kegiatan_indikator_kinerja->satuan = $request->edit_indikator_kinerja_kegiatan_satuan;
        $kegiatan_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal;
        $kegiatan_indikator_kinerja->kondisi_target_anggaran_awal = $request->edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal;
        $kegiatan_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_kegiatan_status_indikator;
        $kegiatan_indikator_kinerja->save();

        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $kegiatan_indikator_kinerja->kegiatan_id)->get();
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }

        $get_programs = Program::where('id', $kegiatan_indikator_kinerja->kegiatan->program_id)
                        ->whereHas('program_indikator_kinerja', function($q){
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

        $get_tujuans = Tujuan::whereHas('sasaran', function($q) use ($sasaran){
            $q->where('id', $sasaran['id']);
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
        })->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_sekarang)
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

        $get_misis = Misi::whereHas('tujuan', function($q) use ($tujuan){
            $q->where('id', $tujuan['id']);
            $q->whereHas('sasaran', function($q){
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
            });
        })->get();
        $misi = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan,
                ];
            }
        }

        $get_visis = Visi::whereHas('misi', function($q) use ($misi){
            $q->where('id', $misi['id']);
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
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
                });
            });
        })->where('tahun_periode_id', $get_periode->id)->get();
        $visi = [];

        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visi = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visi = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        foreach($kegiatans as $kegiatan)
        {
            $html .= '<tr>';
                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
                $html .= '<br>';
                $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>';
                $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span></td>';
                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                })->get();
                $html .= '<td width="30%"><table class="table table-bordered">
                    <tbody>';
                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$kegiatan_indikator_kinerja->deskripsi . ' (';
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                                $html .= '</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-kegiatan-indikator-kinerja mr-1" data-id="'.$kegiatan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Kegiatan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-kegiatan-indikator-kinerja" type="button" title="Hapus Indikator" data-kegiatan-id="'.$kegiatan['id'].'" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="20%">
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-kegiatan-indikator-kinerja" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Tambah Kegiatan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                </td>';
            $html .='</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Indikator</th>
                                    <th>Kondisi Kinerja Awal</th>
                                    <th>Target Anggaran Awal</th>
                                    <th>Target Kinerja</th>
                                    <th>Satuan</th>
                                    <th>Target Anggaran</th>
                                    <th>Realisasi Kinerja</th>
                                    <th>Realisasi Anggaran</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProgramKegiatan'.$kegiatan['id'].'">';
                            $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                            $no_kegiatan_indikator_kinerja = 1;
                            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $html .= '<td> Rp. '.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                    $a = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                            $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                        })->where('tahun', $tahun)->first();
                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                        {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        } else {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil merubah Indikator Kinerja Kegiatan', 'html' => $html, 'program_id' => $program['id']]);
        // Alert::success('Berhasil', 'Berhasil merubah Indikator Kinerja Kegiatan');
        // return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $kegiatan_indikator = KegiatanIndikatorKinerja::find($request->kegiatan_indikator_kinerja_id);
        $idKegiatan = $kegiatan_indikator->kegiatan_id;
        $idProgram = $kegiatan_indikator->kegiatan->program_id;

        $get_opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator->id)->get();

        foreach ($get_opd_kegiatan_indikator_kinerjas as $get_opd_kegiatan_indikator_kinerja) {
            $kegiatan_target_satuan_rp_realisasis = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $get_opd_kegiatan_indikator_kinerja->id)->get();
            foreach ($kegiatan_target_satuan_rp_realisasis as $kegiatan_target_satuan_rp_realisasi) {
                KegiatanTargetSatuanRpRealisasi::find($kegiatan_target_satuan_rp_realisasi->id)->delete();
            }
            OpdKegiatanIndikatorKinerja::find($get_opd_kegiatan_indikator_kinerja->id)->delete();
        }

        $kegiatan_indikator = $kegiatan_indikator->delete();

        // return response()->json(['success' => 'Berhasil Menghapus Indikator Kinerja untuk Program']);
        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $idKegiatan)->get();
        $kegiatans = [];
        foreach ($get_kegiatans as $get_kegiatan) {
            $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)
                                        ->latest()->first();
            if($cek_perubahan_kegiatan)
            {
                $kegiatans[] = [
                    'id' => $cek_perubahan_kegiatan->kegiatan_id,
                    'kode' => $cek_perubahan_kegiatan->kode,
                    'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                ];
            } else {
                $kegiatans[] = [
                    'id' => $get_kegiatan->id,
                    'kode' => $get_kegiatan->kode,
                    'deskripsi' => $get_kegiatan->deskripsi,
                    'tahun_perubahan' => $get_kegiatan->tahun_perubahan
                ];
            }
        }

        $get_programs = Program::where('id', $idProgram)
                        ->whereHas('program_indikator_kinerja', function($q){
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

        $get_tujuans = Tujuan::whereHas('sasaran', function($q) use ($sasaran){
            $q->where('id', $sasaran['id']);
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
        })->get();
        $tujuan = [];
        foreach ($get_tujuans as $get_tujuan) {
            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->where('tahun_perubahan', $tahun_sekarang)
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

        $get_misis = Misi::whereHas('tujuan', function($q) use ($tujuan){
            $q->where('id', $tujuan['id']);
            $q->whereHas('sasaran', function($q){
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
            });
        })->get();
        $misi = [];
        foreach ($get_misis as $get_misi) {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_misi)
            {
                $misi = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                ];
            } else {
                $misi = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan,
                ];
            }
        }

        $get_visis = Visi::whereHas('misi', function($q) use ($misi){
            $q->where('id', $misi['id']);
            $q->whereHas('tujuan', function($q){
                $q->whereHas('sasaran', function($q){
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
                });
            });
        })->where('tahun_periode_id', $get_periode->id)->get();
        $visi = [];

        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->where('tahun_perubahan', $tahun_sekarang)
                                    ->latest()->first();
            if($cek_perubahan_visi)
            {
                $visi = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_visi->tahun_perubahan
                ];
            } else {
                $visi = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi,
                    'tahun_perubahan' => $get_visi->tahun_perubahan
                ];
            }
        }

        foreach($kegiatans as $kegiatan)
        {
            $html .= '<tr>';
                $html .= '<td width="5%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.$program['kode'].'.'.$kegiatan['kode'].'</td>';
                $html .= '<td width="45%" data-bs-toggle="collapse" data-bs-target="#program_kegiatan_'.$kegiatan['id'].'" class="accordion-toggle">'.strtoupper($kegiatan['deskripsi']);
                $html .= '<br>';
                $html .= '<span class="badge bg-primary text-uppercase renstra-kegiatan-tagging">Visi</span>';
                $html .= ' <span class="badge bg-warning text-uppercase renstra-kegiatan-tagging">Misi '.$misi['kode'].'</span>
                <span class="badge bg-secondary text-uppercase renstra-kegiatan-tagging">Tujuan '.$misi['kode'].'.'.$tujuan['kode'].'</span>
                <span class="badge bg-danger text-uppercase renstra-kegiatan-tagging">Sasaran '.$misi['kode'].'.'.$tujuan['kode'].'.'.$sasaran['kode'].'</span>
                <span class="badge bg-dark text-uppercase renstra-kegiatan-tagging">Program '.$program['kode'].'</span>
                <span class="badge bg-success text-uppercase renstra-kegiatan-tagging">Kegiatan '.$program['kode'].'.'.$kegiatan['kode'].'</span></td>';
                $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])
                                                ->whereHas('opd_kegiatan_indikator_kinerja', function($q){
                                                    $q->where('opd_id', Auth::user()->opd->opd_id);
                                                })->get();
                $html .= '<td width="30%"><table class="table table-bordered">
                    <tbody>';
                        foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                            $html .= '<tr>';
                                $html .= '<td width="75%">'.$kegiatan_indikator_kinerja->deskripsi . ' (';
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($kegiatan_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                                $html .= '</td>';
                                $html .= '<td width="25%">
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-kegiatan-indikator-kinerja mr-1" data-id="'.$kegiatan_indikator_kinerja->id.'" title="Edit Indikator Kinerja Kegiatan"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-kegiatan-indikator-kinerja" type="button" title="Hapus Indikator" data-kegiatan-id="'.$kegiatan['id'].'" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            $html .= '</tr>';
                        }
                    $html .= '</tbody>
                </table></td>';
                $html .= '<td width="20%">
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-kegiatan-indikator-kinerja" data-kegiatan-id="'.$kegiatan['id'].'" type="button" title="Tambah Kegiatan Indikator Kinerja"><i class="fas fa-lock"></i></button>
                </td>';
            $html .='</tr>
            <tr>
                <td colspan="4" class="hiddenRow">
                    <div class="collapse accordion-body" id="program_kegiatan_'.$kegiatan['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Indikator</th>
                                    <th>Kondisi Kinerja Awal</th>
                                    <th>Target Anggaran Awal</th>
                                    <th>Target Kinerja</th>
                                    <th>Satuan</th>
                                    <th>Target Anggaran</th>
                                    <th>Realisasi Kinerja</th>
                                    <th>Realisasi Anggaran</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProgramKegiatan'.$kegiatan['id'].'">';
                            $kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                            $no_kegiatan_indikator_kinerja = 1;
                            foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_kegiatan_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$kegiatan_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $html .= '<td> Rp. '.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                                    $a = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                                            $q->where('opd_id', Auth::user()->opd->opd_id);
                                            $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                                        })->where('tahun', $tahun)->first();
                                        if($cek_kegiatan_target_satuan_rp_realisasi)
                                        {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        } else {
                                            if($a == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                $html .='</tr>';
                                            }
                                            $a++;
                                        }
                                    }
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
                </td>
            </tr>';
        }

        return response()->json(['success' => 'Berhasil menghapus Indikator Kinerja Kegiatan', 'html' => $html, 'program_id' => $program['id']]);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'kegiatan_indikator_kinerja_id' => 'required',
            'target' => 'required',
            'target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $get_opd_kegiatan_indikator_kinerja = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $request->kegiatan_indikator_kinerja_id)
                                                ->where('opd_id', Auth::user()->opd->opd_id)->first();

        $kegiatan_target_satuan_rp_realisasi = new KegiatanTargetSatuanRpRealisasi;
        $kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja_id = $get_opd_kegiatan_indikator_kinerja->id;
        $kegiatan_target_satuan_rp_realisasi->target = $request->target;
        $kegiatan_target_satuan_rp_realisasi->target_rp = $request->target_rp;
        $kegiatan_target_satuan_rp_realisasi->tahun = $request->tahun;
        $kegiatan_target_satuan_rp_realisasi->save();

        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->kegiatan_id)->get();
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

        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_indikator_kinerja', function($q){
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

        $getProgram = Program::find($program['id']);

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($getProgram){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($getProgram){
                $q->whereHas('program_rpjmd', function($q) use ($getProgram){
                    $q->whereHas('program', function($q) use ($getProgram){
                        $q->where('id', $getProgram->id);
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
                $html .= '<td> Rp. '.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                $a = 1;
                foreach ($tahuns as $tahun) {
                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                        $q->where('opd_id', Auth::user()->opd->opd_id);
                        $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                    })->where('tahun', $tahun)->first();
                    if($cek_kegiatan_target_satuan_rp_realisasi)
                    {
                        if($a == 1)
                        {
                                $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                            $html .='</tr>';
                        }
                        $a++;
                    } else {
                        if($a == 1)
                        {
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $a++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html, 'kegiatan_id' => $kegiatan['id']]);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'kegiatan_target_satuan_rp_realisasi' => 'required',
            'kegiatan_edit_target' => 'required',
            'kegiatan_edit_target_rp' => 'required',
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::find($request->kegiatan_target_satuan_rp_realisasi);
        $kegiatan_target_satuan_rp_realisasi->target = $request->kegiatan_edit_target;
        $kegiatan_target_satuan_rp_realisasi->target_rp = $request->kegiatan_edit_target_rp;
        $kegiatan_target_satuan_rp_realisasi->save();

        $html = '';
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_kegiatans = Kegiatan::where('id', $kegiatan_target_satuan_rp_realisasi->opd_kegiatan_indikator_kinerja->kegiatan_indikator_kinerja->kegiatan_id)->get();
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

        $get_programs = Program::where('id', $getKegiatan->program_id)->whereHas('program_indikator_kinerja', function($q){
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

        $getProgram = Program::find($program['id']);

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($getProgram){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($getProgram){
                $q->whereHas('program_rpjmd', function($q) use ($getProgram){
                    $q->whereHas('program', function($q) use ($getProgram){
                        $q->where('id', $getProgram->id);
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
                $html .= '<td> Rp. '.number_format($kegiatan_indikator_kinerja->kondisi_target_anggaran_awal,2,',','.').'</td>';
                $a = 1;
                foreach ($tahuns as $tahun) {
                    $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja){
                        $q->where('opd_id', Auth::user()->opd->opd_id);
                        $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                    })->where('tahun', $tahun)->first();
                    if($cek_kegiatan_target_satuan_rp_realisasi)
                    {
                        if($a == 1)
                        {
                                $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format($cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><span class="kegiatan-span-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'">'.$cek_kegiatan_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><span class="kegiatan-span-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.' data-sasaran-id-'.$sasaran['id'].'" data-target-rp="'.$cek_kegiatan_target_satuan_rp_realisasi->target_rp.'">Rp. '.number_format((int)$cek_kegiatan_target_satuan_rp_realisasi->target_rp,2).'</span></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-kegiatan-edit-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-kegiatan-target-satuan-rp-realisasi="'.$cek_kegiatan_target_satuan_rp_realisasi->id.'" data-sasaran-id="'.$sasaran['id'].'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                            $html .='</tr>';
                        }
                        $a++;
                    } else {
                        if($a == 1)
                        {
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        } else {
                            $html .= '<tr>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$kegiatan_indikator_kinerja->satuan.'</td>';
                                $html .= '<td><input type="number" class="form-control kegiatan-add-target-rp '.$tahun.' data-kegiatan-indikator-kinerja-'.$kegiatan_indikator_kinerja->id.'"></td>';
                                $html .= '<td></td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-kegiatan-target-satuan-rp-realisasi" type="button" data-kegiatan-indikator-kinerja-id="'.$kegiatan_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                            $html .='</tr>';
                        }
                        $a++;
                    }
                }
        }

        return response()->json(['success' => 'Berhasil Merubah Target Kegiatan', 'html' => $html, 'kegiatan_id' => $kegiatan['id']]);

        // Alert::success('Berhasil', 'Berhasil Merubah Target Kegiatan');
        // return redirect()->route('opd.renstra.index');
    }
}
