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
use App\Models\Visi;
use App\Models\PivotPerubahanVisi;
use App\Models\Misi;
use App\Models\PivotPerubahanMisi;
use App\Models\Tujuan;
use App\Models\PivotPerubahanTujuan;
use App\Models\Sasaran;
use App\Models\PivotPerubahanSasaran;
use App\Models\PivotSasaranIndikator;

class SasaranImport implements ToCollection,WithStartRow
{
    /**
    * @param Collection $collection
    */
    protected $tujuan_id;

    public function __construct($tujuan_id)
    {
        $this->tujuan_id = $tujuan_id;
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
                        $response['import_message'] = 'Kode Sasaran Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[2] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Sasaran Harus Diisi';
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
                    // Import Semua Sasaran
                    // $cek_sasaran = Sasaran::where('kode', $row[3])->whereHas('tujuan', function($q) use ($row){
                    //     $q->where('kode', $row[2]);
                    //     $q->whereHas('misi', function($q) use ($row){
                    //         $q->where('kode', $row[1]);
                    //     });
                    // })->first();
                    // if($cek_sasaran)
                    // {
                    //     $pivot = new PivotPerubahanSasaran;
                    //     $pivot->sasaran_id = $cek_sasaran->id;
                    //     $pivot->tujuan_id = $cek_sasaran->tujuan_id;
                    //     $pivot->kode = $row[3];
                    //     $pivot->deskripsi = $row[4];
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->tahun_perubahan = $row[5];
                    //     $pivot->save();
                    // } else {
                    //     $get_tujuan = Tujuan::where('kode', $row[2])->whereHas('misi', function($q) use ($row){
                    //         $q->where('kode', $row[1]);
                    //     })->first();
                    //     $sasaran = new Sasaran;
                    //     $sasaran->tujuan_id = $get_tujuan->id;
                    //     $sasaran->kode = $row[3];
                    //     $sasaran->deskripsi = $row[4];
                    //     $sasaran->kabupaten_id = 62;
                    //     $sasaran->tahun_perubahan = $row[5];
                    //     $sasaran->save();
                    // }
                    // Import Sasaran Spesifik
                    // $cek_sasaran = Sasaran::where('kode', $row[1])->where('tujuan_id', $this->tujuan_id)->first();
                    // if($cek_sasaran)
                    // {
                    //     $pivot = new PivotPerubahanSasaran;
                    //     $pivot->sasaran_id = $cek_sasaran->id;
                    //     $pivot->tujuan_id = $this->tujuan_id;
                    //     $pivot->kode = $row[1];
                    //     $pivot->deskripsi = $row[2];
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->tahun_perubahan = $row[3];
                    //     $pivot->save();
                    // } else {
                    //     $sasaran = new Sasaran;
                    //     $sasaran->tujuan_id = $this->tujuan_id;
                    //     $sasaran->kode = $row[1];
                    //     $sasaran->deskripsi = $row[2];
                    //     $sasaran->kabupaten_id = 62;
                    //     $sasaran->tahun_perubahan = $row[3];
                    //     $sasaran->save();
                    // }

                    // Import Semua Sasaran Indikator
                    $cek_sasaran = Sasaran::where('kode', $row[3])->whereHas('tujuan', function($q) use ($row){
                        $q->where('kode', $row[2]);
                        $q->whereHas('misi', function($q) use ($row){
                            $q->where('kode', $row[1]);
                        });
                    })->first();
                    if($cek_sasaran)
                    {
                        $indikator = new PivotSasaranIndikator;
                        $indikator->sasaran_id = $cek_sasaran->id;
                        $indikator->indikator = $row[4];
                        $indikator->target = $row[5];
                        $indikator->satuan = $row[6];
                        $indikator->save();
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
