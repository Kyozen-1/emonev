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
use Auth;

class Tc14Controller extends Controller
{
    public function index()
    {
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
                        $get_tujuan_indikators = PivotTujuanIndikator::where('tujuan_id', $tujuan['id'])->get();
                        foreach ($get_tujuan_indikators as $get_tujuan_indikator) {
                            if($a == 0)
                            {
                                $html .= '<td>'.$get_tujuan_indikator->indikator.'</td>';
                                $html .= '</tr>';
                            } else {
                                $html .= '<tr>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td></td>';
                                    $html .= '<td>'.$get_tujuan_indikator->indikator.'</td>';
                                $html .= '</tr>';
                            }
                            $a++;
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
                            $get_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->latest()->get();
                            foreach ($get_sasaran_indikators as $get_sasaran_indikator) {
                                if($b == 0)
                                {
                                        $html .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                    $html .= '</tr>';
                                } else {
                                    $html .= '<tr>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td></td>';
                                        $html .= '<td>'.$get_sasaran_indikator->indikator.'</td>';
                                    $html .= '</tr>';
                                }
                                $b++;
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
