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
use App\Models\Program;
use App\Models\PivotPerubahanProgram;
use Carbon\Carbon;
use App\Models\ProgramIndikatorKinerja;

class ProgramImport implements ToCollection,WithStartRow
{
    protected $urusan_id;

    public function __construct($urusan_id)
    {
        $this->urusan_id = $urusan_id;
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
                        $response['import_message'] = 'Kode Program Harus Diisi';
                        session(['import_status' => $response['import_status']]);
                        session(['import_message' => $response['import_message']]);
                        return false;
                    }
                    if($row[2] == null)
                    {
                        $response['import_status'] = false;
                        $response['import_message'] = 'Program Harus Diisi';
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
                    // Semua
                    $cek_program = Program::where('kode', $row[2])->whereHas('urusan', function($q) use ($row){
                        $q->where('kode', $row[1]);
                    })->first();
                    if($cek_program)
                    {
                        $cek_pivot_program = PivotPerubahanProgram::where('program_id', $cek_program->id)
                                                ->where('urusan_id', $cek_program->urusan_id)
                                                ->where('kode', $row[2])
                                                ->where('tahun_perubahan', $row[4])
                                                ->first();
                        if($cek_pivot_program)
                        {
                            PivotPerubahanProgram::find($cek_pivot_program->id)->delete();

                            $pivot = new PivotPerubahanProgram;
                            $pivot->program_id = $cek_program->id;
                            $pivot->urusan_id = $cek_program->urusan_id;
                            $pivot->kode = $row[2];
                            $pivot->deskripsi = $row[3];
                            $pivot->tahun_perubahan = $row[4];
                            $pivot->kabupaten_id = 62;
                            if($row[4] > 2020)
                            {
                                $pivot->status_aturan = 'Sesudah Perubahan';
                            } else {
                                $pivot->status_aturan = 'Sebelum Perubahan';
                            }
                            $pivot->save();
                        } else {
                            $pivot = new PivotPerubahanProgram;
                            $pivot->program_id = $cek_program->id;
                            $pivot->urusan_id = $cek_program->urusan_id;
                            $pivot->kode = $row[2];
                            $pivot->deskripsi = $row[3];
                            $pivot->tahun_perubahan = $row[4];
                            $pivot->kabupaten_id = 62;
                            if($row[4] > 2020)
                            {
                                $pivot->status_aturan = 'Sesudah Perubahan';
                            } else {
                                $pivot->status_aturan = 'Sebelum Perubahan';
                            }
                            $pivot->save();
                        }
                    } else {
                        $get_urusan = Urusan::where('kode', $row[1])->first();
                        if($get_urusan)
                        {
                            $program = new Program;
                            $program->urusan_id = $get_urusan->id;
                            $program->kode = $row[2];
                            $program->deskripsi = $row[3];
                            $program->tahun_perubahan = $row[4];
                            $program->kabupaten_id = 62;
                            if($row[4] > 2020)
                            {
                                $program->status_aturan = 'Sesudah Perubahan';
                            } else {
                                $program->status_aturan = 'Sebelum Perubahan';
                            }
                            $program->save();
                        }
                    }

                    // Program Indikator
                    // $cek_program_indikator_kinerja = ProgramIndikatorKinerja::where('deskripsi', 'like', '%'.$row[3].'%')
                    //                                     ->whereHas('program', function($q) use ($row){
                    //                                         $q->where('kode', $row[2]);
                    //                                         $q->whereHas('urusan', function($q) use ($row){
                    //                                             $q->where('kode', $row[1]);
                    //                                         });
                    //                                     })->first();

                    // if($cek_program_indikator_kinerja)
                    // {
                    //     ProgramIndikatorKinerja::find($cek_program_indikator_kinerja->id)->delete();

                    //     $get_program = Program::where('kode', $row[2])->whereHas('urusan', function($q) use ($row){
                    //         $q->where('kode', $row[1]);
                    //     })->first();
                    //     if($get_program)
                    //     {
                    //         $program_indikator_kinerja = new ProgramIndikatorKinerja;
                    //         $program_indikator_kinerja->program_id = $get_program->id;
                    //         $program_indikator_kinerja->deskripsi = $row[3];
                    //         $program_indikator_kinerja->save();
                    //     }
                    // } else {
                    //     $get_program = Program::where('kode', $row[2])->whereHas('urusan', function($q) use ($row){
                    //         $q->where('kode', $row[1]);
                    //     })->first();
                    //     if($get_program)
                    //     {
                    //         $program_indikator_kinerja = new ProgramIndikatorKinerja;
                    //         $program_indikator_kinerja->program_id = $get_program->id;
                    //         $program_indikator_kinerja->deskripsi = $row[3];
                    //         $program_indikator_kinerja->save();
                    //     }
                    // }

                    // Satu Persatu
                    // $cek_program = Program::where('kode', $row[1])->where('urusan_id', $this->urusan_id)->first();
                    // if($cek_program)
                    // {
                    //     $pivot = new PivotPerubahanProgram;
                    //     $pivot->program_id = $cek_program->id;
                    //     $pivot->urusan_id = $this->urusan_id;
                    //     $pivot->kode = $row[1];
                    //     $pivot->deskripsi = $row[2];
                    //     $pivot->tahun_perubahan = $row[3];
                    //     $pivot->kabupaten_id = 62;
                    //     if($row[3] > 2020)
                    //     {
                    //         $pivot->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $pivot->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $pivot->save();
                    // } else {
                    //     $program = new Program;
                    //     $program->urusan_id = $this->urusan_id;
                    //     $program->kode = $row[1];
                    //     $program->deskripsi = $row[2];
                    //     $program->tahun_perubahan = $row[3];
                    //     $program->kabupaten_id = 62;
                    //     if($row[3] > 2020)
                    //     {
                    //         $program->status_aturan = 'Sesudah Perubahan';
                    //     } else {
                    //         $program->status_aturan = 'Sebelum Perubahan';
                    //     }
                    //     $program->save();
                    // }
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
