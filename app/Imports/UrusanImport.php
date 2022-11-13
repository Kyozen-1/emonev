<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use DB;
use App\Models\Urusan;
use App\Models\PivotPerubahanUrusan;

class UrusanImport implements ToCollection,WithStartRow
{
    /**
    * @param Collection $collection
    */
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
                        $response['import_message'] = 'Kode Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[2] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Urusan Harus Diisi';
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
                    $cek_urusan =  Urusan::where('kode', $row[1])->first();
                    if($cek_urusan)
                    {
                        $cek_pivot_perubahan_urusan = PivotPerubahanUrusan::where('kode', $row[1])
                                                        ->where('tahun_perubahan', $row[3])
                                                        ->where('urusan_id', $cek_urusan->id)
                                                        ->first();
                        if($cek_pivot_perubahan_urusan)
                        {
                            PivotPerubahanUrusan::find($cek_pivot_perubahan_urusan->id)->delete();

                            $pivot = new PivotPerubahanUrusan;
                            $pivot->urusan_id = $cek_urusan->id;
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
                            $pivot = new PivotPerubahanUrusan;
                            $pivot->urusan_id = $cek_urusan->id;
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
                        }
                    } else {
                        $urusan = new Urusan;
                        $urusan->kode = $row[1];
                        $urusan->deskripsi = $row[2];
                        $urusan->tahun_perubahan = $row[3];
                        if($row[3] > 2020)
                        {
                            $urusan->status_aturan = 'Sesudah Perubahan';
                        } else {
                            $urusan->status_aturan = 'Sebelum Perubahan';
                        }
                        $urusan->kabupaten_id = 62;
                        $urusan->save();
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
