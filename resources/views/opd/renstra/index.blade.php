@extends('opd.layouts.app')
@section('title', 'OPD | Renstra')

@section('css')
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/datatables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/select2-bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/bootstrap-datepicker3.standalone.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('acorn/acorn-elearning-portal/css/vendor/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dropify/css/dropify.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/fontawesome.min.css" integrity="sha512-RvQxwf+3zJuNwl4e0sZjQeX7kUa3o82bDETpgVCH2RiwYSZVDdFJ7N/woNigN/ldyOOoKw8584jM4plQdt8bhA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .select2-selection__rendered {
            line-height: 40px !important;
        }
        .select2-container .select2-selection--single {
            height: 41px !important;
        }
        .select2-selection__arrow {
            height: 36px !important;
        }
        .select2-container{
            z-index:100000;
        }
    </style>
@endsection

@section('content')
@php
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
    use App\Models\ProgramRpjmd;
    use App\Models\PivotPerubahanProgramRpjmd;
    use App\Models\Urusan;
    use App\Models\PivotPerubahanUrusan;
    use App\Models\Program;
    use App\Models\PivotPerubahanProgram;
    use App\Models\MasterOpd;
    use App\Models\TahunPeriode;
    use App\Models\TargetRpPertahunTujuan;
    use App\Models\TargetRpPertahunSasaran;
    use App\Models\TargetRpPertahunProgram;
    use App\Models\PivotTujuanIndikator;
    use App\Models\Renstra;
    use App\Models\PivotProgramIndikator;

    $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
    $tahun_awal = $get_periode->tahun_awal;
    $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
    $tahuns = [];
    for ($i=0; $i < $jarak_tahun + 1; $i++) {
        $tahuns[] = $tahun_awal + $i;
    }
@endphp
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Renstra</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('opd.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Renstra</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <div class="card mb-5">
            <div class="card-body">
                <div class="row">
                    <div class="col-6 justify-content-center align-self-center">
                        <label for="" class="form-label">Tambah Item Renstra</label>
                    </div>
                    <div class="col-6" style="text-align: right">
                        <button class="btn btn-icon btn-primary waves-effect waves-light tambah-itme-renstra" type="button" data-bs-toggle="modal" data-bs-target="#addEditItemRenstraModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <section class="scroll-section">
            {{-- Misi --}}
            @foreach ($misis as $misi)
                <div class="mb-2" id="accordionMisi{{$misi['id']}}">
                    <div class="card d-flex mb-2">
                        <div class="d-flex flex-grow-1" role="button" data-bs-toggle="collapse" data-bs-target="#collapseMisi{{$misi['id']}}" aria-expanded="true" aria-controls="collapseMisi{{$misi['id']}}">
                            <div class="card-body py-4">
                                <div class="btn btn-link list-item-heading p-0">
                                    <h2 class="small-title text-left">Kode Misi: {{$misi['kode']}}</h2>
                                </div>
                                <p>
                                    {{$misi['deskripsi']}}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="collapse" id="collapseMisi{{$misi['id']}}" data-bs-parent="#accordionMisi{{$misi['id']}}">
                        {{-- Tujuan --}}
                        @php
                            $get_tujuans = Tujuan::select('id', 'deskripsi', 'kode')->wherehas('renstra', function($q) use ($misi){
                                $q->where('misi_id', $misi['id']);
                                $q->whereHas('tujuan');
                                $q->where('opd_id', Auth::user()->opd->opd_id);
                            })->where('misi_id', $misi['id'])->groupBy('id')->get();
                            $tujuans = [];
                            foreach ($get_tujuans as $get_tujuan) {
                                $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->latest()->first();
                                if($cek_perubahan_tujuan)
                                {
                                    $tujuans[] = [
                                        'id' => $cek_perubahan_tujuan->tujuan_id,
                                        'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                        'kode' => $cek_perubahan_tujuan->kode
                                    ];
                                } else {
                                    $tujuans[] = [
                                        'id' => $get_tujuan->id,
                                        'deskripsi' => $get_tujuan->deskripsi,
                                        'kode' => $get_tujuan->kode
                                    ];
                                }
                            }
                        @endphp
                        <section class="scroll-section">
                            <div class="card mb-5">
                                <div class="card-body">
                                    @foreach ($tujuans as $tujuan)
                                        <div class="accordion accordion-flush" id="accordionTujuan{{$tujuan['id']}}">
                                            <div class="accordion-item">
                                                <div class="accordion-header" id="flush-tujuan{{$tujuan['id']}}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTujuan{{$tujuan['id']}}" aria-expanded="false" aria-controls="flush-collapseTujuan{{$tujuan['id']}}">
                                                        Tujuan <br>
                                                        Kode: {{$tujuan['kode']}}. {{$tujuan['deskripsi']}}
                                                    </button>
                                                </div>
                                                <div id="flush-collapseTujuan{{$tujuan['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-tujuan{{$tujuan['id']}}" data-bs-parent="#accordionTujuan{{$tujuan['id']}}">
                                                    <div class="accordion-body">
                                                        {{-- Tujuan Indikator --}}
                                                        @php
                                                            $tujuan_indikators = PivotTujuanIndikator::where('tujuan_id', $tujuan['id'])->get();
                                                        @endphp
                                                        <section class="scroll-section">
                                                            <div class="mb-5">
                                                                @php
                                                                    $a = 1;
                                                                @endphp
                                                                @foreach ($tujuan_indikators as $tujuan_indikator)
                                                                    <div class="accordion" id="accordionTujuanIndikator{{$tujuan_indikator['id']}}">
                                                                        <div class="accordion-item">
                                                                            <div class="accordion-header" id="flush-tujuan-indikator{{$tujuan_indikator['id']}}">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTujuanIndikator{{$tujuan_indikator['id']}}" aria-expanded="false" aria-controls="flush-collapseTujuanIndikator{{$tujuan_indikator['id']}}">
                                                                                    Tujuan Indikator <br>
                                                                                    {{$a++}}. Indikator: {{$tujuan_indikator->indikator}}
                                                                                </button>
                                                                            </div>
                                                                            <div id="flush-collapseTujuanIndikator{{$tujuan_indikator['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-tujuan-indikator{{$tujuan_indikator['id']}}" data-bs-parent="#accordionTujuanIndikator{{$tujuan_indikator['id']}}">
                                                                                <div class="accordion-body">
                                                                                    <table class="table table-borderless">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th width="70%">Indikator</th>
                                                                                                <th width="15%">Target</th>
                                                                                                <th width="15%">Satuan</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td>{{$tujuan_indikator->indikator}}</td>
                                                                                                <td>{{$tujuan_indikator->target}}</td>
                                                                                                <td>{{$tujuan_indikator->satuan}}</td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <hr>
                                                                                    <h2 class="small-title text-left">Atur Target Tujuan Indikator</h2>
                                                                                    <table class="table table-borderless">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th style="text-align: center">Tahun</th>
                                                                                                <th style="text-align: center">Target</th>
                                                                                                <th style="text-align: center">RP</th>
                                                                                                <th style="text-align: center">Aksi</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($tahuns as $tahun)
                                                                                                @php
                                                                                                    $target_rp_pertahun_tujuan = TargetRpPertahunTujuan::where('tahun', $tahun)
                                                                                                                                    ->where('pivot_tujuan_indikator_id', $tujuan_indikator['id'])
                                                                                                                                    ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                                    ->first();
                                                                                                @endphp
                                                                                                <tr>
                                                                                                    <td style="text-align: center">{{$tahun}}</td>
                                                                                                    @if ($target_rp_pertahun_tujuan)
                                                                                                        <td style="text-align: center">{{$target_rp_pertahun_tujuan->target}}</td>
                                                                                                        <td style="text-align: center">{{$target_rp_pertahun_tujuan->rp}}</td>
                                                                                                        <td style="text-align: center"><button class="btn btn-warning waves-effect waves-light edit-rp-pertahun-tujuan" type="button" data-id="{{$target_rp_pertahun_tujuan->id}}"><i class="fas fa-edit"></i></button></td>
                                                                                                    @else
                                                                                                        <td style="text-align: center"></td>
                                                                                                        <td style="text-align: center"></td>
                                                                                                        <td style="text-align: center"><button class="btn btn-primary waves-effect waves-light tambah-rp-pertahun-tujuan" type="button" data-tujuan-indikator-id="{{$tujuan_indikator['id']}}" data-tahun="{{$tahun}}" ><i class="fas fa-plus"></i></button></td>
                                                                                                    @endif
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </section>
                                                        <hr>
                                                        {{-- Sasaran --}}
                                                        @php
                                                            $get_sasarans = Sasaran::select('id', 'deskripsi', 'kode')
                                                                            ->wherehas('renstra', function($q) use ($misi, $tujuan){
                                                                                $q->where('misi_id', $misi['id']);
                                                                                $q->where('tujuan_id', $tujuan['id']);
                                                                                $q->whereHas('sasaran');
                                                                                $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                            })
                                                                            ->where('tujuan_id', $tujuan['id'])
                                                                            ->groupBy('id')
                                                                            ->get();
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
                                                        @endphp
                                                        <section class="scroll-section">
                                                            <div class="mb-5">
                                                                @foreach ($sasarans as $sasaran)
                                                                    <div class="accordion" id="accordionSasaran{{$sasaran['id']}}">
                                                                        <div class="accordion-item">
                                                                            <div class="accordion-header" id="flush-sasaran{{$sasaran['id']}}">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSasaran{{$sasaran['id']}}" aria-expanded="false" aria-controls="flush-collapseSasaran{{$sasaran['id']}}">
                                                                                    Sasaran <br>
                                                                                    Kode: {{$sasaran['kode']}}. {{$sasaran['deskripsi']}}
                                                                                </button>
                                                                            </div>
                                                                            <div id="flush-collapseSasaran{{$sasaran['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-sasaran{{$sasaran['id']}}" data-bs-parent="#accordionSasaran{{$sasaran['id']}}">
                                                                                <div class="accordion-body">
                                                                                    {{-- Sasaran Indikator --}}
                                                                                @php
                                                                                    $sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                @endphp
                                                                                <section class="scroll-section">
                                                                                    <div class="mb-5">
                                                                                        @php
                                                                                            $a = 1;
                                                                                        @endphp
                                                                                        @foreach ($sasaran_indikators as $sasaran_indikator)
                                                                                            <div class="accordion" id="accordionSasaranIndikator{{$sasaran_indikator['id']}}">
                                                                                                <div class="accordion-item">
                                                                                                    <div class="accordion-header" id="flush-sasaran-indikator{{$sasaran_indikator['id']}}">
                                                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSasaranIndikator{{$sasaran_indikator['id']}}" aria-expanded="false" aria-controls="flush-collapseSasaranIndikator{{$sasaran_indikator['id']}}">
                                                                                                            Sasaran Indikator <br>
                                                                                                            {{$a++}}. Indikator: {{$sasaran_indikator->indikator}}
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div id="flush-collapseSasaranIndikator{{$sasaran_indikator['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-sasaran-indikator{{$sasaran_indikator['id']}}" data-bs-parent="#accordionSasaranIndikator{{$sasaran_indikator['id']}}">
                                                                                                        <div class="accordion-body">
                                                                                                            <table class="table table-borderless">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th width="70%">Indikator</th>
                                                                                                                        <th width="15%">Target</th>
                                                                                                                        <th width="15%">Satuan</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    <tr>
                                                                                                                        <td>{{$sasaran_indikator->indikator}}</td>
                                                                                                                        <td>{{$sasaran_indikator->target}}</td>
                                                                                                                        <td>{{$sasaran_indikator->satuan}}</td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <hr>
                                                                                                            <h2 class="small-title text-left">Atur Target Sasaran Indikator</h2>
                                                                                                            <table class="table table-borderless">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th style="text-align: center">Tahun</th>
                                                                                                                        <th style="text-align: center">Target</th>
                                                                                                                        <th style="text-align: center">RP</th>
                                                                                                                        <th style="text-align: center">Aksi</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    @foreach ($tahuns as $tahun)
                                                                                                                        @php
                                                                                                                            $target_rp_pertahun_sasaran = TargetRpPertahunSasaran::where('tahun', $tahun)
                                                                                                                                                            ->where('pivot_sasaran_indikator_id', $sasaran_indikator['id'])
                                                                                                                                                            ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                                                            ->first();
                                                                                                                        @endphp
                                                                                                                        <tr>
                                                                                                                            <td style="text-align: center">{{$tahun}}</td>
                                                                                                                            @if ($target_rp_pertahun_sasaran)
                                                                                                                                <td style="text-align: center">{{$target_rp_pertahun_sasaran->target}}</td>
                                                                                                                                <td style="text-align: center">{{$target_rp_pertahun_sasaran->rp}}</td>
                                                                                                                                <td style="text-align: center"><button class="btn btn-warning waves-effect waves-light edit-rp-pertahun-sasaran" type="button" data-id="{{$target_rp_pertahun_sasaran->id}}"><i class="fas fa-edit"></i></button></td>
                                                                                                                            @else
                                                                                                                                <td style="text-align: center"></td>
                                                                                                                                <td style="text-align: center"></td>
                                                                                                                                <td style="text-align: center"><button class="btn btn-primary waves-effect waves-light tambah-rp-pertahun-sasaran" type="button" data-sasaran-indikator-id="{{$sasaran_indikator['id']}}" data-tahun="{{$tahun}}" ><i class="fas fa-plus"></i></button></td>
                                                                                                                            @endif
                                                                                                                        </tr>
                                                                                                                    @endforeach
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </section>
                                                                                <hr>
                                                                                {{-- Program --}}
                                                                                @php
                                                                                    $get_programs = Program::select('id', 'deskripsi', 'kode')
                                                                                                    ->wherehas('renstra', function($q) use ($misi, $tujuan, $sasaran){
                                                                                                        $q->where('misi_id', $misi['id']);
                                                                                                        $q->where('tujuan_id', $tujuan['id']);
                                                                                                        $q->where('sasaran_id', $sasaran['id']);
                                                                                                        $q->whereHas('program');
                                                                                                        $q->where('opd_id', Auth::user()->opd->opd_id);
                                                                                                    })
                                                                                                    ->whereHas('program_rpjmd', function($q) use ($sasaran){
                                                                                                        $q->where('sasaran_id', $sasaran['id']);
                                                                                                    })
                                                                                                    ->groupBy('id')
                                                                                                    ->get();
                                                                                    $programs = [];
                                                                                    foreach ($get_programs as $get_program) {
                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->latest()->first();
                                                                                        if($cek_perubahan_program)
                                                                                        {
                                                                                            $programs[] = [
                                                                                                'id' => $cek_perubahan_program->program_id,
                                                                                                'kode' => $cek_perubahan_program->kode,
                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi
                                                                                            ];
                                                                                        } else {
                                                                                            $programs[] = [
                                                                                                'id' => $get_program->id,
                                                                                                'kode' => $get_program->kode,
                                                                                                'deskripsi' => $get_program->deskripsi
                                                                                            ];
                                                                                        }
                                                                                    }
                                                                                @endphp
                                                                                <section class="scroll-section">
                                                                                    <div class="mb-5">
                                                                                        @foreach ($programs as $program)
                                                                                            <div class="accordion" id="accordionProgram{{$program['id']}}">
                                                                                                <div class="accordion-item">
                                                                                                    <div class="accordion-header" id="flush-program{{$program['id']}}">
                                                                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseProgram{{$program['id']}}" aria-expanded="false" aria-controls="flush-collapseProgram{{$program['id']}}">
                                                                                                            Program <br>
                                                                                                            Kode: {{$program['kode']}}. {{$program['deskripsi']}}
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div id="flush-collapseProgram{{$program['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-program{{$program['id']}}" data-bs-parent="#accordionProgram{{$program['id']}}">
                                                                                                        <div class="accordion-body">
                                                                                                            @php
                                                                                                                $program_indikators = PivotProgramIndikator::where('program_id', $program['id'])->get();
                                                                                                            @endphp
                                                                                                            <section class="scroll-section">
                                                                                                                <div class="mb-5">
                                                                                                                    @php
                                                                                                                        $a = 1;
                                                                                                                    @endphp
                                                                                                                    @foreach ($program_indikators as $program_indikator)
                                                                                                                        <div class="accordion" id="accordionProgramIndikator{{$program_indikator['id']}}">
                                                                                                                            <div class="accordion-item">
                                                                                                                                <div class="accordion-header" id="flush-program-indikator{{$program_indikator['id']}}">
                                                                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseProgramIndikator{{$program_indikator['id']}}" aria-expanded="false" aria-controls="flush-collapseProgramIndikator{{$program_indikator['id']}}">
                                                                                                                                        Program Indikator <br>
                                                                                                                                        {{$a++}}. Indikator: {{$program_indikator->indikator}}
                                                                                                                                    </button>
                                                                                                                                </div>
                                                                                                                                <div id="flush-collapseProgramIndikator{{$program_indikator['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-program-indikator{{$program_indikator['id']}}" data-bs-parent="#accordionProgramIndikator{{$program_indikator['id']}}">
                                                                                                                                    <div class="accordion-body">
                                                                                                                                        <table class="table table-borderless">
                                                                                                                                            <thead>
                                                                                                                                                <tr>
                                                                                                                                                    <th width="70%">Indikator</th>
                                                                                                                                                    <th width="15%">Target</th>
                                                                                                                                                    <th width="15%">Satuan</th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                <tr>
                                                                                                                                                    <td>{{$program_indikator->indikator}}</td>
                                                                                                                                                    <td>{{$program_indikator->target}}</td>
                                                                                                                                                    <td>{{$program_indikator->satuan}}</td>
                                                                                                                                                </tr>
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                        <hr>
                                                                                                                                        <h2 class="small-title text-left">Atur Target Program Indikator</h2>
                                                                                                                                        <table class="table table-borderless">
                                                                                                                                            <thead>
                                                                                                                                                <tr>
                                                                                                                                                    <th style="text-align: center">Tahun</th>
                                                                                                                                                    <th style="text-align: center">Target</th>
                                                                                                                                                    <th style="text-align: center">RP</th>
                                                                                                                                                    <th style="text-align: center">Aksi</th>
                                                                                                                                                </tr>
                                                                                                                                            </thead>
                                                                                                                                            <tbody>
                                                                                                                                                @foreach ($tahuns as $tahun)
                                                                                                                                                    @php
                                                                                                                                                        $target_rp_pertahun_program = TargetRpPertahunProgram::where('tahun', $tahun)
                                                                                                                                                                                        ->where('pivot_program_indikator_id', $program_indikator['id'])
                                                                                                                                                                                        ->where('opd_id', Auth::user()->opd->opd_id)
                                                                                                                                                                                        ->first();
                                                                                                                                                    @endphp
                                                                                                                                                    <tr>
                                                                                                                                                        <td style="text-align: center">{{$tahun}}</td>
                                                                                                                                                        @if ($target_rp_pertahun_program)
                                                                                                                                                            <td style="text-align: center">{{$target_rp_pertahun_program->target}}</td>
                                                                                                                                                            <td style="text-align: center">{{$target_rp_pertahun_program->rp}}</td>
                                                                                                                                                            <td style="text-align: center"><button class="btn btn-warning waves-effect waves-light edit-rp-pertahun-program" type="button" data-id="{{$target_rp_pertahun_program->id}}"><i class="fas fa-edit"></i></button></td>
                                                                                                                                                        @else
                                                                                                                                                            <td style="text-align: center"></td>
                                                                                                                                                            <td style="text-align: center"></td>
                                                                                                                                                            <td style="text-align: center"><button class="btn btn-primary waves-effect waves-light tambah-rp-pertahun-program" type="button" data-program-indikator-id="{{$program_indikator['id']}}" data-tahun="{{$tahun}}" ><i class="fas fa-plus"></i></button></td>
                                                                                                                                                        @endif
                                                                                                                                                    </tr>
                                                                                                                                                @endforeach
                                                                                                                                            </tbody>
                                                                                                                                        </table>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    @endforeach
                                                                                                                </div>
                                                                                                            </section>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endforeach
                                                                                    </div>
                                                                                </section>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            @endforeach
        </section>
    </div>

    <div class="modal fade" id="addEditItemRenstraModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tambah-item-renstra') }}" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Misi</label>
                            <select name="misi_id" id="misi_id" class="form-control" required>
                                <option value="">--- Pilih Misi ---</option>
                                @foreach ($misis as $misi)
                                    <option value="{{$misi['id']}}">{{$misi['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Tujuan</label>
                            <select name="tujuan_id" id="tujuan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Tujuan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Sasaran</label>
                            <select name="sasaran_id" id="sasaran_id" class="form-controlf" disabled required>
                                <option value="">--- Pilih Sasaran --- </option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-controlf" disabled required>
                                <option value="">--- Pilih Program --- </option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEditTargetRpPertahunTujuanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span id="target_rp_pertahun_tujuan_form_result"></span>
                <div class="modal-body">
                    <form id="target_rp_pertahun_tujuan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="target_rp_pertahun_tujuan_tahun" id="target_rp_pertahun_tujuan_tahun">
                        <input type="hidden" name="target_rp_pertahun_tujuan_tujuan_indikator_id" id="target_rp_pertahun_tujuan_tujuan_indikator_id">
                        <div class="mb-3">
                            <label class="form-label">Target</label>
                            <input name="target_rp_pertahun_tujuan_target" id="target_rp_pertahun_tujuan_target" type="text" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Rp</label>
                            <input type="text" class="form-control" id="target_rp_pertahun_tujuan_rp" name="target_rp_pertahun_tujuan_rp" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="target_rp_pertahun_tujuan_aksi" id="target_rp_pertahun_tujuan_aksi" value="Save">
                    <input type="hidden" name="target_rp_pertahun_tujuan_hidden_id" id="target_rp_pertahun_tujuan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="target_rp_pertahun_tujuan_aksi_button" id="target_rp_pertahun_tujuan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEditTargetRpPertahunSasaranModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span id="target_rp_pertahun_sasaran_form_result"></span>
                <div class="modal-body">
                    <form id="target_rp_pertahun_sasaran_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="target_rp_pertahun_sasaran_tahun" id="target_rp_pertahun_sasaran_tahun">
                        <input type="hidden" name="target_rp_pertahun_sasaran_sasaran_indikator_id" id="target_rp_pertahun_sasaran_sasaran_indikator_id">
                        <div class="mb-3">
                            <label class="form-label">Target</label>
                            <input name="target_rp_pertahun_sasaran_target" id="target_rp_pertahun_sasaran_target" type="text" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Rp</label>
                            <input type="text" class="form-control" id="target_rp_pertahun_sasaran_rp" name="target_rp_pertahun_sasaran_rp" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="target_rp_pertahun_sasaran_aksi" id="target_rp_pertahun_sasaran_aksi" value="Save">
                    <input type="hidden" name="target_rp_pertahun_sasaran_hidden_id" id="target_rp_pertahun_sasaran_hidden_id">
                    <button type="submit" class="btn btn-primary" name="target_rp_pertahun_sasaran_aksi_button" id="target_rp_pertahun_sasaran_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEditTargetRpPertahunProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <span id="target_rp_pertahun_program_form_result"></span>
                <div class="modal-body">
                    <form id="target_rp_pertahun_program_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="target_rp_pertahun_program_tahun" id="target_rp_pertahun_program_tahun">
                        <input type="hidden" name="target_rp_pertahun_program_program_indikator_id" id="target_rp_pertahun_program_program_indikator_id">
                        <div class="mb-3">
                            <label class="form-label">Target</label>
                            <input name="target_rp_pertahun_program_target" id="target_rp_pertahun_program_target" type="text" class="form-control" required/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Rp</label>
                            <input type="text" class="form-control" id="target_rp_pertahun_program_rp" name="target_rp_pertahun_program_rp" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="target_rp_pertahun_program_aksi" id="target_rp_pertahun_program_aksi" value="Save">
                    <input type="hidden" name="target_rp_pertahun_program_hidden_id" id="target_rp_pertahun_program_hidden_id">
                    <button type="submit" class="btn btn-primary" name="target_rp_pertahun_program_aksi_button" id="target_rp_pertahun_program_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/bootstrap-submenu.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/datatables.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/cs/scrollspy.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/jquery.validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/jquery.validate/additional-methods.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/select2.full.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/tagify.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/dropzone.min.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/singleimageupload.js') }}"></script>
    <script src="{{ asset('acorn/acorn-elearning-portal/js/cs/dropzone.templates.js') }}"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js" integrity="sha512-naukR7I+Nk6gp7p5TMA4ycgfxaZBJ7MO5iC3Fp6ySQyKFHOGfpkSZkYVWV5R7u7cfAicxanwYQ5D1e17EfJcMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/fontawesome.min.js" integrity="sha512-j3gF1rYV2kvAKJ0Jo5CdgLgSYS7QYmBVVUjduXdoeBkc4NFV4aSRTi+Rodkiy9ht7ZYEwF+s09S43Z1Y+ujUkA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function(){
            $('#misi_id').select2();
            $('#tujuan_id').select2();
            $('#sasaran_id').select2();
            $('#program_id').select2();
        });

        $('#misi_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#tujuan_id').empty();
                        $('#tujuan_id').prop('disabled', false);
                        $('#tujuan_id').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#tujuan_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            }
        });

        $('#tujuan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#sasaran_id').empty();
                        $('#sasaran_id').prop('disabled', false);
                        $('#sasaran_id').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#sasaran_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            }
        });

        $('#sasaran_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-program-rpjmd') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_id').empty();
                        $('#program_id').prop('disabled', false);
                        $('#program_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#program_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            }
        });

        $('.tambah-rp-pertahun-tujuan').click(function(){
            var tujuan_indikator_id = $(this).attr('data-tujuan-indikator-id');
            var tahun = $(this).attr('data-tahun');
            $('#target_rp_pertahun_tujuan_tahun').val(tahun);
            $('#target_rp_pertahun_tujuan_tujuan_indikator_id').val(tujuan_indikator_id);
            $('#target_rp_pertahun_tujuan_form_result').html('');
            $('.modal-title').text('Tambah Data');
            $('#target_rp_pertahun_tujuan_aksi_button').text('Save');
            $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', false);
            $('#target_rp_pertahun_tujuan_aksi_button').val('Save');
            $('#target_rp_pertahun_tujuan_aksi').val('Save');
            $('#target_rp_pertahun_tujuan_form')[0].reset();
            $('#addEditTargetRpPertahunTujuanModal').modal('show');
        });

        $('#target_rp_pertahun_tujuan_form').on('submit', function(e){
            e.preventDefault();
            if($('#target_rp_pertahun_tujuan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-tujuan.tambah') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#target_rp_pertahun_tujuan_aksi_button').text('Menyimpan...');
                        $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }

                        $('#target_rp_pertahun_tujuan_form_result').html(html);
                    }
                });
            }
            if($('#target_rp_pertahun_tujuan_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-tujuan.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function(){
                        $('#target_rp_pertahun_tujuan_aksi_button').text('Mengubah...');
                        $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }
                        $('#target_rp_pertahun_tujuan_form_result').html(html);
                    }
                });
            }
        });

        $('.edit-rp-pertahun-tujuan').click(function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/target-rp-pertahun-tujuan/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#target_rp_pertahun_tujuan_target').val(data.result.target);
                    $('#target_rp_pertahun_tujuan_rp').val(data.result.rp);
                    $('#target_rp_pertahun_tujuan_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#target_rp_pertahun_tujuan_aksi_button').text('Edit');
                    $('#target_rp_pertahun_tujuan_aksi_button').prop('disabled', false);
                    $('#target_rp_pertahun_tujuan_aksi_button').val('Edit');
                    $('#target_rp_pertahun_tujuan_aksi').val('Edit');
                    $('#addEditTargetRpPertahunTujuanModal').modal('show');
                }
            });
        });

        $('.tambah-rp-pertahun-sasaran').click(function(){
            var sasaran_indikator_id = $(this).attr('data-sasaran-indikator-id');
            var tahun = $(this).attr('data-tahun');
            $('#target_rp_pertahun_sasaran_tahun').val(tahun);
            $('#target_rp_pertahun_sasaran_sasaran_indikator_id').val(sasaran_indikator_id);
            $('#target_rp_pertahun_sasaran_form_result').html('');
            $('.modal-title').text('Tambah Data');
            $('#target_rp_pertahun_sasaran_aksi_button').text('Save');
            $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', false);
            $('#target_rp_pertahun_sasaran_aksi_button').val('Save');
            $('#target_rp_pertahun_sasaran_aksi').val('Save');
            $('#target_rp_pertahun_sasaran_form')[0].reset();
            $('#addEditTargetRpPertahunSasaranModal').modal('show');
        });

        $('#target_rp_pertahun_sasaran_form').on('submit', function(e){
            e.preventDefault();
            if($('#target_rp_pertahun_sasaran_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-sasaran.tambah') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#target_rp_pertahun_sasaran_aksi_button').text('Menyimpan...');
                        $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }

                        $('#target_rp_pertahun_sasaran_form_result').html(html);
                    }
                });
            }
            if($('#target_rp_pertahun_sasaran_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-sasaran.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function(){
                        $('#target_rp_pertahun_sasaran_aksi_button').text('Mengubah...');
                        $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }
                        $('#target_rp_pertahun_sasaran_form_result').html(html);
                    }
                });
            }
        });

        $('.edit-rp-pertahun-sasaran').click(function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/target-rp-pertahun-sasaran/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#target_rp_pertahun_sasaran_target').val(data.result.target);
                    $('#target_rp_pertahun_sasaran_rp').val(data.result.rp);
                    $('#target_rp_pertahun_sasaran_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#target_rp_pertahun_sasaran_aksi_button').text('Edit');
                    $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', false);
                    $('#target_rp_pertahun_sasaran_aksi_button').val('Edit');
                    $('#target_rp_pertahun_sasaran_aksi').val('Edit');
                    $('#addEditTargetRpPertahunSasaranModal').modal('show');
                }
            });
        });

        $('.tambah-rp-pertahun-program').click(function(){
            var program_indikator_id = $(this).attr('data-program-indikator-id');
            var tahun = $(this).attr('data-tahun');
            $('#target_rp_pertahun_program_tahun').val(tahun);
            $('#target_rp_pertahun_program_program_indikator_id').val(program_indikator_id);
            $('#target_rp_pertahun_program_form_result').html('');
            $('.modal-title').text('Tambah Data');
            $('#target_rp_pertahun_program_aksi_button').text('Save');
            $('#target_rp_pertahun_program_aksi_button').prop('disabled', false);
            $('#target_rp_pertahun_program_aksi_button').val('Save');
            $('#target_rp_pertahun_program_aksi').val('Save');
            $('#target_rp_pertahun_program_form')[0].reset();
            $('#addEditTargetRpPertahunProgramModal').modal('show');
        });

        $('#target_rp_pertahun_program_form').on('submit', function(e){
            e.preventDefault();
            if($('#target_rp_pertahun_program_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-program.tambah') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#target_rp_pertahun_program_aksi_button').text('Menyimpan...');
                        $('#target_rp_pertahun_program_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_program_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_program_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }

                        $('#target_rp_pertahun_program_form_result').html(html);
                    }
                });
            }
            if($('#target_rp_pertahun_program_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.target-rp-pertahun-program.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function(){
                        $('#target_rp_pertahun_program_aksi_button').text('Mengubah...');
                        $('#target_rp_pertahun_program_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#target_rp_pertahun_program_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_program_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }
                        $('#target_rp_pertahun_program_form_result').html(html);
                    }
                });
            }
        });

        $('.edit-rp-pertahun-program').click(function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/target-rp-pertahun-program/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#target_rp_pertahun_program_target').val(data.result.target);
                    $('#target_rp_pertahun_program_rp').val(data.result.rp);
                    $('#target_rp_pertahun_program_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#target_rp_pertahun_program_aksi_button').text('Edit');
                    $('#target_rp_pertahun_program_aksi_button').prop('disabled', false);
                    $('#target_rp_pertahun_program_aksi_button').val('Edit');
                    $('#target_rp_pertahun_program_aksi').val('Edit');
                    $('#addEditTargetRpPertahunProgramModal').modal('show');
                }
            });
        });
    </script>
@endsection
