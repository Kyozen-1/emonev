<?php

namespace App\Http\Controllers\Admin;

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
use App\Models\OpdKegiatanIndikatorKinerja;
use App\Models\SasaranTargetSatuanRpRealisasi;
use App\Models\SasaranTwRealisasi;
use App\Models\TujuanIndikatorKinerja;
use App\Models\TujuanTargetSatuanRpRealisasi;
use App\Models\TujuanTwRealisasi;

class MisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(request()->ajax())
        {
            $data = Misi::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function($data){
                    $button_show = '<button type="button" name="detail" id="'.$data->id.'" class="detail btn btn-icon waves-effect btn-success" title="Detail Data"><i class="fas fa-eye"></i></button>';
                    $button_edit = '<button type="button" name="edit" id="'.$data->id.'"
                    class="edit btn btn-icon waves-effect btn-warning" title="Edit Data"><i class="fas fa-edit"></i></button>';
                    // $button_delete = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-icon waves-effect btn-danger" title="Delete Data"><i class="fas fa-trash"></i></button>';
                    // $button = $button_show . ' ' . $button_edit . ' ' . $button_delete;
                    $button = $button_show . ' ' . $button_edit;
                    return $button;
                })
                ->editColumn('visi_id', function($data){
                    if($data->visi_id)
                    {
                        $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $data->visi_id)->latest()->first();
                        if($cek_perubahan_visi)
                        {
                            return strip_tags(substr($cek_perubahan_visi->deskripsi,0, 40)) . '...';
                        } else {
                            return strip_tags(substr($data->visi->deskripsi,0, 40)) . '...';
                        }
                    } else {
                        return 'Segera Edit Data!';
                    }
                })
                ->editColumn('deskripsi', function($data){
                    $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id',$data->id)->latest()->first();
                    if($cek_perubahan_misi)
                    {
                        return strip_tags(substr($cek_perubahan_misi->deskripsi,0, 40)) . '...';
                    } else {
                        return strip_tags(substr($data->deskripsi,0, 40)) . '...';;
                    }
                })
                ->rawColumns(['aksi', 'visi_id', 'deskripsi'])
                ->make(true);
        }
        $get_visis = Visi::all();
        $visis = [];
        foreach ($get_visis as $get_visi) {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_visi->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $visis[] = [
                    'id' => $cek_perubahan_visi->visi_id,
                    'deskripsi' => $cek_perubahan_visi->deskripsi
                ];
            } else {
                $visis[] = [
                    'id' => $get_visi->id,
                    'deskripsi' => $get_visi->deskripsi
                ];
            }
        }
        return view('admin.misi.index', [
            'visis' => $visis
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'misi_visi_id' => 'required',
            'misi_kode' => 'required',
            'misi_deskripsi' => 'required',
            'misi_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }
        $cek_misi = Misi::where('kode', $request->misi_kode)->where('visi_id', $request->misi_visi_id)->first();
        if($cek_misi)
        {
            $pivot = new PivotPerubahanMisi;
            $pivot->misi_id = $cek_misi->id;
            $pivot->visi_id = $request->misi_visi_id;
            $pivot->kode = $request->misi_kode;
            $pivot->deskripsi = $request->misi_deskripsi;
            $pivot->kabupaten_id = 62;
            $pivot->tahun_perubahan = $request->misi_tahun_perubahan;
            $pivot->save();
        } else {
            $misi = new Misi;
            $misi->visi_id = $request->misi_visi_id;
            $misi->kode = $request->misi_kode;
            $misi->deskripsi = $request->misi_deskripsi;
            $misi->kabupaten_id = 62;
            $misi->tahun_perubahan = $request->misi_tahun_perubahan;
            $misi->save();
        }

        $getVisi = Visi::find($request->misi_visi_id);
        $visi = [
            'id' => $getVisi->id,
            'kode' => $getVisi->kode,
            'deskripsi' => $getVisi->deskripsi
        ];

        $get_misis = Misi::where('visi_id', $visi['id'])->get();
        $misis = [];
        foreach($get_misis as $get_misi)
        {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misis[] = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                ];
            } else {
                $misis[] = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan,
                ];
            }
        }

        $html = '<td colspan="3" class="hiddenRow">
                    <div class="collapse show" id="misi_visi'.$visi['id'].'">
                        <table class="table table-striped">
                            <tbody>';
                            $a = 1;
                            foreach ($misis as $misi) {
                                $html .= '<tr id="trMisi'.$misi['id'].'">
                                            <td width="5%">'.$misi['kode'].'</td>
                                            <td width="75%">
                                                '.$misi['deskripsi'].'
                                                <br>';
                                                if($a == 1 || $a == 2)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Aman]</span>';
                                                }
                                                if($a == 3)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Mandiri]</span>';
                                                }
                                                if($a == 4)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Sejahtera]</span>';
                                                }
                                                if($a == 5)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Berahlak]</span>';
                                                }
                                                $html .= ' <span class="badge bg-warning text-uppercase misi-tagging">Misi '.$misi['kode'].'</span>
                                            </td>
                                            <td width="20%">
                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-icon waves-effect btn-danger waves-light delete-misi " type="button" data-misi-id="'.$misi['id'].'" title="Delete Data"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>';
                                $a++;
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
        </td>';

        return response()->json(['success' => 'Berhasil menambahkan misi','html' => $html]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $tahun)
    {
        $data = Misi::find($id);

        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $id)->latest()->first();
        $html = '<div>';

        if($cek_perubahan_misi)
        {
            $get_perubahans = PivotPerubahanMisi::where('misi_id', $id)->get();
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $cek_perubahan_misi->visi_id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($cek_perubahan_misi->visi_id);
                $deskripsi_visi = $visi->deskripsi;
            }

            $html .= '<ul>';
            $html .= '<li><p>
                            Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$data->kode.'<br>
                            Misi: '.$data->deskripsi.'<br>
                            Tahun: '.$data->tahun_perubahan.'<br>
                        </p></li>';
            $a = 1;
            foreach ($get_perubahans as $get_perubahan) {
                $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $get_perubahan->visi_id)->latest()->first();
                if($cek_perubahan_visi)
                {
                    $deskripsi_visi = $cek_perubahan_visi->deskripsi;
                } else {
                    $visi = Visi::find($get_perubahan->visi_id);
                    $deskripsi_visi = $visi->deskripsi;
                }
                $html .= '<li><p>
                            Visi: '.$deskripsi_visi.' <br>
                            Kode Misi: '.$get_perubahan->kode.'<br>
                            Misi: '.$get_perubahan->deskripsi.'<br>
                            Tahun: '.$get_perubahan->tahun_perubahan.'<br>
                        </p></li>';
            }
            $html .= '</ul>';
            $kode_misi = $cek_perubahan_misi->kode;
            $deskripsi_misi = $cek_perubahan_misi->deskripsi;
        } else {
            $cek_perubahan_visi = PivotPerubahanVisi::where('visi_id', $data->id)->latest()->first();
            if($cek_perubahan_visi)
            {
                $deskripsi_visi = $cek_perubahan_visi->deskripsi;
            } else {
                $visi = Visi::find($data->id);
                $deskripsi_visi = $visi->deskripsi;
            }
            $html .= '<p>Tidak ada</p>';

            $kode_misi = $data->kode;
            $deskripsi_misi = $data->deskripsi;
        }

        $html .='</div>';

        $array = [
            'visi' => $deskripsi_visi,
            'kode' => $kode_misi,
            'deskripsi' => $deskripsi_misi,
            'pivot_perubahan_misi' => $html
        ];

        return response()->json(['result' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, $tahun)
    {
        $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $id)->latest()->first();
        if($cek_perubahan_misi)
        {
            $array = [
                'visi_id' => $cek_perubahan_misi->visi_id,
                'kode' => $cek_perubahan_misi->kode,
                'deskripsi' => $cek_perubahan_misi->deskripsi,
                'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
            ];
        } else {
            $misi = Misi::find($id);
            $array = [
                'visi_id' => $misi->visi_id,
                'kode' => $misi->kode,
                'deskripsi' => $misi->deskripsi,
                'tahun_perubahan' => $misi->tahun_perubahan,
            ];
        }

        return response()->json(['result' => $array]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $errors = Validator::make($request->all(), [
            'misi_visi_id' => 'required',
            'misi_kode' => 'required',
            'misi_deskripsi' => 'required',
            'misi_tahun_perubahan' => 'required'
        ]);

        if($errors -> fails())
        {
            return response()->json(['errors' => $errors->errors()->all()]);
        }

        $pivot_perubahan = new PivotPerubahanMisi;
        $pivot_perubahan->misi_id = $request->misi_hidden_id;
        $pivot_perubahan->visi_id = $request->misi_visi_id;
        $pivot_perubahan->kode = $request->misi_kode;
        $pivot_perubahan->deskripsi = $request->misi_deskripsi;
        $pivot_perubahan->tahun_perubahan = $request->misi_tahun_perubahan;
        $pivot_perubahan->kabupaten_id = 62;
        $pivot_perubahan->save();

        $get_misis = Misi::where('id', $request->misi_hidden_id)->get();
        foreach($get_misis as $get_misi)
        {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
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

        $getVisi = Visi::find($request->misi_visi_id);
        $visi = [
            'id' => $getVisi->id,
            'kode' => $getVisi->kode,
            'deskripsi' => $getVisi->deskripsi
        ];

        $html = '<td width="5%">'.$misi['kode'].'</td>
                <td width="75%">
                    '.$misi['deskripsi'];
                    $html .= ' <span class="badge bg-warning text-uppercase misi-tagging">Misi '.$misi['kode'].'</span>
                </td>
                <td width="20%">
                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-icon waves-effect btn-danger waves-light delete-misi " type="button" data-misi-id="'.$misi['id'].'" title="Delete Data"><i class="fas fa-trash"></i></button>
                </td>';

        return response()->json(['success' => 'Berhasil menambahkan misi','html' => $html]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $idVisi = Misi::find($request->id)->visi_id;
        DB::transaction(function () use ($request){
            $getTujuans = Tujuan::where('misi_id', $request->id)->get();
            foreach ($getTujuans as $getTujuan) {
                $getSasarans = Sasaran::where('tujuan_id', $getTujuan->id)->get();
                foreach ($getSasarans as $getSasaran) {

                    $getSasaranIndikatorKinerjas = SasaranIndikatorKinerja::where('sasaran_id', $getSasaran->id)->get();
                    foreach ($getSasaranIndikatorKinerjas as $getSasaranIndikatorKinerja) {
                        $getPivotSasaranIndikatorProgramRpjmds = PivotSasaranIndikatorProgramRpjmd::where('sasaran_indikator_kinerja_id', $getSasaranIndikatorKinerja->id)->get();
                        foreach ($getPivotSasaranIndikatorProgramRpjmds as $getPivotSasaranIndikatorProgramRpjmd) {
                            PivotSasaranIndikatorProgramRpjmd::find($getPivotSasaranIndikatorProgramRpjmd->id)->delete();
                        }

                        $getSasaranTargetSatuanRpRealisasis = SasaranTargetSatuanRpRealisasi::where('sasaran_indikator_kinerja_id', $getSasaranIndikatorKinerja->id)->get();
                        foreach ($getSasaranTargetSatuanRpRealisasis as $getSasaranTargetSatuanRpRealisasi) {
                            $getSasaranTwRealisasis = SasaranTwRealisasi::where('sasaran_target_satuan_rp_realisasi_id', $getSasaranTargetSatuanRpRealisasi->id)->get();
                            foreach ($getSasaranTwRealisasis as $getSasaranTwRealisasi) {
                                SasaranTwRealisasi::find($getSasaranTwRealisasi->id)->delete();
                            }

                            SasaranTargetSatuanRpRealisasi::find($getSasaranTargetSatuanRpRealisasi->id)->delete();
                        }

                        SasaranIndikatorKinerja::find($getSasaranIndikatorKinerja->id)->delete();
                    }

                    $getSasaranPds = SasaranPd::where('sasaran_id', $getSasaran->id)->get();
                    foreach ($getSasaranPds as $getSasaranPd) {
                        $getSasaranPdProgramRpjmds = SasaranPdProgramRpjmd::where('sasaran_pd_id', $getSasaranPd->id)->get();
                        foreach ($getSasaranPdProgramRpjmds as $getSasaranPdProgramRpjmd) {
                            SasaranPdProgramRpjmd::find($getSasaranPdProgramRpjmd->id)->delete();
                        }

                        $getSasaranPdIndikatorKinerjas = SasaranPdIndikatorKinerja::where('sasaran_pd_id', $getSasaranPd->id)->get();
                        foreach ($getSasaranPdIndikatorKinerjas as $getSasaranPdIndikatorKinerja) {
                            $getSasaranPdTargetSatuanRpRealisasis = SasaranPdTargetSatuanRpRealisasi::where('sasaran_pd_indikator_kinerja_id', $getSasaranPdIndikatorKinerja->id)->get();
                            foreach ($getSasaranPdTargetSatuanRpRealisasis as $getSasaranPdTargetSatuanRpRealisasi) {
                                $getSasaranPdRealisasiRenjas = SasaranPdRealisasiRenja::where('sasaran_pd_target_satuan_rp_realisasi_id', $getSasaranPdTargetSatuanRpRealisasi->id)->get();
                                foreach ($getSasaranPdRealisasiRenjas as $getSasaranPdRealisasiRenja) {
                                    SasaranPdRealisasiRenja::find($getSasaranPdRealisasiRenja->id)->delete();
                                }

                                SasaranPdTargetSatuanRpRealisasi::find($getSasaranPdTargetSatuanRpRealisasi->id)->delete();
                            }

                            SasaranPdIndikatorKinerja::find($getSasaranPdIndikatorKinerja->id)->delete();
                        }

                        $getPivotPerubahanSasaranPds = PivotPerubahanSasaranPd::where('sasaran_pd_id', $getSasaranPd->id)->get();
                        foreach ($getPivotPerubahanSasaranPds as $getPivotPerubahanSasaranPd) {
                            PivotPerubahanSasaranPd::find($getPivotPerubahanSasaranPd->id)->delete();
                        }

                        SasaranPd::find($getSasaranPd->id)->delete();
                    }

                    $getPivotPerubahanSasarans = PivotPerubahanSasaran::where('sasaran_id', $getSasaran->id)->get();
                    foreach ($getPivotPerubahanSasarans as $getPivotPerubahanSasaran) {
                        PivotPerubahanSasaran::find($getPivotPerubahanSasaran->id)->delete();
                    }

                    Sasaran::find($getSasaran->id)->delete();
                }

                $getTujuanIndikatorKinerjas = TujuanIndikatorKinerja::where('tujuan_id', $getTujuan->id)->get();
                foreach ($getTujuanIndikatorKinerjas as $getTujuanIndikatorKinerja) {
                    $getTujuanTargetSatuanRpRealisasis = TujuanTargetSatuanRpRealisasi::where('tujuan_indikator_kinerja_id', $getTujuanIndikatorKinerja->id)->get();
                    foreach ($getTujuanTargetSatuanRpRealisasis as $getTujuanTargetSatuanRpRealisasi) {
                        $getTujuanTwRealisasis = TujuanTwRealisasi::where('tujuan_target_satuan_rp_realisasi_id', $getTujuanTargetSatuanRpRealisasi->id)->get();
                        foreach ($getTujuanTwRealisasis as $getTujuanTwRealisasi) {
                            TujuanTwRealisasi::find($getTujuanTwRealisasi->id)->delete();
                        }

                        TujuanTargetSatuanRpRealisasi::find($getTujuanTargetSatuanRpRealisasi->id)->delete();
                    }

                    TujuanIndikatorKinerja::find($getTujuanIndikatorKinerja->id)->delete();
                }

                $getTujuanPds = TujuanPd::where('tujuan_id', $getTujuan->id)->get();
                foreach ($getTujuanPds as $getTujuanPd) {
                    $getTujuanPdIndikatorKinerjas = TujuanPdIndikatorKinerja::where('tujuan_pd_id', $getTujuanPd->id)->get();
                    foreach ($getTujuanPdIndikatorKinerjas as $getTujuanPdIndikatorKinerja) {

                        $getTujuanPdTargetSatuanRpRealisasis = TujuanPdTargetSatuanRpRealisasi::where('tujuan_pd_indikator_kinerja_id', $getTujuanPdIndikatorKinerja->id)->get();
                        foreach ($getTujuanPdTargetSatuanRpRealisasis as $getTujuanPdTargetSatuanRpRealisasi) {
                            $getTujuanPdRealisasiRenjas = TujuanPdRealisasiRenja::where('tujuan_pd_target_satuan_rp_realisasi_id', $getTujuanPdTargetSatuanRpRealisasi->id)->get();
                            foreach ($getTujuanPdRealisasiRenjas as $getTujuanPdRealisasiRenja) {
                                TujuanPdRealisasiRenja::find($getTujuanPdRealisasiRenja->id)->delete();
                            }
                            TujuanPdTargetSatuanRpRealisasi::find($getTujuanPdTargetSatuanRpRealisasi->id)->delete();
                        }
                        TujuanPdIndikatorKinerja::find($getTujuanPdIndikatorKinerja->id)->delete();
                    }
                    $getPivotPerubahanTujuanPds = PivotPerubahanTujuanPd::where('tujuan_pd_id', $getTujuanPd->id)->get();
                    foreach ($getPivotPerubahanTujuanPds as $getPivotPerubahanTujuanPd) {
                        PivotPerubahanTujuanPd::find($getPivotPerubahanTujuanPd->id)->delete();
                    }
                    TujuanPd::find($getTujuanPd->id)->delete();
                }

                $getPivotPerubahanTujuans = PivotPerubahanTujuan::where('tujuan_id', $getTujuan->id)->get();
                foreach ($getPivotPerubahanTujuans as $getPivotPerubahanTujuan) {
                    PivotPerubahanTujuan::find($getPivotPerubahanTujuan->id)->delete();
                }
                Tujuan::find($getTujuan->id)->delete();
            }

            $getPivotPerubahanMisis = PivotPerubahanMisi::where('misi_id', $request->id)->get();
            foreach ($getPivotPerubahanMisis as $getPivotPerubahanMisi) {
                PivotPerubahanMisi::find($getPivotPerubahanMisi->id)->delete();
            }
            Misi::find($request->id)->delete();
        });

        $getVisi = Visi::find($idVisi);
        $visi = [
            'id' => $getVisi->id,
            'kode' => $getVisi->kode,
            'deskripsi' => $getVisi->deskripsi
        ];

        $get_misis = Misi::where('visi_id', $visi['id'])->get();
        $misis = [];
        foreach($get_misis as $get_misi)
        {
            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $get_misi->id)
                                    ->latest()
                                    ->first();
            if($cek_perubahan_misi)
            {
                $misis[] = [
                    'id' => $cek_perubahan_misi->misi_id,
                    'kode' => $cek_perubahan_misi->kode,
                    'deskripsi' => $cek_perubahan_misi->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_misi->tahun_perubahan,
                ];
            } else {
                $misis[] = [
                    'id' => $get_misi->id,
                    'kode' => $get_misi->kode,
                    'deskripsi' => $get_misi->deskripsi,
                    'tahun_perubahan' => $get_misi->tahun_perubahan,
                ];
            }
        }

        $html = '<td colspan="3" class="hiddenRow">
                    <div class="collapse show" id="misi_visi'.$visi['id'].'">
                        <table class="table table-striped">
                            <tbody>';
                            $a = 1;
                            foreach ($misis as $misi) {
                                $html .= '<tr id="trMisi'.$misi['id'].'">
                                            <td width="5%">'.$misi['kode'].'</td>
                                            <td width="75%">
                                                '.$misi['deskripsi'].'
                                                <br>';
                                                if($a == 1 || $a == 2)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Aman]</span>';
                                                }
                                                if($a == 3)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Mandiri]</span>';
                                                }
                                                if($a == 4)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Sejahtera]</span>';
                                                }
                                                if($a == 5)
                                                {
                                                    $html .= '<span class="badge bg-primary text-uppercase misi-tagging">Visi [Berahlak]</span>';
                                                }
                                                $html .= ' <span class="badge bg-warning text-uppercase misi-tagging">Misi '.$misi['kode'].'</span>
                                            </td>
                                            <td width="20%">
                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" type="button" title="Detail Misi"><i class="fas fa-eye"></i></button>
                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-misi" data-misi-id="'.$misi['id'].'" data-tahun="semua" data-visi-id="'.$visi['id'].'" type="button" title="Edit Misi"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-icon waves-effect btn-danger waves-light delete-misi " type="button" data-misi-id="'.$misi['id'].'" title="Delete Data"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>';
                                $a++;
                            }
                            $html .= '</tbody>
                        </table>
                    </div>
        </td>';

        return response()->json(['success' => 'Berhasil menghapus', 'html' => $html, 'visi_id' => $idVisi]);
    }
}
