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

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index');
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
}
