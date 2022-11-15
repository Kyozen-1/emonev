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
use App\Models\SasaranIndikatorKinerja;
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

class SasaranImport implements ToCollection,WithStartRow
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
                    $cek_sasaran = Sasaran::where('kode', $row[3])->whereHas('tujuan', function($q) use ($row){
                        $q->where('kode', $row[2]);
                        $q->whereHas('misi', function($q) use ($row){
                            $q->where('kode', $row[1]);
                        });
                    })->first();
                    if($cek_sasaran)
                    {
                        $cek_pivot = PivotPerubahanSasaran::where('sasaran_id', $cek_sasaran->id)
                                        ->where('tujuan_id', $cek_sasaran->tujuan_id)
                                        ->where('kode', $row[3])
                                        ->where('tahun_perubahan', $row[5])
                                        ->first();
                        if($cek_pivot)
                        {
                            $pivot = new PivotPerubahanSasaran;
                            $pivot->sasaran_id = $cek_sasaran->id;
                            $pivot->tujuan_id = $cek_sasaran->tujuan_id;
                            $pivot->kode = $row[3];
                            $pivot->deskripsi = $row[4];
                            $pivot->kabupaten_id = 62;
                            $pivot->tahun_perubahan = $row[5];
                            $pivot->save();

                            PivotPerubahanSasaran::find($cek_pivot->id)->delete();
                        } else {
                            $pivot = new PivotPerubahanSasaran;
                            $pivot->sasaran_id = $cek_sasaran->id;
                            $pivot->tujuan_id = $cek_sasaran->tujuan_id;
                            $pivot->kode = $row[3];
                            $pivot->deskripsi = $row[4];
                            $pivot->kabupaten_id = 62;
                            $pivot->tahun_perubahan = $row[5];
                            $pivot->save();
                        }
                    } else {
                        $get_tujuan = Tujuan::where('kode', $row[2])->whereHas('misi', function($q) use ($row){
                            $q->where('kode', $row[1]);
                        })->first();
                        $sasaran = new Sasaran;
                        $sasaran->tujuan_id = $get_tujuan->id;
                        $sasaran->kode = $row[3];
                        $sasaran->deskripsi = $row[4];
                        $sasaran->kabupaten_id = 62;
                        $sasaran->tahun_perubahan = $row[5];
                        $sasaran->save();
                    }
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
                    // $cek_sasaran = Sasaran::where('kode', $row[3])->whereHas('tujuan', function($q) use ($row){
                    //     $q->where('kode', $row[2]);
                    //     $q->whereHas('misi', function($q) use ($row){
                    //         $q->where('kode', $row[1]);
                    //     });
                    // })->first();
                    // if($cek_sasaran)
                    // {
                    //     $cek_indikator = SasaranIndikatorKinerja::where('sasaran_id', $cek_sasaran->id)
                    //                         ->where('deskripsi', 'like', '%'.$row[4].'%')->first();
                    //     if($cek_indikator)
                    //     {
                    //         $indikator = new SasaranIndikatorKinerja;
                    //         $indikator->sasaran_id = $cek_sasaran->id;
                    //         $indikator->deskripsi = $row[4];
                    //         $indikator->save();

                    //         SasaranIndikatorKinerja::find($cek_indikator->id)->delete();
                    //     } else {
                    //         $indikator = new SasaranIndikatorKinerja;
                    //         $indikator->sasaran_id = $cek_sasaran->id;
                    //         $indikator->deskripsi = $row[4];
                    //         $indikator->save();
                    //     }
                    // }

                    // Import Program Rpjmd

                    // $get_program = Program::where('kode', $row[2])->whereHas('urusan', function($q) use ($row){
                    //     $q->where('kode', $row[1]);
                    // })->first();
                    // if($get_program)
                    // {
                    //     $get_sasaran_indikator = PivotSasaranIndikator::where('indikator', 'like', '%'.$row[7].'%')
                    //                                 ->whereHas('sasaran', function($q) use ($row){
                    //                                     $q->where('kode', $row[6]);
                    //                                     $q->whereHas('tujuan', function($q) use ($row){
                    //                                         $q->where('kode', $row[5]);
                    //                                         $q->whereHas('misi', function($q) use ($row){
                    //                                             $q->where('kode', $row[4]);
                    //                                         });
                    //                                     });
                    //                                 })->first();
                    //     if($get_sasaran_indikator)
                    //     {
                    //         $get_opd = MasterOpd::where('nama', 'like', '%'.$row[3].'%')->first();
                    //         if($get_opd)
                    //         {
                    //             $cek_program_rpjmd = ProgramRpjmd::where('program_id', $get_program->id)
                    //                                     ->whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($get_sasaran_indikator){
                    //                                         $q->where('sasaran_indikator_id', $get_sasaran_indikator->id);
                    //                                     })->first();
                    //             if($cek_program_rpjmd)
                    //             {
                    //                 $cek_pivot_opd_program_rpjmd = PivotOpdProgramRpjmd::where('program_rpjmd_id', $cek_program_rpjmd->id)
                    //                                                     ->where('opd_id', $get_opd->id)->first();
                    //                 if(!$cek_pivot_opd_program_rpjmd)
                    //                 {
                    //                     $pivot_opd_program_rpjmd = new PivotOpdProgramRpjmd;
                    //                     $pivot_opd_program_rpjmd->program_rpjmd_id = $cek_program_rpjmd->id;
                    //                     $pivot_opd_program_rpjmd->opd_id = $get_opd->id;
                    //                     $pivot_opd_program_rpjmd->save();
                    //                 }

                    //                 $cek_pivot_sasaran_indikator_program_rpjmd = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $cek_program_rpjmd->id)
                    //                                                                 ->where('sasaran_indikator_id', $get_sasaran_indikator->id)->first();
                    //                 if(!$cek_pivot_sasaran_indikator_program_rpjmd)
                    //                 {
                    //                     $pivot_sasaran_indikator_program_rpjmd = new PivotSasaranIndikatorProgramRpjmd;
                    //                     $pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id = $cek_program_rpjmd->id;
                    //                     $pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_id = $get_sasaran_indikator->id;
                    //                     $pivot_sasaran_indikator_program_rpjmd->save();
                    //                 }
                    //             } else {
                    //                 $program_rpjmd = new ProgramRpjmd;
                    //                 $program_rpjmd->program_id = $get_program->id;
                    //                 $program_rpjmd->status_program = 'Program Prioritas';
                    //                 $program_rpjmd->save();

                    //                 $cek_pivot_opd_program_rpjmd = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)
                    //                                                     ->where('opd_id', $get_opd->id)->first();
                    //                 if(!$cek_pivot_opd_program_rpjmd)
                    //                 {
                    //                     $pivot_opd_program_rpjmd = new PivotOpdProgramRpjmd;
                    //                     $pivot_opd_program_rpjmd->program_rpjmd_id = $program_rpjmd->id;
                    //                     $pivot_opd_program_rpjmd->opd_id = $get_opd->id;
                    //                     $pivot_opd_program_rpjmd->save();
                    //                 }

                    //                 $cek_pivot_sasaran_indikator_program_rpjmd = PivotSasaranIndikatorProgramRpjmd::where('program_rpjmd_id', $program_rpjmd->id)
                    //                                                                 ->where('sasaran_indikator_id', $get_sasaran_indikator->id)->first();
                    //                 if(!$cek_pivot_sasaran_indikator_program_rpjmd)
                    //                 {
                    //                     $pivot_sasaran_indikator_program_rpjmd = new PivotSasaranIndikatorProgramRpjmd;
                    //                     $pivot_sasaran_indikator_program_rpjmd->program_rpjmd_id = $program_rpjmd->id;
                    //                     $pivot_sasaran_indikator_program_rpjmd->sasaran_indikator_id = $get_sasaran_indikator->id;
                    //                     $pivot_sasaran_indikator_program_rpjmd->save();
                    //                 }
                    //             }

                    //         }
                    //     }
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
