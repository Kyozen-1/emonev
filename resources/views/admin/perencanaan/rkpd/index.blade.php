@php
    use App\Models\Urusan;
    use App\Models\Program;
    use App\Models\ProgramIndikatorKinerja;
    use App\Models\OpdProgramIndikatorKinerja;
@endphp
<div class="data-table-rows slim mb-5">
    <!-- Table Start -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered" id="rkpdTable">
            <thead class="text-center">
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">OPD</th>
                    <th rowspan="2">Urusan</th>
                    <th colspan="5">P-RPJMD</th>
                    <th colspan="5">Renstra</th>
                    <th colspan="5">RKPD Awal / Renja Awal</th>
                    <th colspan="5">RKPD Perubahan / Renja Perubahan</th>
                </tr>
                <tr>
                    <th>Program</th>
                    <th>Indikator Kinerja</th>
                    <th>Satuan</th>
                    <th>Target Kinerja</th>
                    <th>Target Anggaran</th>
                    <th>Program / Kegiatan</th>
                    <th>Indikator Kinerja</th>
                    <th>Satuan</th>
                    <th>Target Kinerja</th>
                    <th>Target Anggaran</th>
                    <th>Program / Kegiatan / Sub Kegiatan</th>
                    <th>Indikator Kinerja</th>
                    <th>Satuan</th>
                    <th>Target Kinerja</th>
                    <th>Target Anggaran</th>
                    <th>Program / Kegiatan / Sub Kegiatan</th>
                    <th>Indikator Kinerja</th>
                    <th>Satuan</th>
                    <th>Target Kinerja</th>
                    <th>Target Anggaran</th>
                </tr>
            </thead>
            <tbody id="tbodyRkpd" style="text-align: left"></tbody>
            {{-- <tbody class="text-left">
                @php
                    $i = 1;
                @endphp
                @foreach ($masterOpds as $masterOpd)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$masterOpd->nama}}</td>
                        @php
                            $urusans = Urusan::whereHas('program', function($q) use ($masterOpd){
                                            $q->whereHas('program_indikator_kinerja', function($q) use ($masterOpd){
                                                $q->whereHas('opd_program_indikator_kinerja', function($q) use ($masterOpd){
                                                    $q->where('opd_id', $masterOpd->id);
                                                });
                                            });
                                        })->get();
                            $a = 1;
                        @endphp
                        @foreach ($urusans as $urusan)
                            @if ($a == 1)
                                    <td>{{$urusan->deskripsi}}</td>
                                    @php
                                        $programs = Program::where('urusan_id', $urusan->id)
                                                        ->whereHas('program_rpjmd')
                                                        ->whereHas('program_indikator_kinerja', function($q) use ($masterOpd){
                                                            $q->whereHas('opd_program_indikator_kinerja', function($q) use ($masterOpd){
                                                                $q->where('opd_id', $masterOpd->id);
                                                            });
                                                        })->get();
                                        $b = 1;
                                    @endphp
                                    @foreach ($programs as $program)
                                        @if ($b == 1)
                                                <td>{{$program->deskripsi}}</td>
                                            </tr>
                                        @else
                                                <td>{{$program->deskripsi}}</td>
                                            </tr>
                                        @endif
                                        @php
                                            $b++;
                                        @endphp
                                    @endforeach
                            @else
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{$urusan->deskripsi}}</td>
                                </tr>
                            @endif
                            @php
                                $a++;
                            @endphp
                        @endforeach
                    </tr>
                @endforeach
            </tbody> --}}
        </table>
    </div>
    <!-- Table End -->
</div>
@push('script_rkpd')
    <script>
    </script>
@endpush
