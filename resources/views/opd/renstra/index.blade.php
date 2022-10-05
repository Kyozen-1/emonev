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
                            $get_tujuans = Tujuan::wherehas('renstra', function($q) use ($misi){
                                $q->where('misi_id', $misi['id']);
                                $q->where('opd_id', Auth::user()->opd_id);
                            })->where('misi_id', $misi['id'])->get();
                            $tujuans = [];
                            foreach ($get_tujuans as $get_tujuan) {
                                $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $get_tujuan->tujuan_id)->latest()->first();
                                if($cek_perubahan_tujuan)
                                {
                                    $tujuans[] = [
                                        'id' => $cek_perubahan_tujuan->tujuan_id,
                                        'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                        'kode' => $cek_perubahan_tujuan->kode
                                    ];
                                } else {
                                    $tujuans[] = [
                                        'id' => $get_tujuan->tujuan_id,
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
                                                                            <div id="flush-collapseTujuanIndikator{{$tujuan_indikator['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-tujuan-indikator{{$tujuan_indikator['id']}}" data-bs-parent="#accordionTujuanIndikator{{$tujuan['id']}}">
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
                                                                                                                                    ->where('renstra_id', $misi['renstra_id'])
                                                                                                                                    ->where('tujuan_id', $tujuan['id'])
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
                                                                                                        <td style="text-align: center"><button class="btn btn-primary waves-effect waves-light tambah-rp-pertahun-tujuan" type="button" data-tujuan-indikator-id="{{$tujuan_indikator['id']}}" data-tahun="{{$tahun}}" data-renstra-id="{{$misi['renstra_id']}}" data-tujuan-id="{{$tujuan['id']}}"><i class="fas fa-plus"></i></button></td>
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
                                                        {{-- @php
                                                            $get_sasarans = Sasaran::where('tujuan_id', $tujuan['id'])->get();
                                                            $sasarans = [];
                                                            foreach ($get_sasarans as $get_sasaran) {
                                                                $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_sasaran->id)->latest()->first();
                                                                if($cek_perubahan_sasaran)
                                                                {
                                                                    $sasarans[] = [
                                                                        'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                        'program_id' => $cek_perubahan_sasaran->program_id,
                                                                        'sasaran_id' => $cek_perubahan_sasaran->sasaran_id,
                                                                        'status_program' => $cek_perubahan_sasaran->status_program,
                                                                    ];
                                                                } else {
                                                                    $sasarans[] = [
                                                                        'id' => $get_sasaran->id,
                                                                        'program_id' => $get_sasaran->program_id,
                                                                        'sasaran_id' => $get_sasaran->sasaran_id,
                                                                        'status_program' => $get_sasaran->status_program
                                                                    ];
                                                                }
                                                            }
                                                        @endphp
                                                        <section class="scroll-section">
                                                            <div class="mb-5">
                                                                @foreach ($sasarans as $sasaran)
                                                                    <div class="accordion" id="accordionSasaran{{$sasaran['id']}}">
                                                                        <div class="accordion-item">
                                                                            <div class="accordion-header" id="flush-program-rjpmd{{$sasaran['id']}}">
                                                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSasaran{{$sasaran['id']}}" aria-expanded="false" aria-controls="flush-collapseSasaran{{$sasaran['id']}}">
                                                                                    @php
                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $sasaran['program_id'])->latest()->first();
                                                                                        if($cek_perubahan_program)
                                                                                        {
                                                                                            $kode_program = $cek_perubahan_program->kode;
                                                                                            $deskripsi_program = $cek_perubahan_program->deskripsi;
                                                                                        } else {
                                                                                            $program = Program::find($sasaran['program_id']);
                                                                                            $kode_program = $program->kode;
                                                                                            $deskripsi_program = $program->deskripsi;
                                                                                        }
                                                                                    @endphp
                                                                                    Program <br>
                                                                                    Kode: {{$kode_program}}. {{$deskripsi_program}}
                                                                                </button>
                                                                            </div>
                                                                            <div id="flush-collapseSasaran{{$sasaran['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-program-rjpmd{{$sasaran['id']}}" data-bs-parent="#accordionSasaran{{$tujuan['id']}}">
                                                                                <div class="accordion-body">

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </section> --}}
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

    <div class="modal fade" id="addEditTargetRpPertahunRentraModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="target_rp_pertahun_tujuan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="target_rp_pertahun_tujuan_tahun" id="target_rp_pertahun_tujuan_tahun">
                        <input type="hidden" name="target_rp_pertahun_tujuan_tujuan_indikator_id" id="target_rp_pertahun_tujuan_tujuan_indikator_id">
                        <input type="hidden" name="target_rp_pertahun_tujuan_renstra_id" id="target_rp_pertahun_tujuan_renstra_id">
                        <input type="hidden" name="target_rp_pertahun_tujuan_tujuan_id" id="target_rp_pertahun_tujuan_tujuan_id">
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
                        $.each(response, function(){
                            $('#program_id').append(new Option(response.deskripsi, response.id));
                        });
                    }
                });
            }
        });
    </script>
@endsection
