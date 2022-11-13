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
use App\Imports\KegiatanImport;
use App\Models\PivotPerubahanProgram;
use App\Models\PivotPerubahanUrusan;
use App\Models\KegiatanIndikatorKinerja;

class KegiatanImport implements ToCollection,WithStartRow
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
                    // if($row[1] == null)
                    // {
                    //     $response['import_status'] = false;
                    //     $response['import_message'] = 'Kode Kegiatan Harus Diisi';
                    //     session(['import_status' => $response['import_status']]);
                    //     session(['import_message' => $response['import_message']]);
                    //     return false;
                    // }
                    // if($row[2] == null)
                    // {
                    //     $response['import_status'] = false;
                    //     $response['import_message'] = 'Kegiatan Harus Diisi';
                    //     session(['import_status' => $response['import_status']]);
                    //     session(['import_message' => $response['import_message']]);
                    //     return false;
                    // }
                    // if($row[3] == null)
                    // {
                    //     $response['import_status'] = false;
                    //     $response['import_message'] = 'Tahun Perubahan Harus Diisi';
                    //     session(['import_status' => $response['import_status']]);
                    //     session(['import_message' => $response['import_message']]);
                    //     return false;
                    // }
                    // Semua
                    // $cek_kegiatan = Kegiatan::where('kode', $row[3])
                    //                 ->whereHas("program", function($q) use ($row){
                    //                     $q->where('kode', $row[2]);
                    //                     $q->whereHas('urusan', function($q) use ($row){
                    //                         $q->where('kode', $row[1]);
                    //                     });
                    //                 })->first();
                    // if($cek_kegiatan)
                    // {
                    //     $cek_pivot_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $cek_kegiatan->id)
                    //                             ->where('program_id', $cek_kegiatan->program_id)
                    //                             ->where('kode', $row[3])
                    //                             ->where('tahun_perubahan', $row[5])
                    //                             ->first();
                    //     if($cek_pivot_kegiatan)
                    //     {
                    //         PivotPerubahanKegiatan::find($cek_pivot_kegiatan->id)->delete();

                    //         $pivot = new PivotPerubahanKegiatan;
                    //         $pivot->kegiatan_id = $cek_kegiatan->id;
                    //         $pivot->program_id = $cek_kegiatan->program_id;
                    //         $pivot->kode = $row[3];
                    //         $pivot->deskripsi = $row[4];
                    //         $pivot->tahun_perubahan = $row[5];
                    //         if($row[5] > 2020)
                    //         {
                    //             $pivot->status_aturan = 'Sesudah Perubahan';
                    //         } else {
                    //             $pivot->status_aturan = 'Sebelum Perubahan';
                    //         }
                    //         $pivot->kabupaten_id = 62;
                    //         $pivot->save();
                    //     } else {
                    //         $pivot = new PivotPerubahanKegiatan;
                    //         $pivot->kegiatan_id = $cek_kegiatan->id;
                    //         $pivot->program_id = $cek_kegiatan->program_id;
                    //         $pivot->kode = $row[3];
                    //         $pivot->deskripsi = $row[4];
                    //         $pivot->tahun_perubahan = $row[5];
                    //         if($row[5] > 2020)
                    //         {
                    //             $pivot->status_aturan = 'Sesudah Perubahan';
                    //         } else {
                    //             $pivot->status_aturan = 'Sebelum Perubahan';
                    //         }
                    //         $pivot->kabupaten_id = 62;
                    //         $pivot->save();
                    //     }
                    // } else {
                    //     $get_program = Program::where('kode', $row[2])
                    //                     ->whereHas('urusan', function($q) use ($row){
                    //                         $q->where('kode', $row[1]);
                    //                     })->first();
                    //     if($get_program)
                    //     {
                    //         $kegiatan = new Kegiatan;
                    //         $kegiatan->program_id = $get_program->id;
                    //         $kegiatan->kode = $row[3];
                    //         $kegiatan->deskripsi = $row[4];
                    //         $kegiatan->tahun_perubahan = $row[5];
                    //         if($row[5] > 2020)
                    //         {
                    //             $kegiatan->status_aturan = 'Sesudah Perubahan';
                    //         } else {
                    //             $kegiatan->status_aturan = 'Sebelum Perubahan';
                    //         }
                    //         $kegiatan->kabupaten_id = 62;
                    //         $kegiatan->save();
                    //     }
                    // }
                    // Satu Per Satu
                    // $cek_kegiatan = Kegiatan::where('kode', ''.$row[1])->where('program_id', $this->program_id)->first();
                    // if($cek_kegiatan)
                    // {
                    //     $pivot = new PivotPerubahanKegiatan;
                    //     $pivot->kegiatan_id = $cek_kegiatan->id;
                    //     $pivot->program_id = $this->program_id;
                    //     $pivot->kode = $row[1];
                    //     $pivot->deskripsi = $row[2];
                    //     $pivot->tahun_perubahan = $row[3];
                    //     if($row[3] > 2020)
                    //     {
                    //         $pivot->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $pivot->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $pivot->kabupaten_id = 62;
                    //     $pivot->save();
                    // } else {
                    //     $kegiatan = new Kegiatan;
                    //     $kegiatan->program_id = $this->program_id;
                    //     $kegiatan->kode = $row[1];
                    //     $kegiatan->deskripsi = $row[2];
                    //     $kegiatan->tahun_perubahan = $row[3];
                    //     if($row[3] > 2020)
                    //     {
                    //         $kegiatan->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $kegiatan->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $kegiatan->kabupaten_id = 62;
                    //     $kegiatan->save();
                    // }

                    // Kegiatan Indikator Kinerja
                    $cek_kegiatan_indikator_kinerja = KegiatanIndikatorKinerja::whereHas('kegiatan', function($q) use ($row){
                        $q->where('kode', $row[3]);
                        $q->whereHas('program', function($q) use ($row){
                            $q->where('kode', $row[2]);
                            $q->whereHas('urusan', function($q) use ($row){
                                $q->where('kode', $row[1]);
                            });
                        });
                    })->first();

                    if($cek_kegiatan_indikator_kinerja)
                    {
                        KegiatanIndikatorKinerja::find($cek_kegiatan_indikator_kinerja->id)->delete();

                        $get_kegiatan = Kegiatan::where('kode', $row[3])->whereHas('program', function($q) use ($row){
                                            $q->where('kode', $row[2]);
                                            $q->whereHas('urusan', function($q) use ($row){
                                                $q->where('kode', $row[1]);
                                            });
                                        })->first();

                        if($get_kegiatan)
                        {
                            $kegiatan_indikator_kinerja = new KegiatanIndikatorKinerja;
                            $kegiatan_indikator_kinerja->kegiatan_id = $get_kegiatan->id;
                            $kegiatan_indikator_kinerja->deskripsi = $row[4];
                            $kegiatan_indikator_kinerja->save();
                        }
                    } else {
                        $get_kegiatan = Kegiatan::where('kode', $row[3])->whereHas('program', function($q) use ($row){
                            $q->where('kode', $row[2]);
                            $q->whereHas('urusan', function($q) use ($row){
                                $q->where('kode', $row[1]);
                            });
                        })->first();

                        if($get_kegiatan)
                        {
                            $kegiatan_indikator_kinerja = new KegiatanIndikatorKinerja;
                            $kegiatan_indikator_kinerja->kegiatan_id = $get_kegiatan->id;
                            $kegiatan_indikator_kinerja->deskripsi = $row[4];
                            $kegiatan_indikator_kinerja->save();
                        }
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
