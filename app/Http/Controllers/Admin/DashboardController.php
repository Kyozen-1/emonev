<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use RealRashid\SweetAlert\Facades\Alert;
use DB;
use Validator;
use Carbon\Carbon;
use App\Models\MasterOpd;
use App\Models\TahunPeriode;
use App\Models\Urusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Tujuan;
use App\Models\Sasaran;
use App\Models\TujuanPd;
use App\Models\SasaranPd;
use App\Models\SubKegiatanTargetSatuanRpRealisasi;
use App\Models\SubKegiatanTwRealisasi;
use App\Models\MasterTw;
use App\Models\ProgramIndikatorKinerja;
use App\Models\OpdProgramIndikatorKinerja;
use App\Models\ProgramTargetSatuanRpRealisasi;
use App\Models\ProgramTwRealisasi;
use App\Models\KegiatanIndikatorKinerja;
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\KegiatanTargetSatuanRpRealisasi;
use App\Models\KegiatanTwRealisasi;
use App\Models\SubKegiatanIndikatorKinerja;
use App\Models\OpdSubKegiatanIndikatorKinerja;
class DashboardController extends Controller
{
    public function index()
    {
        $getTahunPeriode = TahunPeriode::where('status', 'Aktif')->first();
        $countUrusan = Urusan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countProgram = Program::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countKegiatan = Kegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countSubKegiatan = SubKegiatan::where('tahun_periode_id', $getTahunPeriode->id)->count();
        $countOpd = MasterOpd::count();
        $countTujuan = Tujuan::count();
        $countSasaran = Sasaran::count();
        $targetAnggaran = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_awal');

        $targetAnggaranPerubahan = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
            $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                    $q->where('tahun_periode_id', $getTahunPeriode->id);
                });
            });
        })->sum('target_anggaran_perubahan');


        $targetRealisasi = SubKegiatanTwRealisasi::whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) use ($getTahunPeriode){
            $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                $q->whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($getTahunPeriode){
                    $q->whereHas('sub_kegiatan', function($q) use ($getTahunPeriode){
                        $q->where('tahun_periode_id', $getTahunPeriode->id);
                    });
                });
            });
        })->sum('realisasi_rp');

        return view('admin.dashboard.index', [
            'getTahunPeriode' => $getTahunPeriode,
            'countUrusan' => $countUrusan,
            'countProgram' => $countProgram,
            'countKegiatan' => $countKegiatan,
            'countSubKegiatan' => $countSubKegiatan,
            'countOpd' => $countOpd,
            'countTujuan' => $countTujuan,
            'countSasaran' => $countSasaran,
            'targetAnggaran' => $targetAnggaran,
            'targetAnggaranPerubahan' => $targetAnggaranPerubahan,
            'targetRealisasi' => $targetRealisasi
        ]);
    }

    public function change(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user->color_layout = $request->color_layout;
        $user->nav_color = $request->nav_color;
        $user->behaviour = $request->behaviour;
        $user->layout = $request->layout;
        $user->radius = $request->radius;
        $user->placement = $request->placement;
        $user->save();
    }

    public function normalisasi_opd()
    {
        $sekretariat_daerah = [
            'Biro Administrasi Pimpinan',
            'Biro Perekonomian dan Administrasi Pembangunan',
            'Biro Pemerintahan dan Kesejahteraan Rakyat',
            'Biro Hukum',
            'Biro Organisasi dan Reformasi Birokrasi',
            'Biro Umum',
            'Biro Pengadaan Barang / Jasa'
        ];

        for ($i=0; $i < count($sekretariat_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 2;
            $master_opd->nama = $sekretariat_daerah[$i];
            $master_opd->save();
        }

        $dinas_daerah = [
            'Dinas Perpustakaan dan Kearsipan',
            'Dinas Pemberdayaan Perempuan, Perlindungan Anak, Kependudukan dan Keluarga Berencana',
            'Dinas Lingkungan Hidup dan Kehutanan',
            'Dinas Ketahanan Pangan',
            'Dinas Perumahan Rakyat dan Kawasan Permukiman',
            'Dinas Pekerjaan Umum dan Penataan Ruang',
            'Dinas Kesehatan',
            'Dinas Pendidikan dan Kebudayaan',
            'Dinas Kepemudaan dan Olah Raga',
            'Dinas Pertanian',
            'Dinas Kelautan dan Perikanan',
            'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
            'Dinas Pemberdayaan Masyarakat dan Desa',
            'Dinas Komunikasi, Informatika, Statistik dan Persandian',
            'Dinas Perhubungan',
            'Dinas Energi dan Sumberdaya Mineral',
            'Dinas Perindustrian dan Perdagangan',
            'Dinas Pariwisata',
            'Dinas Sosial',
            'Dinas Tenaga Kerja dan Transmigrasi',
            'Dinas Koperasi, Usaha Kecil dan Menengah'
        ];

        for ($i=0; $i < count($dinas_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 3;
            $master_opd->nama = $dinas_daerah[$i];
            $master_opd->save();
        }

        $sekretariat_dprd = [
            'Sekretariat Dewan Perwakilan Rakyat Daerah (DPRD) Provinsi Banten'
        ];

        for ($i=0; $i < count($sekretariat_dprd); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 4;
            $master_opd->nama = $sekretariat_dprd [$i];
            $master_opd->save();
        }

        $lembaga_teknis_daerah = [
            'Inspektorat',
            'Badan Pengelolaan Keuangan dan Aset Daerah',
            'Badan Kepegawaian Daerah',
            'Badan Pengembangan Sumber Daya Manusia',
            'Badan Kesatuan Bangsa dan Politik',
            'Badan Pendapatan Daerah',
            'Badan Perencanaan Pembangunan Daerah',
            'Badan Penanggulangan Bencana Daerah',
            'Badan Penghubung'
        ];

        for ($i=0; $i < count($lembaga_teknis_daerah); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 5;
            $master_opd->nama = $lembaga_teknis_daerah [$i];
            $master_opd->save();
        }

        $satuan_polisi_pamong_praja = [
            'Satuan Polisi Pamong Praja (SATPOLPP)'
        ];

        for ($i=0; $i < count($satuan_polisi_pamong_praja); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 6;
            $master_opd->nama = $satuan_polisi_pamong_praja[$i];
            $master_opd->save();
        }

        $lembaga_lainnya = [
            'Rumah Sakit Umum Daerah'
        ];

        for ($i=0; $i < count($lembaga_lainnya); $i++) {
            $master_opd = new MasterOpd;
            $master_opd->jenis_opd_id = 7;
            $master_opd->nama = $lembaga_lainnya[$i];
            $master_opd->save();
        }

        return 'berhasil';
    }

    public function grafik_tujuan_pd()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = TujuanPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_sasaran_pd()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = SasaranPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_tujuan_pd_bar()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = TujuanPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_sasaran_pd_bar()
    {
        $opds = MasterOpd::all();

        $jumlah = [];
        foreach ($opds as $opd) {
            $jumlah[] = SasaranPd::where('opd_id', $opd->id)->count();
        }

        $nama = [];
        foreach ($opds as $opd) {
            $nama[] = $opd->nama;
        }

        return response()->json([
            'jumlah' => $jumlah,
            'nama' => $nama
        ]);
    }

    public function grafik_program()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = Program::whereHas('program_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_program[] = [
                    'x' => $nama,
                    'y' => Program::whereHas('program_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('program_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('program_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_program' => $data_program
            ]);
        }
    }

    public function grafik_kegiatan()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = Kegiatan::whereHas('kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_kegiatan[] = [
                    'x' => $nama,
                    'y' => Kegiatan::whereHas('kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('kegiatan_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('kegiatan_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_kegiatan' => $data_kegiatan
            ]);
        }
    }

    public function grafik_sub_kegiatan()
    {
        if(request()->ajax())
        {
            $opds = MasterOpd::pluck('nama', 'id');
            foreach ($opds as $id => $nama) {
                $harapan = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                });
                            })->count();
                $data_sub_kegiatan[] = [
                    'x' => $nama,
                    'y' => SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($id){
                                    $q->where('opd_id', $id);
                                    $q->whereHas('sub_kegiatan_target_satuan_rp_realisasi', function($q) {
                                        $q->whereHas('sub_kegiatan_tw_realisasi');
                                    });
                                });
                            })->count(),
                    'goals' => [[
                        'name' => 'Rencana',
                        'value' => $harapan,
                        'strokeWidth' => 5,
                        'strokeColor' => '#775DD0'
                    ]]
                ];
            }

            return response()->json([
                'data_sub_kegiatan' => $data_sub_kegiatan
            ]);
        }
    }

    public function normalisasi_sasaran_program_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $program_indikator_kinerjas = ProgramIndikatorKinerja::where('program_id', $get_program->id)
                ->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                })->get();

                foreach ($program_indikator_kinerjas as $program_indikator_kinerja) {
                    $opd_program_indikator_kinerjas = OpdProgramIndikatorKinerja::where('program_indikator_kinerja_id', $program_indikator_kinerja->id)
                                                    ->where('opd_id', $opd_id)
                                                    ->get();
                    foreach ($opd_program_indikator_kinerjas as $opd_program_indikator_kinerja) {
                        foreach ($tahuns as $tahun)
                        {
                            $cek_program_target_satuan_rp_realisasi = ProgramTargetSatuanRpRealisasi::where('opd_program_indikator_kinerja_id', $opd_program_indikator_kinerja->id)
                                                                                                        ->where('tahun', $tahun)
                                                                                                        ->first();
                            if($cek_program_target_satuan_rp_realisasi)
                            {
                                foreach ($tws as $tw)
                                {
                                    $cek_program_tw_realisasi_renja = ProgramTwRealisasi::where('program_target_satuan_rp_realisasi_id', $cek_program_target_satuan_rp_realisasi->id)->where('tw_id', $tw->id)->first();
                                    if($cek_program_tw_realisasi_renja)
                                    {
                                        if (!$cek_program_tw_realisasi_renja->sasaran_id) {
                                            $updateProgram = ProgramTwRealisasi::find($cek_program_tw_realisasi_renja->id);
                                            $updateProgram->sasaran_id = $get_sasaran->id;
                                            $updateProgram->save();
                                            $a++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $a;
    }

    public function normalisasi_sasaran_kegiatan_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $get_kegiatans = Kegiatan::where('program_id', $get_program->id)
                                ->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    });
                                })->get();
                foreach ($get_kegiatans as $get_kegiatan) {
                    $kegiatan_indikator_kinerjas = KegiatanIndikatorKinerja::where('kegiatan_id', $get_kegiatan->id)
                                                        ->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                                            $q->where('opd_id', $opd_id);
                                                        })->get();
                    foreach ($kegiatan_indikator_kinerjas as $kegiatan_indikator_kinerja) {
                        foreach ($tahuns as $tahun)
                        {
                            $cek_kegiatan_target_satuan_rp_realisasi = KegiatanTargetSatuanRpRealisasi::whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($kegiatan_indikator_kinerja, $opd_id){
                                $q->where('opd_id', $opd_id);
                                $q->where('kegiatan_indikator_kinerja_id', $kegiatan_indikator_kinerja->id);
                            })->where('tahun', $tahun)->first();

                            if($cek_kegiatan_target_satuan_rp_realisasi)
                            {
                                foreach ($tws as $tw) {
                                    $cek_kegiatan_tw_realisasi_renja = KegiatanTwRealisasi::where('kegiatan_target_satuan_rp_realisasi_id', $cek_kegiatan_target_satuan_rp_realisasi->id)
                                                                        ->where('tw_id', $tw->id)->first();
                                    if($cek_kegiatan_tw_realisasi_renja)
                                    {
                                        if(!$cek_kegiatan_tw_realisasi_renja->sasaran_id && !$cek_kegiatan_tw_realisasi_renja->program_id)
                                        {
                                            $updateKegiatan = KegiatanTwRealisasi::find($cek_kegiatan_tw_realisasi_renja->id);
                                            $updateKegiatan->sasaran_id = $get_sasaran->id;
                                            $updateKegiatan->program_id = $get_program->id;
                                            $updateKegiatan->save();
                                            $a++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        return $a;
    }

    public function normalisasi_sasaran_subkegiatan_realisasi($opd_id)
    {
        $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
        $tahun_awal = $get_periode->tahun_awal;
        $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
        $tahuns = [];
        for ($i=0; $i < $jarak_tahun + 1; $i++) {
            $tahuns[] = $tahun_awal + $i;
        }

        $tws = MasterTw::all();

        $get_sasarans = Sasaran::whereHas('sasaran_indikator_kinerja', function($q) use ($opd_id){
            $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($opd_id){
                $q->whereHas('program_rpjmd', function($q) use ($opd_id){
                    $q->whereHas('program', function($q) use ($opd_id){
                        $q->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                                $q->where('opd_id', $opd_id);
                            });
                        });
                    });
                });
            });
        })->get();
        $a = 1;
        foreach ($get_sasarans as $get_sasaran) {
            $get_programs = Program::whereHas('program_rpjmd', function($q) use ($get_sasaran){
                $q->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran) {
                    $q->whereHas('sasaran_indikator_kinerja', function($q) use ($get_sasaran){
                        $q->whereHas('sasaran', function($q) use ($get_sasaran) {
                            $q->where('id', $get_sasaran->id);
                        });
                    });
                });
            })->whereHas('program_indikator_kinerja', function($q) use ($opd_id){
                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($opd_id){
                    $q->where('opd_id', $opd_id);
                });
            })->get();

            foreach ($get_programs as $get_program) {
                $get_kegiatans = Kegiatan::where('program_id', $get_program->id)
                                ->whereHas('kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                    $q->whereHas('opd_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                                        $q->where('opd_id', $opd_id);
                                    });
                                })->get();
                foreach ($get_kegiatans as $get_kegiatan) {
                    $get_sub_kegiatans = SubKegiatan::whereHas('sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                        $q->whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
                        });
                    })->where('kegiatan_id', $get_kegiatan->id)->get();

                    foreach ($get_sub_kegiatans as $get_sub_kegiatan) {
                        $sub_kegiatan_indikator_kinerjas = SubKegiatanIndikatorKinerja::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($opd_id){
                            $q->where('opd_id', $opd_id);
                        })->where('sub_kegiatan_id', $get_sub_kegiatan->id)->get();
                        foreach ($sub_kegiatan_indikator_kinerjas as $sub_kegiatan_indikator_kinerja) {
                            foreach($tahuns as $tahun)
                            {
                                $cek_sub_kegiatan_target_satuan_rp_realisasi = SubKegiatanTargetSatuanRpRealisasi::whereHas('opd_sub_kegiatan_indikator_kinerja', function($q) use ($sub_kegiatan_indikator_kinerja, $opd_id){
                                    $q->where('opd_id', $opd_id);
                                    $q->where('sub_kegiatan_indikator_kinerja_id', $sub_kegiatan_indikator_kinerja->id);
                                })->where('tahun', $tahun)->first();
                                if($cek_sub_kegiatan_target_satuan_rp_realisasi)
                                {
                                    foreach($tws as $tw)
                                    {
                                        $cek_sub_kegiatan_tw_realisasi_renja = SubKegiatanTwRealisasi::where('sub_kegiatan_target_satuan_rp_realisasi_id', $cek_sub_kegiatan_target_satuan_rp_realisasi->id)
                                                                                    ->where('tw_id', $tw->id)->first();
                                        if($cek_sub_kegiatan_tw_realisasi_renja)
                                        {
                                            if(!$cek_sub_kegiatan_tw_realisasi_renja->sasaran_id && !$cek_sub_kegiatan_tw_realisasi_renja->program_id && !$cek_sub_kegiatan_tw_realisasi_renja->kegiatan_id)
                                            {
                                                $updateSubKegiatan = SubKegiatanTwRealisasi::find($cek_sub_kegiatan_tw_realisasi_renja->id);
                                                $updateSubKegiatan->sasaran_id = $get_sasaran->id;
                                                $updateSubKegiatan->program_id = $get_program->id;
                                                $updateSubKegiatan->kegiatan_id = $get_kegiatan->id;
                                                $updateSubKegiatan->save();
                                                $a++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
        return $a;
    }
}
