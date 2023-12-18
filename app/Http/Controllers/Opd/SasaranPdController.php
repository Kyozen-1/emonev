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
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\ProgramRpjmd;
use App\Models\SasaranPd;
use App\Models\PivotPerubahanSasaranPd;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\SasaranPdIndikatorKinerja;
use App\Models\SasaranPdTargetSatuanRpRealisasi;
use App\Models\SasaranPdProgramRpjmd;
use App\Models\TahunPeriode;
use App\Models\SasaranPdRealisasiRenja;

class SasaranPdController extends Controller
{
    public function tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tambah_sasaran_pd_sasaran_id' => 'required',
            'tambah_sasaran_pd_kode' => 'required',
            'tambah_sasaran_pd_deskripsi' => 'required',
            'tambah_sasaran_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd = new SasaranPd;
        $sasaran_pd->sasaran_id = $request->tambah_sasaran_pd_sasaran_id;
        $sasaran_pd->kode = $request->tambah_sasaran_pd_kode;
        $sasaran_pd->deskripsi = $request->tambah_sasaran_pd_deskripsi;
        $sasaran_pd->opd_id = Auth::user()->opd->opd_id;
        $sasaran_pd->tahun_perubahan = $request->tambah_sasaran_pd_tahun_perubahan;
        $sasaran_pd->save();

        // Alert::success('Berhasil', 'Berhasil menambahkan Sasaran PD');
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

        $get_sasarans = Sasaran::where('id', $sasaran_pd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil menambah data Sasaran PD', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function edit($id)
    {
        $data = SasaranPd::find($id);
        $array = [];
        $cek_perubahan_sasaran_pd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $id)->latest()->first();
        if($cek_perubahan_sasaran_pd)
        {
            $array = [
                'kode' => $cek_perubahan_sasaran_pd->kode,
                'deskripsi' => $cek_perubahan_sasaran_pd->deskripsi,
                'tahun_perubahan' => $cek_perubahan_sasaran_pd->tahun_perubahan,
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
            'edit_sasaran_pd_sasaran_pd_id' => 'required',
            'edit_sasaran_pd_sasaran_id' => 'required',
            'edit_sasaran_pd_kode' => 'required',
            'edit_sasaran_pd_deskripsi' => 'required',
            'edit_sasaran_pd_tahun_perubahan' => 'required',
        ]);

        if($errors -> fails())
        {
            Alert::error('Gagal', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $cek_sasaran_pd = SasaranPd::where('id', $request->edit_sasaran_pd_sasaran_pd_id)
                            ->where('tahun_perubahan', $request->edit_sasaran_pd_tahun_perubahan)
                            ->first();
        if($cek_sasaran_pd)
        {
            $update_sasaran_pd = SasaranPd::find($request->edit_sasaran_pd_sasaran_pd_id);
            $update_sasaran_pd->kode = $request->edit_sasaran_pd_kode;
            $update_sasaran_pd->deskripsi = $request->edit_sasaran_pd_deskripsi;
            $update_sasaran_pd->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
            $update_sasaran_pd->save();
            $idSasaranPd = $update_sasaran_pd->id;
        } else {
            $cek_pivot = PivotPerubahanSasaranPd::where('sasaran_pd_id', $request->edit_sasaran_pd_sasaran_pd_id)
                            ->where('tahun_perubahan', $request->edit_sasaran_pd_tahun_perubahan)->first();
            if($cek_pivot)
            {
                PivotPerubahanSasaranPd::find($cek_pivot->id)->delete();

                $pivot = new PivotPerubahanSasaranPd;
                $pivot->sasaran_pd_id = $request->edit_sasaran_pd_sasaran_pd_id;
                $pivot->sasaran_id = $request->edit_sasaran_pd_sasaran_id;
                $pivot->kode = $request->edit_sasaran_pd_kode;
                $pivot->deskripsi = $request->edit_sasaran_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
                $pivot->save();
                $idSasaranPd = $pivot->sasaran_pd_id;
            } else {
                $pivot = new PivotPerubahanSasaranPd;
                $pivot->sasaran_pd_id = $request->edit_sasaran_pd_sasaran_pd_id;
                $pivot->sasaran_id = $request->edit_sasaran_pd_sasaran_id;
                $pivot->kode = $request->edit_sasaran_pd_kode;
                $pivot->deskripsi = $request->edit_sasaran_pd_deskripsi;
                $pivot->opd_id = Auth::user()->opd->opd_id;
                $pivot->tahun_perubahan = $request->edit_sasaran_pd_tahun_perubahan;
                $pivot->save();
                $idSasaranPd = $pivot->sasaran_pd_id;
            }
        }

        // Alert::success('Berhasil', 'Berhasil merubah data Sasaran PD');
        // return redirect()->route('opd.renstra.index');

        $getSasaranPd = SasaranPd::find($idSasaranPd);
        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $getSasaranPd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil merubah data Sasaran PD', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function hapus(Request $request)
    {
        $idSasaran = SasaranPd::find($request->sasaran_pd_id)->sasaran_id;
        $pivot_perubahan_sasaran_pds = PivotPerubahanSasaranPd::where('sasaran_pd_id', $request->sasaran_pd_id)->get();
        foreach ($pivot_perubahan_sasaran_pds as $pivot_perubahan_sasaran_pd) {
            PivotPerubahanSasaranPd::find($pivot_perubahan_sasaran_pd->id)->delete();
        }

        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $request->sasaran_pd_id)->get();
        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
            $sasaran_pd_target_satuan_rp_realisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)->get();
            foreach ($sasaran_pd_target_satuan_rp_realisasis as $sasaran_pd_target_satuan_rp_realisasi) {
                SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
            }

            SasaranPdIndikatorKinerja::find($sasaran_pd_indikator_kinerja->id)->delete();
        }

        SasaranPd::find($request->sasaran_pd_id)->delete();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $idSasaran)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil menghapus data', 'html' => $html, 'sasaran_id' => $sasaran['id']]);

        // return response()->json(['success' => 'Berhasil menghapus data']);
    }

    public function indikator_kinerja_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sasaran_pd_sasaran_pd_id' => 'required',
            'indikator_kinerja_sasaran_pd_deskripsi' => 'required',
            'indikator_kinerja_sasaran_pd_status_indikator' => 'required',
            'indikator_kinerja_sasaran_pd_satuan' => 'required',
            'indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd_indikator_kinerja = new SasaranPdIndikatorKinerja;
        $sasaran_pd_indikator_kinerja->sasaran_pd_id = $request->indikator_kinerja_sasaran_pd_sasaran_pd_id;
        $sasaran_pd_indikator_kinerja->deskripsi = $request->indikator_kinerja_sasaran_pd_deskripsi;
        $sasaran_pd_indikator_kinerja->status_indikator = $request->indikator_kinerja_sasaran_pd_status_indikator;
        $sasaran_pd_indikator_kinerja->satuan = $request->indikator_kinerja_sasaran_pd_satuan;
        $sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal;
        $sasaran_pd_indikator_kinerja->save();

        // Alert::success('Berhasil', 'Berhasil menambahkan Indikator Kinerja Sasaran PD');
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

        $get_sasarans = Sasaran::where('id', $sasaran_pd_indikator_kinerja->sasaran_pd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil Tambah Indikator Kinerja Sasaran PD', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function indikator_kinerja_edit($id)
    {
        $data = SasaranPdIndikatorKinerja::find($id);

        return response()->json(['result' => $data]);
    }

    public function indikator_kinerja_update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'indikator_kinerja_sasaran_pd_id' => 'required',
            'edit_indikator_kinerja_sasaran_pd_deskripsi' => 'required',
            'edit_indikator_kinerja_sasaran_pd_status_indikator' => 'required',
            'edit_indikator_kinerja_sasaran_pd_satuan' => 'required',
            'edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal' => 'required'
        ]);

        if($errors -> fails())
        {
            Alert::error('Berhasil', $errors->errors()->all());
            return redirect()->route('opd.renstra.index');
        }

        $sasaran_pd_indikator_kinerja = SasaranPdIndikatorKinerja::find($request->indikator_kinerja_sasaran_pd_id);
        $sasaran_pd_indikator_kinerja->deskripsi = $request->edit_indikator_kinerja_sasaran_pd_deskripsi;
        $sasaran_pd_indikator_kinerja->status_indikator = $request->edit_indikator_kinerja_sasaran_pd_status_indikator;
        $sasaran_pd_indikator_kinerja->satuan = $request->edit_indikator_kinerja_sasaran_pd_satuan;
        $sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal = $request->edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal;
        $sasaran_pd_indikator_kinerja->save();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $sasaran_pd_indikator_kinerja->sasaran_pd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil Merubah Indikator Kinerja Sasaran PD', 'html' => $html, 'sasaran_id' => $sasaran['id']]);

        // Alert::success('Berhasil', 'Berhasil Merubah Indikator Kinerja Sasaran PD');
        // return redirect()->route('opd.renstra.index');
    }

    public function indikator_kinerja_hapus(Request $request)
    {
        $idSasaran = SasaranPdIndikatorKinerja::find($request->sasaran_pd_indikator_kinerja_id)->sasaran_pd->sasaran_id;
        $sasaran_pd_target_satuan_rp_realisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $request->sasaran_pd_indikator_kinerja_id)->get();
        foreach ($sasaran_pd_target_satuan_rp_realisasis as $sasaran_pd_target_satuan_rp_realisasi) {
            SasaranPdTargetSatuanRpRealisasi::find($sasaran_pd_target_satuan_rp_realisasi->id)->delete();
        }

        SasaranPdIndikatorKinerja::find($request->sasaran_pd_indikator_kinerja_id)->delete();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $idSasaran)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil menghapus data', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function target_satuan_realisasi_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'tahun' => 'required',
            'sasaran_pd_indikator_kinerja_id' => 'required',
            'target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_pd_target_satuan_rp_realisasi = new SasaranPdTargetSatuanRpRealisasi;
        $sasaran_pd_target_satuan_rp_realisasi->sasaran_pd_indikator_kinerja_id = $request->sasaran_pd_indikator_kinerja_id;
        $sasaran_pd_target_satuan_rp_realisasi->target = $request->target;
        $sasaran_pd_target_satuan_rp_realisasi->tahun = $request->tahun;
        $sasaran_pd_target_satuan_rp_realisasi->save();

        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasaran_pds = SasaranPd::where('id', $sasaran_pd_target_satuan_rp_realisasi->sasaran_pd_indikator_kinerja->sasaran_pd_id)->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pd = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pd = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pd = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }

        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
        $no_sasaran_pd_indikator_kinerja = 1;
        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_sasaran_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_sasaran_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td>'.$tahun.'</td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
        }
        return response()->json(['success' => 'Berhasil menambahkan data', 'html' => $html, 'sasaran_pd_id' => $sasaran_pd['id']]);
    }

    public function target_satuan_realisasi_ubah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_pd_target_satuan_rp_realisasi' => 'required',
            'sasaran_pd_edit_target' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::find($request->sasaran_pd_target_satuan_rp_realisasi);
        $sasaran_pd_target_satuan_rp_realisasi->target = $request->sasaran_pd_edit_target;
        $sasaran_pd_target_satuan_rp_realisasi->save();

        // Alert::success('Berhasil', 'Berhasil Merubah Target Sasaran PD');
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

        $get_sasaran_pds = SasaranPd::where('id', $sasaran_pd_target_satuan_rp_realisasi->sasaran_pd_indikator_kinerja->sasaran_pd_id)->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pd = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pd = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pd = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }

        $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
        $no_sasaran_pd_indikator_kinerja = 1;
        foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
            $html .= '<tr>';
                $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                $b = 1;
                foreach ($tahuns as $tahun) {
                    $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                        ->where('tahun', $tahun)->first();
                    if($cek_sasaran_pd_target_satuan_rp_realisasi)
                    {
                        if($b == 1)
                        {
                                $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_sasaran_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                if($cek_sasaran_pd_realisasi_renja)
                                {
                                    $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                } else {
                                    $html .= '<td></td>';
                                }
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                </button>
                                            </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    } else {
                        if($b == 1)
                        {
                                $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td>'.$tahun.'</td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                $html .= '<td></td>';
                                $html .= '<td>'.$tahun.'</td>';
                                if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                {
                                    $html .= '<td></td>';
                                } else {
                                    $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                            </button>
                                        </td>';
                                }
                            $html .='</tr>';
                        }
                        $b++;
                    }
                }
        }
        return response()->json(['success' => 'Berhasil Merubah Target Sasaran PD', 'html' => $html, 'sasaran_pd_id' => $sasaran_pd['id']]);
    }

    public function sasaran_pd_program_rpjmd_get_program_rpjmd(Request $request)
    {
        $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($request){
            $q->whereHas('sasaran_indikator_kinerja', function($q) use ($request){
                $q->whereHas('sasaran', function($q) use ($request){
                    $q->where('id', $request->sasaran_id);
                });
            });
        })->whereHas('program', function($q){
            $q->whereHas('program_indikator_kinerja', function($q){
                $q->whereHas('opd_program_indikator_kinerja', function($q){
                    $q->where('opd_id', Auth::user()->opd->opd_id);
                });
            });
        })->whereDoesntHave('sasaran_pd_program_rpjmd', function($q){
            $q->whereHas('sasaran_pd', function($q){
                $q->where('opd_id', Auth::user()->opd->opd_id);
            });
        })->get();

        $programs = [];

        foreach ($get_program_rpjmds as $get_program_rpjmd) {
            $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)->latest()->first();
            if($cek_perubahan_program)
            {
                $programs[] = [
                    'id' => $get_program_rpjmd->id,
                    'deskripsi' => $cek_perubahan_program->deskripsi
                ];
            } else {
                $programs[] = [
                    'id' => $get_program_rpjmd->id,
                    'deskripsi' => $get_program_rpjmd->program->deskripsi
                ];
            }
        }

        return response()->json($programs);
    }

    public function sasaran_pd_program_rpjmd_tambah(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'sasaran_pd_program_rpjmd_sasaran_pd_id' => 'required',
            'sasaran_pd_program_rpjmd_program_rpjmd_id' => 'required | array',
            'sasaran_pd_program_rpjmd_program_rpjmd_id.*' => 'required'
        ]);

        if($errors -> fails())
        {
            return back()->with('errors', $errors->message()->all())->withInput();
        }

        $program_rpjmd_id = $request->sasaran_pd_program_rpjmd_program_rpjmd_id;
        for ($i=0; $i < count($program_rpjmd_id); $i++) {
            $sasaran_pd_program_rpjmd = new SasaranPdProgramRpjmd;
            $sasaran_pd_program_rpjmd->sasaran_pd_id = $request->sasaran_pd_program_rpjmd_sasaran_pd_id;
            $sasaran_pd_program_rpjmd->program_rpjmd_id = $program_rpjmd_id[$i];
            $sasaran_pd_program_rpjmd->save();
        }

        // Alert::success('Berhasil', 'Berhasil mengelompokan Program di Sasaran PD');
        // return redirect()->route('opd.renstra.index');
        $getSasaranPd = SasaranPd::find($request->sasaran_pd_program_rpjmd_sasaran_pd_id);
        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $getSasaranPd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil mengelompokan Program di Sasaran PD', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }

    public function sasaran_pd_program_rpjmd_hapus(Request $request)
    {
        $getSasaranPd = SasaranPd::find(SasaranPdProgramRpjmd::find($request->sasaran_pd_program_rpjmd_id)->sasaran_pd_id);
        SasaranPdProgramRpjmd::find($request->sasaran_pd_program_rpjmd_id)->delete();

        // return response()->json(['success' => 'Berhasil menghapus program dari kelompok sasaran pd']);
        $html = '';

        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }
        $tahun_sekarang = Carbon::parse(Carbon::now())->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('Y');

        $get_sasarans = Sasaran::where('id', $getSasaranPd->sasaran_id)->whereHas('sasaran_indikator_kinerja', function($q){
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

        $get_sasaran_pds = SasaranPd::where('sasaran_id', $sasaran['id'])->where('opd_id', Auth::user()->opd->opd_id)->get();
        $sasaran_pds = [];
        foreach ($get_sasaran_pds as $get_sasaran_pd) {
            $cek_perubahan_sasaran_opd = PivotPerubahanSasaranPd::where('sasaran_pd_id', $get_sasaran_pd->id)->latest()->first();
            if($cek_perubahan_sasaran_opd)
            {
                $sasaran_pds[] = [
                    'id' => $cek_perubahan_sasaran_opd->sasaran_pd_id,
                    'sasaran_id' => $cek_perubahan_sasaran_opd->sasaran_id,
                    'kode' => $cek_perubahan_sasaran_opd->kode,
                    'deskripsi' => $cek_perubahan_sasaran_opd->deskripsi,
                    'opd_id' => $cek_perubahan_sasaran_opd->opd_id,
                    'tahun_perubahan' => $cek_perubahan_sasaran_opd->tahun_perubahan,
                ];
            } else {
                $sasaran_pds[] = [
                    'id' => $get_sasaran_pd->id,
                    'sasaran_id' => $get_sasaran_pd->sasaran_id,
                    'kode' => $get_sasaran_pd->kode,
                    'deskripsi' => $get_sasaran_pd->deskripsi,
                    'opd_id' => $get_sasaran_pd->opd_id,
                    'tahun_perubahan' => $get_sasaran_pd->tahun_perubahan,
                ];
            }
        }
        foreach ($sasaran_pds as $sasaran_pd) {
            $html .= '<tr>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['kode'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle">'.$sasaran_pd['deskripsi'].'</td>';
                $html .= '<td data-bs-toggle="collapse" data-bs-target="#sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'" class="accordion-toggle"><ul>';
                $sasaran_pd_program_rpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                foreach($sasaran_pd_program_rpjmds as $sasaran_pd_program_rpjmd)
                {
                    if($sasaran_pd_program_rpjmd->program_rpjmd)
                    {
                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran_pd_program_rpjmd->program_rpjmd->program->id)->latest()->first();
                        if($cek_perubahan_program)
                        {
                            $html .= '<li>'.$cek_perubahan_program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        } else {
                            $html .= '<li>'.$sasaran_pd_program_rpjmd->program_rpjmd->program->deskripsi.' <button type="button" class="btn-close btn-hapus-sasaran-pd-program-rpjmd" data-sasaran-pd-program-rpjmd-id="'.$sasaran_pd_program_rpjmd->id.'"></button></li>';
                        }
                    }
                }
                $html .= '</ul></td>';
                $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                $html .= '<td><table class="table table-bordered">
                    <thead>
                        <th width="40%">Deskripsi</th>
                        <th width="15%">Satuan</th>
                        <th width="20%">T Awal</th>
                        <th width="25%">Aksi</th>
                    </thead>
                    <tbody>';
                    foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                        $html .= '<tr>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi .' (';
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target NSPK')
                                {
                                    $html .= '<i class="fas fa-n text-primary ml-1" title="Target NSPK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target IKK')
                                {
                                    $html .= '<i class="fas fa-i text-primary ml-1" title="Target IKK"></i>)';
                                }
                                if($sasaran_pd_indikator_kinerja->status_indikator == 'Target Indikator Lainnya')
                                {
                                    $html .= '<i class="fas fa-t text-primary ml-1" title="Target Indikator Lainnya"></i>)';
                                }
                            $html .= '</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                            $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                            if($sasaran_pd_indikator_kinerja->status_lock == '1')
                            {
                                $html .= '<td></td>';
                            } else {
                                $html .= '<td >
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-warning btn-edit-sasaran-indikator-kinerja mr-1" data-id="'.$sasaran_pd_indikator_kinerja->id.'" title="Edit Indikator Kinerja Sasaran"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-icon-only btn-outline-danger btn-hapus-sasaran-indikator-kinerja" type="button" title="Hapus Indikator" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'"><i class="fas fa-trash"></i></button>
                                </td>';
                            }
                        $html .= '</tr>';
                    }
                    $html .='</tbody>
                </table></td>';
                $html .= '<td>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Edit Sasaran PD"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon btn-danger waves-effect waves-light hapus-sasaran-pd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Hapus Sasaran PD"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-indikator-kinerja" data-sasaran-pd-id="'.$sasaran_pd['id'].'" type="button" title="Tambah Sasaran PD Indikator Kinerja"><i class="fas fa-lock"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light tambah-sasaran-pd-program-rpjmd" data-sasaran-pd-id="'.$sasaran_pd['id'].'" data-sasaran-id="'.$sasaran['id'].'" type="button" title="Tambah Sasaran PD Program RPJMD"><i class="fas fa-user"></i></button>
                </td>';
            $html .= '</tr>
            <tr>
                <td colspan="5" class="hiddenRow">
                    <div class="collapse accordion-body" id="sasaran_sasaran_pd_indikator_kinerja'.$sasaran_pd['id'].'">
                        <table class="table table-striped table-condesed">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Indikator</th>
                                    <th width="20%">Kondisi Kinerja Awal</th>
                                    <th width="10%">Target Kinerja</th>
                                    <th width="10%">Satuan</th>
                                    <th width="10%">Realisasi Kinerja</th>
                                    <th width="10%">Tahun</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySasaranSasaranPdIndikatorKinerja'.$sasaran_pd['id'].'">';
                            $sasaran_pd_indikator_kinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $sasaran_pd['id'])->get();
                            $no_sasaran_pd_indikator_kinerja = 1;
                            foreach ($sasaran_pd_indikator_kinerjas as $sasaran_pd_indikator_kinerja) {
                                $html .= '<tr>';
                                    $html .= '<td>'.$no_sasaran_pd_indikator_kinerja++.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->deskripsi.'</td>';
                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->kondisi_target_kinerja_awal.'</td>';
                                    $b = 1;
                                    foreach ($tahuns as $tahun) {
                                        $cek_sasaran_pd_target_satuan_rp_realisasi = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $sasaran_pd_indikator_kinerja->id)
                                                                                            ->where('tahun', $tahun)->first();
                                        if($cek_sasaran_pd_target_satuan_rp_realisasi)
                                        {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
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
                                                    $html .= '<td><span class="sasaran-pd-span-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'">'.$cek_sasaran_pd_target_satuan_rp_realisasi->target.'</span></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $cek_sasaran_pd_realisasi_renja = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $cek_sasaran_pd_target_satuan_rp_realisasi->id)->first();
                                                    if($cek_sasaran_pd_realisasi_renja)
                                                    {
                                                        $html .= '<td>'.$cek_sasaran_pd_realisasi_renja->realisasi.'</td>';
                                                    } else {
                                                        $html .= '<td></td>';
                                                    }
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-tertiary mb-1 button-sasaran-pd-edit-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'" data-sasaran-pd-target-satuan-rp-realisasi="'.$cek_sasaran_pd_target_satuan_rp_realisasi->id.'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-gear undefined"><path d="M8.32233 3.75427C8.52487 1.45662 11.776 1.3967 11.898 3.68836C11.9675 4.99415 13.2898 5.76859 14.4394 5.17678C16.4568 4.13815 18.0312 7.02423 16.1709 8.35098C15.111 9.10697 15.0829 10.7051 16.1171 11.4225C17.932 12.6815 16.2552 15.6275 14.273 14.6626C13.1434 14.1128 11.7931 14.9365 11.6777 16.2457C11.4751 18.5434 8.22404 18.6033 8.10202 16.3116C8.03249 15.0059 6.71017 14.2314 5.56062 14.8232C3.54318 15.8619 1.96879 12.9758 3.82906 11.649C4.88905 10.893 4.91709 9.29487 3.88295 8.57749C2.06805 7.31848 3.74476 4.37247 5.72705 5.33737C6.85656 5.88718 8.20692 5.06347 8.32233 3.75427Z"></path><path d="M10 8C11.1046 8 12 8.89543 12 10V10C12 11.1046 11.1046 12 10 12V12C8.89543 12 8 11.1046 8 10V10C8 8.89543 8.89543 8 10 8V8Z"></path></svg>
                                                                    </button>
                                                                </td>';
                                                    }
                                                $html .='</tr>';
                                            }
                                            $b++;
                                        } else {
                                            if($b == 1)
                                            {
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td>'.$tahun.'</td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
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
                                                    $html .= '<td><input type="number" class="form-control sasaran-pd-add-target '.$tahun.' data-sasaran-pd-indikator-kinerja-'.$sasaran_pd_indikator_kinerja->id.'"></td>';
                                                    $html .= '<td>'.$sasaran_pd_indikator_kinerja->satuan.'</td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$tahun.'</td>';
                                                    if($sasaran_pd_indikator_kinerja->status_lock == '1')
                                                    {
                                                        $html .= '<td></td>';
                                                    } else {
                                                        $html .= '<td><button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-sasaran-pd-target-satuan-rp-realisasi" type="button" data-sasaran-pd-indikator-kinerja-id="'.$sasaran_pd_indikator_kinerja->id.'" data-tahun="'.$tahun.'">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>
                                                                </button>
                                                            </td>';
                                                    }
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

        return response()->json(['success' => 'Berhasil menghapus program dari kelompok sasaran pd', 'html' => $html, 'sasaran_id' => $sasaran['id']]);
    }
}
