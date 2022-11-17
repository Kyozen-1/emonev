<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
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
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;
use App\Models\MasterOpd;
use App\Models\PivotSasaranIndikatorProgramRpjmd;
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotProgramKegiatanRenstra;
use App\Models\TargetRpPertahunProgram;
use App\Models\RenstraKegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\Kegiatan;
use App\Models\PivotOpdRentraKegiatan;
use App\Models\TargetRpPertahunRenstraKegiatan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\SasaranIndikatorKinerja;
use App\Models\SasaranTargetSatuanRpRealisasi;
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
use App\Models\OpdKegiatanIndikatorKinerja;

class Tc19Ekspor implements FromView
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function view(): View
    {
        $tahun_awal = $this->tahun;
        // TC 19 Start
        $tc_19 = '';

        $get_urusans = Urusan::orderBy('kode', 'asc')->where('tahun_perubahan', $tahun_awal)->get();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)
                                        ->where('tahun_perubahan', $tahun_awal)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi
                ];
            }
        }
        $a = 1;
        foreach ($urusans as $urusan) {
            $tc_19 .= '<tr>';
                $tc_19 .= '<td>'.$a++.'</td>';
                $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td></td>';
                $tc_19 .= '<td>'.$urusan['deskripsi'].'</td>';
            $tc_19 .= '</tr>';

            $get_programs = Program::where('urusan_id', $urusan['id'])->get();
            $programs = [];
            foreach ($get_programs as $get_program) {
                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)
                                            ->where('tahun_perubahan', $tahun_awal)->latest()->first();
                if($cek_perubahan_program)
                {
                    $programs[] = [
                        'id' => $cek_perubahan_program->program_id,
                        'kode' => $cek_perubahan_program->kode,
                        'deskripsi' => $cek_perubahan_program->deskripsi
                    ];
                } else {
                    $programs[] = [
                        'id' => $get_program->id,
                        'kode' => $get_program->kode,
                        'deskripsi' => $get_program->deskripsi
                    ];
                }
            }
            // Program
            foreach ($programs as $program) {
                $tc_19 .= '<tr>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                    $tc_19 .= '<td>'.$program['kode'].'</td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td></td>';
                    $tc_19 .= '<td>'.$program['deskripsi'].'</td>';

                    $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $program['id'])->get();
                    $b = 1;
                    foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                            $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($program_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$program_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($program_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$program_target_satuan_rp_realisasi->realisasi.'/'.$program_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($program_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_program_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }

                // Kegiatan
                $get_kegiatans = Kegiatan::where('program_id', $program['id'])->get();
                $kegiatans = [];
                foreach ($get_kegiatans as $get_kegiatan) {
                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $get_kegiatan->id)->latest()->first();
                    if($cek_perubahan_kegiatan)
                    {
                        $kegiatans[] = [
                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                            'kode' => $cek_perubahan_kegiatan->kode,
                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi
                        ];
                    } else {
                        $kegiatans[] = [
                            'id' => $get_kegiatan->id,
                            'kode' => $get_kegiatan->kode,
                            'deskripsi' => $get_kegiatan->deskripsi
                        ];
                    }
                }

                foreach ($kegiatans as $kegiatan) {
                    $tc_19 .= '<tr>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$urusan['kode'].'</td>';
                        $tc_19 .= '<td>'.$program['kode'].'</td>';
                        $tc_19 .= '<td>'.$kegiatan['kode'].'</td>';
                        $tc_19 .= '<td></td>';
                        $tc_19 .= '<td>'.$kegiatan['deskripsi'].'</td>';

                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $kegiatan['id'])->get();
                    $b = 1;
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        if($b == 1)
                        {
                            $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                            $opd_kegiatan_indikator_kinerjas = OpdKegiatanIndikatorKinerja::where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id)->get();
                            $opd = [];
                            $c = 1;
                            foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                if($c == 1)
                                {
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                } else {
                                    $tc_19 .= '<tr>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $tc_19 .= '<td></td>';
                                        $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                ->where('tahun', $tahun_awal)->first();
                                        if($kegiatan_target_satuan_rp_realisasi)
                                        {
                                            $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                            $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                        } else {
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                        }
                                        $tc_19 .= '<td colspan="12"></td>';
                                        $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                        $tc_19 .= '<td></td>';
                                    $tc_19 .= '</tr>';
                                }
                                $c++;
                            }
                        } else {
                            $tc_19 .= '<tr>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td></td>';
                                $tc_19 .= '<td>'.$kegiatan_indikator_kinerja->deskripsi.'</td>';
                                foreach ($opd_kegiatan_indikator_kinerjas as $opd_kegiatan_indikator_kinerja) {
                                    if($c == 1)
                                    {
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    } else {
                                        $tc_19 .= '<tr>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $tc_19 .= '<td></td>';
                                            $kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::where('opd_kegiatan_indikator_kinerja_id', $opd_kegiatan_indikator_kinerja->id)
                                                                                    ->where('tahun', $tahun_awal)->first();
                                            if($kegiatan_target_satuan_rp_realisasi)
                                            {
                                                $tc_19 .= '<td>'.$kegiatan_target_satuan_rp_realisasi->realisasi.'/'.$kegiatan_target_satuan_rp_realisasi->satuan.'</td>';
                                                $tc_19 .= '<td>Rp. '.number_format($kegiatan_target_satuan_rp_realisasi->realisasi_rp,2).'</td>';
                                            } else {
                                                $tc_19 .= '<td></td>';
                                                $tc_19 .= '<td></td>';
                                            }
                                            $tc_19 .= '<td colspan="12"></td>';
                                            $tc_19 .= '<td>'.$opd_kegiatan_indikator_kinerja->opd->nama.'</td>';
                                            $tc_19 .= '<td></td>';
                                        $tc_19 .= '</tr>';
                                    }
                                    $c++;
                                }
                        }
                        $b++;
                    }
                }
            }
        }
        // TC 19 End

        return view('admin.laporan.tc-19', [
            'tc_19' => $tc_19,
            'tahun' => $this->tahun
        ]);
    }
}
