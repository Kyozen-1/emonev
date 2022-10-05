<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Validator;
use Carbon\Carbon;
use Auth;
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\PivotTujuanIndikator;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;
use App\Models\ProgramRpjmd;
use App\Models\PivotPerubahanProgramRpjmd;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\MasterOpd;
use App\Models\TargetRpPertahunTujuan;
use App\Models\TargetRpPertahunSasaran;
use App\Models\TargetRpPertahunProgram;
use App\Models\PivotProgramIndikator;
use App\Models\TahunPeriode;

class Tc14Controller extends Controller
{
    public function index()
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'kode' => $cek_perubahan_visi->kode,
                    'deskripsi' => $cek_perubahan_visi->deskripsi
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'kode' => $get_visi->kode,
                    'deskripsi' => $get_visi->deskripsi
                ];
            }
        }
        $html = '';
        foreach ($visis as $visi) {
            $get_misis = Misi::where('visi_id', $visi['id'])->get();
            $misis = [];
            foreach ($get_misis as $get_misi) {
                $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)->latest()->first();
                if($cek_perubahan_misi)
                {
                    $misis[] = [
                        'id' => $cek_perubahan_misi->misi_id,
                        'kode' => $cek_perubahan_misi->kode,
                        'deskripsi' => $cek_perubahan_misi->deskripsi
                    ];
                } else {
                    $misis[] = [
                        'id' => $get_misi->id,
                        'kode' => $get_misi->kode,
                        'deskripsi' => $get_misi->deskripsi
                    ];
                }
            }
            foreach ($misis as $misi) {
                $html .= '<tr>';
                    $html .= '<td colspan="19" style="text-align:left;"><strong>Misi</strong></td>';
                $html .='</tr>';
                $html .= '<tr>';
                    $html .= '<td>'.$misi['kode'].'</td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td>'.$misi['deskripsi'].'</td>';
                    $html .= '<td colspan="15"></td>';
                $html .= '</tr>';
                $get_tujuans = Tujuan::where('misi_id', $misi['id'])->latest()->get();
                $tujuans = [];
                foreach ($get_tujuans as $get_tujuan) {
                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->latest()->first();
                    if($cek_perubahan_tujuan)
                    {
                        $tujuans[] = [
                            'id' => $cek_perubahan_tujuan->tujuan_id,
                            'kode' => $cek_perubahan_tujuan->kode,
                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                        ];
                    } else {
                        $tujuans[] = [
                            'id' => $get_tujuan->id,
                            'kode' => $get_tujuan->kode,
                            'deskripsi' => $get_tujuan->deskripsi,
                        ];
                    }
                }
                foreach ($tujuans as $tujuan) {
                    $html .= '<tr>';
                        $html .= '<td colspan="19" style="text-align:left;"><strong>Tujuan</strong></td>';
                    $html .='</tr>';
                    $html .= '<tr>';
                        $html .= '<td>'.$misi['kode'].'</td>';
                        $html .= '<td>'.$tujuan['kode'].'</td>';
                        $html .= '<td></td>';
                        $html .= '<td>'.$tujuan['deskripsi'].'</td>';
                        $a = 0;
                        $get_opds = TargetRpPertahunTujuan::select('opd_id')->distinct()->get();
                        foreach ($get_opds as $get_opd) {
                            $get_tujuan_indikators = PivotTujuanIndikator::whereHas('target_rp_pertahun_tujuan', function($q) use ($get_opd){
                                $q->where('opd_id', $get_opd->opd_id);
                            })->where('tujuan_id', $tujuan['id'])->get();
                            foreach ($get_tujuan_indikators as $get_tujuan_indikator) {
                                $last_target = '';
                                $last_rp = '';
                                $nama_opd = '';
                                $indikator_a = 0;
                                $len = count($tahuns);
                                if($a == 0)
                                {
                                    $html .= '<td>'.$get_tujuan_indikator->indikator.'</td>';
                                    $html .= '<td></td>';
                                    foreach ($tahuns as $tahun) {
                                        $target_rp_pertahun_tujuan = TargetRpPertahunTujuan::where('pivot_tujuan_indikator_id', $get_tujuan_indikator->id)
                                                                        ->where('tahun', $tahun)
                                                                        ->where('opd_id', $get_opd->opd_id)
                                                                        ->first();
                                        if($target_rp_pertahun_tujuan)
                                        {
                                            if ($indikator_a == 0) {
                                                $nama_opd = $target_rp_pertahun_tujuan->opd->nama;
                                            }

                                            $html .= '<td>'.$target_rp_pertahun_tujuan->target.'</td>';
                                            $html .= '<td>'.$target_rp_pertahun_tujuan->rp.'</td>';

                                            if($indikator_a == $len - 1)
                                            {
                                                $last_target = $target_rp_pertahun_tujuan->target;
                                                $last_rp = $target_rp_pertahun_tujuan->rp;

                                                $html .= '<td>'.$last_target.'</td>';
                                                $html .= '<td>'.$last_rp.'</td>';
                                            }
                                        } else {
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            if($indikator_a == $len - 1)
                                            {
                                                $html .= '<td></td>';
                                                $html .= '<td></td>';
                                            }
                                        }
                                        $indikator_a++;
                                    }
                                    $html .= '<td>'.$nama_opd.'</td>';
                                    $html .= '</tr>';
                                } else {
                                    $html .= '<tr>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$get_tujuan_indikator->indikator.'</td>';
                                        $html .= '<td></td>';
                                        foreach ($tahuns as $tahun) {
                                            $target_rp_pertahun_tujuan = TargetRpPertahunTujuan::where('pivot_tujuan_indikator_id', $get_tujuan_indikator->id)
                                                                            ->where('tahun', $tahun)
                                                                            ->where('opd_id', $get_opd->opd_id)
                                                                            ->first();
                                            if($target_rp_pertahun_tujuan)
                                            {
                                                if ($indikator_a == 0) {
                                                    $nama_opd = $target_rp_pertahun_tujuan->opd->nama;
                                                }

                                                $html .= '<td>'.$target_rp_pertahun_tujuan->target.'</td>';
                                                $html .= '<td>'.$target_rp_pertahun_tujuan->rp.'</td>';

                                                if($indikator_a == $len - 1)
                                                {
                                                    $last_target = $target_rp_pertahun_tujuan->target;
                                                    $last_rp = $target_rp_pertahun_tujuan->rp;

                                                    $html .= '<td>'.$last_target.'</td>';
                                                    $html .= '<td>'.$last_rp.'</td>';
                                                }
                                            } else {
                                                $html .= '<td></td>';
                                                $html .= '<td></td>';
                                                if($indikator_a == $len - 1)
                                                {
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                }
                                            }
                                            $indikator_a++;
                                        }
                                        $html .= '<td>'.$nama_opd.'</td>';
                                    $html .= '</tr>';
                                }
                                $a++;
                            }
                        }


                    $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                    $sasarans = [];
                    foreach ($get_sasarans as $get_sasaran) {
                        $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                        if($cek_perubahan_sasaran)
                        {
                            $sasarans[] = [
                                'id' => $cek_perubahan_sasaran->sasaran_id,
                                'kode' => $cek_perubahan_sasaran->kode,
                                'deskripsi' => $cek_perubahan_sasaran->deskripsi
                            ];
                        } else {
                            $sasarans[] = [
                                'id' => $get_sasaran->id,
                                'kode' => $get_sasaran->kode,
                                'deskripsi' => $get_sasaran->deskripsi
                            ];
                        }
                    }
                    foreach ($sasarans as $sasaran) {
                        $html .= '<tr>';
                            $html .= '<td colspan="19" style="text-align:left;"><strong>Sasaran</strong></td>';
                        $html .='</tr>';
                        $html .= '<tr>';
                            $html .= '<td>'.$misi['kode'].'</td>';
                            $html .= '<td>'.$tujuan['kode'].'</td>';
                            $html .= '<td>'.$sasaran['kode'].'</td>';
                            $html .= '<td>'.$sasaran['deskripsi'].'</td>';
                            $b = 0;
                            $get_opds = TargetRpPertahunSasaran::select('opd_id')->distinct()->get();
                            foreach ($get_opds as $get_opd) {
                                $get_sasaran_indikators = PivotSasaranIndikator::whereHas('target_rp_pertahun_sasaran', function($q) use ($get_opd){
                                    $q->where('opd_id', $get_opd->opd_id);
                                })->where('sasaran_id', $sasaran['id'])->get();
                                foreach ($get_sasaran_indikators as $get_sasaran_indikator) {
                                    $last_target = '';
                                    $last_rp = '';
                                    $nama_opd = '';
                                    $indikator_b = 0;
                                    $len = count($tahuns);
                                    if($b == 0)
                                    {
                                            $html .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                            $html .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $target_rp_pertahun_sasaran = TargetRpPertahunSasaran::where('pivot_sasaran_indikator_id', $get_sasaran_indikator->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->where('opd_id', $get_opd->opd_id)
                                                                                ->first();
                                                if($target_rp_pertahun_sasaran)
                                                {
                                                    if ($indikator_b == 0) {
                                                        $nama_opd = $target_rp_pertahun_sasaran->opd->nama;
                                                    }

                                                    $html .= '<td>'.$target_rp_pertahun_sasaran->target.'</td>';
                                                    $html .= '<td>'.$target_rp_pertahun_sasaran->rp.'</td>';

                                                    if($indikator_b == $len - 1)
                                                    {
                                                        $last_target = $target_rp_pertahun_sasaran->target;
                                                        $last_rp = $target_rp_pertahun_sasaran->rp;

                                                        $html .= '<td>'.$last_target.'</td>';
                                                        $html .= '<td>'.$last_rp.'</td>';
                                                    }
                                                } else {
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    if($indikator_b == $len - 1)
                                                    {
                                                        $html .= '<td></td>';
                                                        $html .= '<td></td>';
                                                    }
                                                }
                                                $indikator_b++;
                                            }
                                            $html .= '<td>'.$nama_opd.'</td>';
                                        $html .= '</tr>';
                                    } else {
                                        $html .= '<tr>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td></td>';
                                            $html .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                            $html .= '<td></td>';
                                            foreach ($tahuns as $tahun) {
                                                $target_rp_pertahun_sasaran = TargetRpPertahunSasaran::where('pivot_sasaran_indikator_id', $get_sasaran_indikator->id)
                                                                                ->where('tahun', $tahun)
                                                                                ->where('opd_id', $get_opd->opd_id)
                                                                                ->first();
                                                if($target_rp_pertahun_sasaran)
                                                {
                                                    if ($indikator_b == 0) {
                                                        $nama_opd = $target_rp_pertahun_sasaran->opd->nama;
                                                    }

                                                    $html .= '<td>'.$target_rp_pertahun_sasaran->target.'</td>';
                                                    $html .= '<td>'.$target_rp_pertahun_sasaran->rp.'</td>';

                                                    if($indikator_b == $len - 1)
                                                    {
                                                        $last_target = $target_rp_pertahun_sasaran->target;
                                                        $last_rp = $target_rp_pertahun_sasaran->rp;

                                                        $html .= '<td>'.$last_target.'</td>';
                                                        $html .= '<td>'.$last_rp.'</td>';
                                                    }
                                                } else {
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    if($indikator_b == $len - 1)
                                                    {
                                                        $html .= '<td></td>';
                                                        $html .= '<td></td>';
                                                    }
                                                }
                                                $indikator_b++;
                                            }
                                            $html .= '<td>'.$nama_opd.'</td>';
                                        $html .= '</tr>';
                                    }
                                    $b++;
                                }
                            }

                            // Program Prioritas
                            $get_program_rpjmd_prioritases = ProgramRpjmd::where('sasaran_id', $sasaran['id'])
                                                                ->where('status_program', 'Program Prioritas')
                                                                ->get();
                            $program_rpjmd_prioritases = [];
                            $program_prioritases = [];
                            foreach ($get_program_rpjmd_prioritases as $get_program_rpjmd_prioritas) {
                                $cek_perubahan_program_rpjmd = PivotPerubahanProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd_prioritas->id)
                                                                ->latest()
                                                                ->first();
                                if ($cek_perubahan_program_rpjmd) {
                                    $program_rpjmd_prioritases[] = [
                                        'program_id' => $cek_perubahan_program_rpjmd->program_id
                                    ];
                                } else {
                                    $program_rpjmd_prioritases[] = [
                                        'program_id' => $get_program_rpjmd_prioritas->program_id
                                    ];
                                }
                            }
                            foreach ($program_rpjmd_prioritases as $program_rpjmd_prioritas) {
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd_prioritas['program_id'])->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $program_prioritases[] = [
                                        'id' => $cek_perubahan_program->program_id,
                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                    ];
                                } else {
                                    $program = Program::find($program_rpjmd_prioritas['program_id']);
                                    $program_prioritases[] = [
                                        'id' => $program['id'],
                                        'deskripsi' => $program->deskripsi
                                    ];
                                }
                            }
                            foreach ($program_prioritases as $program_prioritas) {
                                $html .= '<tr>';
                                    $html .= '<td colspan="19" style="text-align:left;"><strong>Program Prioritas</strong></td>';
                                $html .= '</tr>';
                                $html .= '<tr>';
                                    $html .= '<td>'.$misi['kode'].'</td>';
                                    $html .= '<td>'.$tujuan['kode'].'</td>';
                                    $html .= '<td>'.$sasaran['kode'].'</td>';
                                    $html .= '<td>'.$program_prioritas['deskripsi'].'</td>';
                                    $c = 0;
                                    $get_opds = TargetRpPertahunProgram::select('opd_id')->distinct()->get();
                                    foreach ($get_opds as $get_opd) {
                                        $get_program_indikators = PivotProgramIndikator::whereHas('target_rp_pertahun_program', function($q) use ($get_opd){
                                            $q->where('opd_id', $get_opd->opd_id);
                                        })->where('program_id', $program_prioritas['id'])->get();
                                        foreach ($get_program_indikators as $get_program_indikator) {
                                            $last_target = '';
                                            $last_rp = '';
                                            $nama_opd = '';
                                            $indikator_c = 0;
                                            $len = count($tahuns);
                                            if($c == 0)
                                            {
                                                    $html .= '<td>'.$get_program_indikator->indikator.'</td>';
                                                    $html .= '<td></td>';
                                                    foreach ($tahuns as $tahun) {
                                                        $target_rp_pertahun_program = TargetRpPertahunProgram::where('pivot_program_indikator_id', $get_program_indikator->id)
                                                                                        ->where('tahun', $tahun)
                                                                                        ->where('opd_id', $get_opd->opd_id)
                                                                                        ->first();
                                                        if($target_rp_pertahun_program)
                                                        {
                                                            if ($indikator_c == 0) {
                                                                $nama_opd = $target_rp_pertahun_program->opd->nama;
                                                            }

                                                            $html .= '<td>'.$target_rp_pertahun_program->target.'</td>';
                                                            $html .= '<td>'.$target_rp_pertahun_program->rp.'</td>';

                                                            if($indikator_c == $len - 1)
                                                            {
                                                                $last_target = $target_rp_pertahun_program->target;
                                                                $last_rp = $target_rp_pertahun_program->rp;

                                                                $html .= '<td>'.$last_target.'</td>';
                                                                $html .= '<td>'.$last_rp.'</td>';
                                                            }
                                                        } else {
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            if($indikator_c == $len - 1)
                                                            {
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                            }
                                                        }
                                                        $indikator_c++;
                                                    }
                                                    $html .= '<td>'.$nama_opd.'</td>';
                                                $html .= '</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$get_program_indikator->indikator.'</td>';
                                                    $html .= '<td></td>';
                                                    foreach ($tahuns as $tahun) {
                                                        $target_rp_pertahun_program = TargetRpPertahunProgram::where('pivot_program_indikator_id', $get_program_indikator->id)
                                                                                        ->where('tahun', $tahun)
                                                                                        ->where('opd_id', $get_opd->opd_id)
                                                                                        ->first();
                                                        if($target_rp_pertahun_program)
                                                        {
                                                            if ($indikator_c == 0) {
                                                                $nama_opd = $target_rp_pertahun_program->opd->nama;
                                                            }

                                                            $html .= '<td>'.$target_rp_pertahun_program->target.'</td>';
                                                            $html .= '<td>'.$target_rp_pertahun_program->rp.'</td>';

                                                            if($indikator_c == $len - 1)
                                                            {
                                                                $last_target = $target_rp_pertahun_program->target;
                                                                $last_rp = $target_rp_pertahun_program->rp;

                                                                $html .= '<td>'.$last_target.'</td>';
                                                                $html .= '<td>'.$last_rp.'</td>';
                                                            }
                                                        } else {
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            if($indikator_c == $len - 1)
                                                            {
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                            }
                                                        }
                                                        $indikator_c++;
                                                    }
                                                    $html .= '<td>'.$nama_opd.'</td>';
                                                $html .= '</tr>';
                                            }
                                            $c++;
                                        }
                                    }
                            }
                            // Program Pendukung
                            $get_program_rpjmd_pendukunges = ProgramRpjmd::where('sasaran_id', $sasaran['id'])
                                                                ->where('status_program', 'Program Pendukung')
                                                                ->get();
                            $program_rpjmd_pendukunges = [];
                            $program_pendukunges = [];
                            foreach ($get_program_rpjmd_pendukunges as $get_program_rpjmd_pendukung) {
                                $cek_perubahan_program_rpjmd = PivotPerubahanProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd_pendukung->id)
                                                                ->latest()
                                                                ->first();
                                if ($cek_perubahan_program_rpjmd) {
                                    $program_rpjmd_pendukunges[] = [
                                        'program_id' => $cek_perubahan_program_rpjmd->program_id
                                    ];
                                } else {
                                    $program_rpjmd_pendukunges[] = [
                                        'program_id' => $get_program_rpjmd_pendukung->program_id
                                    ];
                                }
                            }
                            foreach ($program_rpjmd_pendukunges as $program_rpjmd_pendukung) {
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd_pendukung['program_id'])->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $program_pendukunges[] = [
                                        'id' => $cek_perubahan_program->program_id,
                                        'deskripsi' => $cek_perubahan_program->deskripsi
                                    ];
                                } else {
                                    $program = Program::find($program_rpjmd_pendukung['program_id']);
                                    $program_pendukunges[] = [
                                        'id' => $program['id'],
                                        'deskripsi' => $program->deskripsi
                                    ];
                                }
                            }
                            foreach ($program_pendukunges as $program_pendukung) {
                                $html .= '<tr>';
                                    $html .= '<td colspan="19" style="text-align:left;"><strong>Program Pendukung</strong></td>';
                                $html .= '</tr>';
                                $html .= '<tr>';
                                    $html .= '<td>'.$misi['kode'].'</td>';
                                    $html .= '<td>'.$tujuan['kode'].'</td>';
                                    $html .= '<td>'.$sasaran['kode'].'</td>';
                                    $html .= '<td>'.$program_pendukung['deskripsi'].'</td>';
                                    $d = 0;
                                    $get_opds = TargetRpPertahunProgram::select('opd_id')->distinct()->get();
                                    foreach ($get_opds as $get_opd) {
                                        $get_program_indikators = PivotProgramIndikator::whereHas('target_rp_pertahun_program', function($q) use ($get_opd){
                                            $q->where('opd_id', $get_opd->opd_id);
                                        })->where('program_id', $program_pendukung['id'])->get();
                                        foreach ($get_program_indikators as $get_program_indikator) {
                                            $last_target = '';
                                            $last_rp = '';
                                            $nama_opd = '';
                                            $indikator_d = 0;
                                            $len = count($tahuns);

                                            if($d == 0)
                                            {
                                                    $html .= '<td>'.$get_program_indikator->indikator.'</td>';
                                                    $html .= '<td></td>';
                                                    foreach ($tahuns as $tahun) {
                                                        $target_rp_pertahun_program = TargetRpPertahunProgram::where('pivot_program_indikator_id', $get_program_indikator->id)
                                                                                        ->where('tahun', $tahun)
                                                                                        ->where('opd_id', $get_opd->opd_id)
                                                                                        ->first();
                                                        if($target_rp_pertahun_program)
                                                        {
                                                            if ($indikator_d == 0) {
                                                                $nama_opd = $target_rp_pertahun_program->opd->nama;
                                                            }

                                                            $html .= '<td>'.$target_rp_pertahun_program->target.'</td>';
                                                            $html .= '<td>'.$target_rp_pertahun_program->rp.'</td>';

                                                            if($indikator_d == $len - 1)
                                                            {
                                                                $last_target = $target_rp_pertahun_program->target;
                                                                $last_rp = $target_rp_pertahun_program->rp;

                                                                $html .= '<td>'.$last_target.'</td>';
                                                                $html .= '<td>'.$last_rp.'</td>';
                                                            }
                                                        } else {
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            if($indikator_d == $len - 1)
                                                            {
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                            }
                                                        }
                                                        $indikator_d++;
                                                    }
                                                    $html .= '<td>'.$nama_opd.'</td>';
                                                $html .= '</tr>';
                                            } else {
                                                $html .= '<tr>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td></td>';
                                                    $html .= '<td>'.$get_program_indikator->indikator.'</td>';
                                                    $html .= '<td></td>';
                                                    foreach ($tahuns as $tahun) {
                                                        $target_rp_pertahun_program = TargetRpPertahunProgram::where('pivot_program_indikator_id', $get_program_indikator->id)
                                                                                        ->where('tahun', $tahun)
                                                                                        ->where('opd_id', $get_opd->opd_id)
                                                                                        ->first();
                                                        if($target_rp_pertahun_program)
                                                        {
                                                            if ($indikator_d == 0) {
                                                                $nama_opd = $target_rp_pertahun_program->opd->nama;
                                                            }

                                                            $html .= '<td>'.$target_rp_pertahun_program->target.'</td>';
                                                            $html .= '<td>'.$target_rp_pertahun_program->rp.'</td>';

                                                            if($indikator_d == $len - 1)
                                                            {
                                                                $last_target = $target_rp_pertahun_program->target;
                                                                $last_rp = $target_rp_pertahun_program->rp;

                                                                $html .= '<td>'.$last_target.'</td>';
                                                                $html .= '<td>'.$last_rp.'</td>';
                                                            }
                                                        } else {
                                                            $html .= '<td></td>';
                                                            $html .= '<td></td>';
                                                            if($indikator_d == $len - 1)
                                                            {
                                                                $html .= '<td></td>';
                                                                $html .= '<td></td>';
                                                            }
                                                        }
                                                        $indikator_d++;
                                                    }
                                                    $html .= '<td>'.$nama_opd.'</td>';
                                                $html .= '</tr>';
                                            }
                                            $d++;
                                        }
                                    }
                            }
                    }
                }
            }
        }

        return view('admin.laporan.tc-14.index', [
            'html' => $html
        ]);
    }
}
