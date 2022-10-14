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
use App\Models\PivotTujuanIndikator;
use App\Imports\TujuanImport;

class TujuanImport implements ToCollection,WithStartRow
{
    /**
    * @param Collection $collection
    */
    protected $misi_id;

    public function __construct($misi_id)
    {
        $this->misi_id = $misi_id;
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
                        $response['import_message'] = 'Kode Tujuan Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[2] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Tujuan Harus Diisi';
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
                    // $cek_tujuan = Tujuan::where('kode', $row['2'])->whereHas('misi', function($q) use ($row) {
                    //     $q->where('kode', $row[1]);
                    // })->first();
                    // if($cek_tujuan)
                    // {
                    //     $pivot = new PivotPerubahanTujuan;
                    //     $pivot->tujuan_id = $cek_tujuan->id;
                    //     $pivot->misi_id = $cek_tujuan->misi_id;
                    //     $pivot->kode = $row[2];
                    //     $pivot->deskripsi = $row[3];
                    //     $pivot->tahun_perubahan = $row[4];
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->save();
                    // } else {
                    //     $get_misi = Misi::where('kode', $row[1])->first();

                    //     $tujuan = new Tujuan;
                    //     $tujuan->misi_id = $get_misi->id;
                    //     $tujuan->kode = $row[2];
                    //     $tujuan->deskripsi = $row[3];
                    //     $tujuan->tahun_perubahan = $row[4];
                    //     $tujuan->kabupaten_id = 62;
                    //     $tujuan->save();
                    // }
                    $cek_tujuan = Tujuan::where('kode', $row[1])->where('misi_id', $this->misi_id)->first();
                    if($cek_tujuan)
                    {
                        $pivot = new PivotPerubahanTujuan;
                        $pivot->tujuan_id = $cek_tujuan->id;
                        $pivot->misi_id = $this->misi_id;
                        $pivot->kode = $row[1];
                        $pivot->deskripsi = $row[2];
                        $pivot->tahun_perubahan = $row[3];
                        $pivot->kabupaten_id = 62;
                        $pivot->save();
                    } else {
                        $tujuan = new Tujuan;
                        $tujuan->misi_id = $this->misi_id;
                        $tujuan->kode = $row[1];
                        $tujuan->deskripsi = $row[2];
                        $tujuan->tahun_perubahan = $row[3];
                        $tujuan->kabupaten_id = 62;
                        $tujuan->save();
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
