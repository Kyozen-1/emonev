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

        <!-- Content Start -->
        <!-- Flush Start -->
        {{-- <section class="scroll-section" id="flush">
            <h2 class="small-title">Flush</h2>
            <div class="card mb-5">
              <div class="card-body">
                <div class="accordion accordion-flush" id="accordionFlushExample">
                  <div class="accordion-item">
                    <div class="accordion-header" id="flush-headingOne">
                      <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#flush-collapseOne"
                        aria-expanded="false"
                        aria-controls="flush-collapseOne"
                      >
                        Accordion Item #1
                      </button>
                    </div>
                    <div
                      id="flush-collapseOne"
                      class="accordion-collapse collapse"
                      aria-labelledby="flush-headingOne"
                      data-bs-parent="#accordionFlushExample"
                    >
                      <div class="accordion-body">
                        Placeholder content for this accordion, which is intended to demonstrate the
                        <code>.accordion-flush</code>
                        class. This is the first item's accordion body.
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <div class="accordion-header" id="flush-headingTwo">
                      <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#flush-collapseTwo"
                        aria-expanded="false"
                        aria-controls="flush-collapseTwo"
                      >
                        Accordion Item #2
                      </button>
                    </div>
                    <div
                      id="flush-collapseTwo"
                      class="accordion-collapse collapse"
                      aria-labelledby="flush-headingTwo"
                      data-bs-parent="#accordionFlushExample"
                    >
                      <div class="accordion-body">
                        Placeholder content for this accordion, which is intended to demonstrate the
                        <code>.accordion-flush</code>
                        class. This is the second item's accordion body. Let's imagine this being filled with some actual content.
                      </div>
                    </div>
                  </div>
                  <div class="accordion-item">
                    <div class="accordion-header" id="flush-headingThree">
                      <button
                        class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#flush-collapseThree"
                        aria-expanded="false"
                        aria-controls="flush-collapseThree"
                      >
                        Accordion Item #3
                      </button>
                    </div>
                    <div
                      id="flush-collapseThree"
                      class="accordion-collapse collapse"
                      aria-labelledby="flush-headingThree"
                      data-bs-parent="#accordionFlushExample"
                    >
                      <div class="accordion-body">
                        Placeholder content for this accordion, which is intended to demonstrate the
                        <code>.accordion-flush</code>
                        class. This is the third item's accordion body. Nothing more exciting happening here in terms of content, but just filling up the
                        space to make it look, at least at first glance, a bit more representative of how this would look in a real-world application.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </section> --}}
        <!-- Flush End -->

        <!-- Accordion Cards Start -->
        {{-- <section class="scroll-section" id="accordionCards">
            <div class="mb-n2" id="accordionCardsExample">
                <div class="card d-flex mb-2">
                    <div class="d-flex flex-grow-1" role="button" data-bs-toggle="collapse" data-bs-target="#collapseOneCards" aria-expanded="true" aria-controls="collapseOneCards">
                        <div class="card-body py-4">
                            <div class="btn btn-link list-item-heading p-0">
                                <h2 class="small-title text-left">Misi</h2>
                            </div>
                            <p>
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non
                                cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on
                                it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                                nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic
                                synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                            </p>
                        </div>
                    </div>
                </div>
                <div id="collapseOneCards" class="collapse" data-bs-parent="#accordionCardsExample">

                </div>
            </div>
        </section> --}}
        <!-- Accordion Cards End -->
        <section class="scroll-section">
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

                    <div id="collapseMisi{{$misi['id']}}" class="collapse" data-bs-parent="#accordionMisi{{$misi['id']}}">
                        @php
                            $get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                            $tujuans = [];
                            foreach ($get_tujuans as $get_tujuan) {
                                $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->id)->latest()->first();
                                if($cek_perubahan_tujuan)
                                {
                                    $tujuans[] = [
                                        'id' => $cek_perubahan_tujuan->tujuan_id,
                                        'kode' => $cek_perubahan_tujuan->kode,
                                        'deskripsi' => $cek_perubahan_tujuan->deskripsi
                                    ];
                                } else {
                                    $tujuans[] = [
                                        'id' => $get_tujuan->id,
                                        'kode' => $get_tujuan->kode,
                                        'deskripsi' => $get_tujuan->deskripsi
                                    ];
                                }
                            }
                        @endphp
                        <section class="scroll-section">
                            @foreach ($tujuans as $tujuan)
                                <div class="mb-2" id="accordionTujuan{{$tujuan['id']}}">
                                    <div class="card d-flex mb-2">
                                        <div class="d-flex flex-grow-1" role="button" data-bs-toggle="collapse" data-bs-target="#collapseTujuan{{$tujuan['id']}}" aria-expanded="true" aria-controls="collapseTujuan{{$tujuan['id']}}">
                                            <div class="card-body py-4">
                                                <div class="btn btn-link list-item-heading p-0">
                                                    <h2 class="small-title text-left">Kode Tujuan: {{$tujuan['kode']}}</h2>
                                                </div>
                                                <p>
                                                    {{$tujuan['deskripsi']}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapseTujuan{{$tujuan['id']}}" class="collapse" data-bs-parent="#accordionTujuan{{$tujuan['id']}}">
                                        @php
                                            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
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
                                            <div class="card mb-5">
                                                <div class="card-body">
                                                    @foreach ($sasarans as $sasaran)
                                                        <div class="accordion accordion-flush" id="accordionSasaran{{$sasaran['id']}}">
                                                            <div class="accordion-item">
                                                                <div class="accordion-header" id="flush-sasaran{{$sasaran['id']}}">
                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSasaran{{$sasaran['id']}}" aria-expanded="false" aria-controls="flush-collapseSasaran{{$sasaran['id']}}">
                                                                        Sasaran <br>
                                                                        Kode: {{$sasaran['kode']}}. {{$sasaran['deskripsi']}}
                                                                    </button>
                                                                </div>
                                                                <div id="flush-collapseSasaran{{$sasaran['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-sasaran{{$sasaran['id']}}" data-bs-parent="#accordionSasaran{{$sasaran['id']}}">
                                                                    <div class="accordion-body">
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
                                                                                            <div id="flush-collapseSasaranIndikator{{$sasaran_indikator['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-sasaran-indikator{{$sasaran_indikator['id']}}" data-bs-parent="#accordionSasaranIndikator{{$sasaran['id']}}">
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
                                                                                                                        <td style="text-align: center"><button class="btn btn-primary waves-effect waves-light tambah-rp-pertahun-sasaran" type="button" data-sasaran-indikator-id="{{$sasaran_indikator['id']}}" data-tahun="{{$tahun}}"><i class="fas fa-plus"></i></button></td>
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
                                                                        @php
                                                                            $get_program_rpjmds = ProgramRpjmd::where('sasaran_id', $sasaran['id'])->get();
                                                                            $program_rpjmds = [];
                                                                            foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                                                                $cek_perubahan_program_rpjmd = PivotPerubahanProgramRpjmd::where('program_rpjmd_id', $get_program_rpjmd->id)->latest()->first();
                                                                                if($cek_perubahan_program_rpjmd)
                                                                                {
                                                                                    $program_rpjmds[] = [
                                                                                        'id' => $cek_perubahan_program_rpjmd->program_rpjmd_id,
                                                                                        'program_id' => $cek_perubahan_program_rpjmd->program_id,
                                                                                        'sasaran_id' => $cek_perubahan_program_rpjmd->sasaran_id,
                                                                                        'status_program' => $cek_perubahan_program_rpjmd->status_program,
                                                                                    ];
                                                                                } else {
                                                                                    $program_rpjmds[] = [
                                                                                        'id' => $get_program_rpjmd->id,
                                                                                        'program_id' => $get_program_rpjmd->program_id,
                                                                                        'sasaran_id' => $get_program_rpjmd->sasaran_id,
                                                                                        'status_program' => $get_program_rpjmd->status_program
                                                                                    ];
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <section class="scroll-section">
                                                                            <div class="mb-5">
                                                                                @foreach ($program_rpjmds as $program_rpjmd)
                                                                                    <div class="accordion" id="accordionProgramRPJMD{{$program_rpjmd['id']}}">
                                                                                        <div class="accordion-item">
                                                                                            <div class="accordion-header" id="flush-program-rjpmd{{$program_rpjmd['id']}}">
                                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseProgramRpjmd{{$program_rpjmd['id']}}" aria-expanded="false" aria-controls="flush-collapseProgramRpjmd{{$program_rpjmd['id']}}">
                                                                                                    @php
                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_rpjmd['program_id'])->latest()->first();
                                                                                                        if($cek_perubahan_program)
                                                                                                        {
                                                                                                            $kode_program = $cek_perubahan_program->kode;
                                                                                                            $deskripsi_program = $cek_perubahan_program->deskripsi;
                                                                                                        } else {
                                                                                                            $program = Program::find($program_rpjmd['program_id']);
                                                                                                            $kode_program = $program->kode;
                                                                                                            $deskripsi_program = $program->deskripsi;
                                                                                                        }
                                                                                                    @endphp
                                                                                                    Program <br>
                                                                                                    Kode: {{$kode_program}}. {{$deskripsi_program}}
                                                                                                </button>
                                                                                            </div>
                                                                                            <div id="flush-collapseProgramRpjmd{{$program_rpjmd['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-program-rjpmd{{$program_rpjmd['id']}}" data-bs-parent="#accordionProgramRPJMD{{$sasaran['id']}}">
                                                                                                <div class="accordion-body">

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
                </div>
            @endforeach
        </section>
        <!-- Content End -->
    </div>

    <div class="modal fade" id="addEditTargetRpPertahunSasaranModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
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
        $('.tambah-rp-pertahun-sasaran').click(function(){
            var sasaran_indikator_id = $(this).attr('data-sasaran-indikator-id');
            var sasaran_tahun = $(this).attr('data-tahun');

            $('#target_rp_pertahun_sasaran_tahun').val(sasaran_tahun);
            $('#target_rp_pertahun_sasaran_sasaran_indikator_id').val(sasaran_indikator_id);
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
                            $('#target_rp_pertahun_sasaran_aksi_button').prop('disabled', false);
                            $('#target_rp_pertahun_sasaran_aksi_button').text('Save');
                            $('#sasaran_table').DataTable().ajax.reload();
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }
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
                            $('#target_rp_pertahun_sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            window.location.reload();
                        }
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
    </script>
@endsection
