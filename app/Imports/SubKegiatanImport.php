<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use DB;
use Carbon\Carbon;
use App\Models\Urusan;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\PivotPerubahanKegiatan;
use App\Models\PivotKegiatanIndikator;
use App\Imports\SubKegiatanImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;
use App\Models\SubKegiatan;
use App\Models\PivotPerubahanSubKegiatan;
use App\Models\PivotSubKegiatanIndikator;

class SubKegiatanImport implements ToCollection,WithStartRow
{
    /**
    * @param Collection $collection
    */
    protected $kegiatan_id;

    public function __construct($kegiatan_id)
    {
        $this->kegiatan_id = $kegiatan_id;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $response = [
            'import_status' => true,
            'import_message' => true
        ];
        DB::beginTransaction();
        try {
            $start = microtime(true);
            $response['import_status']  = true;
            session(['import_status' => $response['import_status']]);
            $n = 0;
            foreach ($rows as $key => $row) {
                if($row->filter()->isNotEmpty())
                {
                    if($row[1] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Kode Sub Kegiatan Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[2] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Sub Kegiatan Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[3] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Tahun Perubahan Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    // $cek_sub_kegiatan = SubKegiatan::whereHas('kegiatan', function($q) use ($row){
                    //     $q->where('kode', $row[3]);
                    //     $q->whereHas('program', function($q) use ($row){
                    //         $q->where('kode', $row[2]);
                    //         $q->whereHas('urusan', function($q) use ($row){
                    //             $q->where('kode', $row[1]);
                    //         });
                    //     });
                    // })->first();

                    // if($cek_sub_kegiatan)
                    // {
                    //     $get_kegiatan = Kegiatan::where('kode', $row[3])->whereHas('program', function($q) use ($row){
                    //         $q->where('kode', $row[2]);
                    //         $q->whereHas('urusan', function($q) use ($row){
                    //             $q->where('kode', $row[1]);
                    //         });
                    //     })->first();
                    //     $pivot = new PivotPerubahanSubKegiatan;
                    //     $pivot->sub_kegiatan_id = $cek_sub_kegiatan->id;
                    //     $pivot->kegiatan_id = $get_kegiatan->id;
                    //     $pivot->kode = $row[4];
                    //     $pivot->deskripsi = $row[5];
                    //     $pivot->tahun_perubahan = $row[6];
                    //     if($row[6] > 2020)
                    //     {
                    //         $pivot->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $pivot->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->save();
                    // } else {
                    //     $get_kegiatan = Kegiatan::where('kode', $row[3])->whereHas('program', function($q) use ($row){
                    //         $q->where('kode', $row[2]);
                    //         $q->whereHas('urusan', function($q) use ($row){
                    //             $q->where('kode', $row[1]);
                    //         });
                    //     })->first();
                    //     $pivot = new SubKegiatan;
                    //     $pivot->kegiatan_id = $get_kegiatan->id;
                    //     $pivot->kode = $row[4];
                    //     $pivot->deskripsi = $row[5];
                    //     $pivot->tahun_perubahan = $row[6];
                    //     if($row[6] > 2020)
                    //     {
                    //         $pivot->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $pivot->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->save();
                    // }

                    $cek_sub_kegiatan = SubKegiatan::where('kode', $row[1])->where('kegiatan_id', $this->kegiatan_id)->first();
                    if($cek_sub_kegiatan)
                    {
                        $pivot = new PivotPerubahanSubKegiatan;
                        $pivot->sub_kegiatan_id = $cek_sub_kegiatan->id;
                        $pivot->kegiatan_id = $this->kegiatan_id;
                        $pivot->kode = $row[1];
                        $pivot->deskripsi = $row[2];
                        $pivot->tahun_perubahan = $row[3];
                        if($row[3] > 2020)
                        {
                            $pivot->status_aturan = 'Sesudah Perubahan';
                        } else {
                            $pivot->status_aturan = 'Sebelum Perubahan';
                        }
                        $pivot->kabupaten_id = 62;
                        $pivot->save();
                    } else {
                        $sub_kegiatan = new SubKegiatan;
                        $sub_kegiatan->kegiatan_id = $this->kegiatan_id;
                        $sub_kegiatan->kode = $row[1];
                        $sub_kegiatan->deskripsi = $row[2];
                        $sub_kegiatan->tahun_perubahan = $row[3];
                        if($row[3] > 2020)
                        {
                            $sub_kegiatan->status_aturan = 'Sesudah Perubahan';
                        } else {
                            $sub_kegiatan->status_aturan = 'Sebelum Perubahan';
                        }
                        $sub_kegiatan->kabupaten_id = 62;
                        $sub_kegiatan->save();
                    }
                }
                $n++;
            }
            $time_elapsed_secs = microtime(true) - $start;
            $response['import_message'] = $n. ' data telah diimport dalam '. $time_elapsed_secs.' Second';
            session(['import_message' => $response['import_message']]);
            DB::commit();
        } catch (\Illuminate\Database\QueryException $exception) {
            DB::rollback();
            $errorInfo = $exception->errorInfo;
            $response['import_status'] = false;
            $response['import_message'] = $errorInfo[2];;
            session(['import_status' => $response['import_status']]);
            session(['import_message' => $response['import_message']]);
        }
        session(['import_status' => $response['import_status']]);
        session(['import_message' => $response['import_message']]);
        return true;
    }
}
