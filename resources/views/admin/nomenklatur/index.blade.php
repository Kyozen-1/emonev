@extends('admin.layouts.app')
@section('title', 'Admin | Nomenklatur')

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
        .hiddenRow {
            padding: 0 !important;
        }
        @media (min-width: 374px) {
            .scrollBarPagination {
                height:200px;
                overflow-y: scroll;
            }
        }
        @media (min-width: 992px) {
            .scrollBarPagination {
                height:450px;
                overflow-y: scroll;
            }
        }

        @media (min-height: 1300px) {
            .scrollBarPagination {
                height:700px;
                overflow-y: scroll;
            }
        }
    </style>
@endsection

@section('content')
    @php
        use App\Models\Urusan;
        use App\Models\PivotPerubahanUrusan;
        use App\Models\Program;
        use App\Models\PivotPerubahanProgram;

        // $get_kodes = Urusan::select('kode')->distinct('kode')->get();
        // $urusans = [];
        // $a = 1;
        // foreach ($get_kodes as $kode) {
        //     $latest_urusan = Urusan::where('kode', $kode->kode)->orderBy('tahun_perubahan', 'desc')->first();
        //     $urusans[] = $latest_urusan;
        // }
        $get_urusans = Urusan::all();
        $urusans = [];
        foreach ($get_urusans as $get_urusan) {
            $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $get_urusan->id)->latest()->first();
            if($cek_perubahan_urusan)
            {
                $urusans[] = [
                    'id' => $cek_perubahan_urusan->urusan_id,
                    'kode' => $cek_perubahan_urusan->kode,
                    'deskripsi' => $cek_perubahan_urusan->deskripsi,
                    'tahun_perubahan' => $cek_perubahan_urusan->tahun_perubahan,
                ];
            } else {
                $urusans[] = [
                    'id' => $get_urusan->id,
                    'kode' => $get_urusan->kode,
                    'deskripsi' => $get_urusan->deskripsi,
                    'tahun_perubahan' => $get_urusan->tahun_perubahan,
                ];
            }
        }
    @endphp
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Nomenklatur</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Nomenklatur</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->
        <!-- Responsive Tabs Start -->
        <section class="scroll-section" id="responsiveTabs">
            <div class="card mb-3">
                <div class="card-header border-0 pb-0">
                    <ul class="nav nav-tabs nav-tabs-line card-header-tabs responsive-tabs" role="tablist">
                        {{-- Urusan Start --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#urusan" role="tab" type="button" aria-selected="true">
                            Urusan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#program" role="tab" type="button" aria-selected="false">
                                Program
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#kegiatan" role="tab" type="button" aria-selected="false">
                                Kegiatan
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Urusan Start --}}
                            <div class="tab-pane fade" id="urusan" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12" style="text-align: right">
                                        <button class="btn btn-outline-primary waves-effect waves-light mr-2" id="urusan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditUrusanModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                                        <a class="btn btn-outline-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_urusan.xlsx') }}" title="Download Template Import Data"><i class="fas fa-file-excel"></i></a>
                                        <button class="btn btn-outline-info waves-effect waves-light" title="Import Data" id="urusan_btn_impor_template" type="button"><i class="fas fa-file-import"></i></button>
                                    </div>
                                </div>

                                <div class="data-table-rows slim">
                                    <!-- Table Start -->
                                    <div class="data-table-responsive-wrapper">
                                        <table id="urusan_table" class="data-table w-100">
                                            <thead>
                                                <tr>
                                                    <th class="text-muted text-small text-uppercase" width="15%">No</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Kode</th>
                                                    <th class="text-muted text-small text-uppercase" width="40%">Deskripsi</th>
                                                    <th class="text-muted text-small text-uppercase" widht="15%">Tahun Perubahan</th>
                                                    <th class="text-muted text-small text-uppercase" width="15%">Aksi</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <!-- Table End -->
                                </div>
                            </div>
                        {{-- Urusan End --}}

                        {{-- Program Start --}}
                        <div class="tab-pane fade" id="program" role="tabpanel">
                            <div class="data-table-rows slim">
                                <div class="data-table-responsive-wrapper">
                                    <table class="table table-condensed table-striped">
                                        <thead>
                                            <tr>
                                                <th width="15%">Kode</th>
                                                <th width="50%">Urusan</th>
                                                <th width="15%">Tahun Perubahan</th>
                                                <th width="20%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($urusans as $urusan)
                                                @php
                                                    $get_programs = Program::where('urusan_id', $urusan['id'])->get();
                                                    $programs = [];
                                                    foreach ($get_programs as $get_program) {
                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                        if($cek_perubahan_program)
                                                        {
                                                            $programs[] = [
                                                                'id' => $cek_perubahan_program->program_id,
                                                                'kode' => $cek_perubahan_program->kode,
                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan,
                                                                'status_aturan' => $cek_perubahan_program->status_aturan,
                                                            ];
                                                        } else {
                                                            $programs[] = [
                                                                'id' => $get_program->id,
                                                                'kode' => $get_program->kode,
                                                                'deskripsi' => $get_program->deskripsi,
                                                                'tahun_perubahan' => $get_program->tahun_perubahan,
                                                                'status_aturan' => $get_program->status_aturan,
                                                            ];
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['kode']}}
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['deskripsi']}}
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase">{{$urusan['kode']}} Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['tahun_perubahan']}}
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="{{$urusan['id']}}"><i class="fas fa-plus"></i></button>
                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_program.xlsx') }}" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                        <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button" data-urusan-id="{{$urusan['id']}}"><i class="fas fa-file-import"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="hiddenRow">
                                                        <div class="accordian-body collapse" id="urusan{{$urusan['id']}}">
                                                            <table class="table table-striped">
                                                                {{-- <thead>
                                                                    <tr>
                                                                        <th width="15%">Kode</th>
                                                                        <th width="50%">Program</th>
                                                                        <th width="15%">Tahun Perubahan</th>
                                                                        <th width="20%">Aksi</th>
                                                                    </tr>
                                                                </thead> --}}
                                                                <tbody>
                                                                    @foreach ($programs as $program)
                                                                        <tr>
                                                                            <td width="15%">{{$program['kode']}}</td>
                                                                            <td width="50%">
                                                                                {{$program['deskripsi']}}
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">{{$urusan['kode']}} Urusan</span>
                                                                                <span class="badge bg-warning text-uppercase">{{$program['kode']}} Program</span>
                                                                            </td>
                                                                            <td width="15%"> {{$program['tahun_perubahan']}}</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-program" data-program-id="{{$program['id']}}" type="button" title="Detail Program"><i class="fas fa-eye"></i></button>
                                                                                <button class="btn btn-icon btn-warning waves-effect waves-light edit-program" data-program-id="{{$program['id']}}" data-urusan-id="{{$urusan['id']}}" type="button" title="Edit Program"><i class="fas fa-edit"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- Program End --}}

                        {{-- Kegiatan Start --}}
                        <div class="tab-pane fade active show" id="kegiatan" role="tabpanel">
                            <div class="data-table-rows slim">
                                <div class="data-table-responsive-wrapper">
                                    <table class="table table-condensed table-striped">
                                        <thead>
                                            <tr>
                                                <th width="15%">Kode</th>
                                                <th width="70%">Urusan</th>
                                                <th width="15%">Tahun Perubahan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($urusans as $urusan)
                                                @php
                                                    $get_programs = Program::where('urusan_id', $urusan['id'])->get();
                                                    $programs = [];
                                                    foreach ($get_programs as $get_program) {
                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program->id)->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                        if($cek_perubahan_program)
                                                        {
                                                            $programs[] = [
                                                                'id' => $cek_perubahan_program->program_id,
                                                                'kode' => $cek_perubahan_program->kode,
                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                'tahun_perubahan' => $cek_perubahan_program->tahun_perubahan,
                                                                'status_aturan' => $cek_perubahan_program->status_aturan,
                                                            ];
                                                        } else {
                                                            $programs[] = [
                                                                'id' => $get_program->id,
                                                                'kode' => $get_program->kode,
                                                                'deskripsi' => $get_program->deskripsi,
                                                                'tahun_perubahan' => $get_program->tahun_perubahan,
                                                                'status_aturan' => $get_program->status_aturan,
                                                            ];
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['kode']}}
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['deskripsi']}}
                                                        <br>
                                                        <span class="badge bg-primary text-uppercase">{{$urusan['kode']}} Urusan</span>
                                                    </td>
                                                    <td data-bs-toggle="collapse" data-bs-target="#urusan{{$urusan['id']}}" class="accordion-toggle">
                                                        {{$urusan['tahun_perubahan']}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="hiddenRow">
                                                        <div class="accordian-body collapse" id="urusan{{$urusan['id']}}">
                                                            <table class="table table-striped">
                                                                {{-- <thead>
                                                                    <tr>
                                                                        <th width="15%">Kode</th>
                                                                        <th width="50%">Program</th>
                                                                        <th width="15%">Tahun Perubahan</th>
                                                                        <th width="20%">Aksi</th>
                                                                    </tr>
                                                                </thead> --}}
                                                                <tbody>
                                                                    @php
                                                                        $a = 1;
                                                                    @endphp
                                                                    @foreach ($programs as $program)
                                                                        @php
                                                                            $id_a = $a++;
                                                                        @endphp
                                                                        <tr data-bs-toggle="collapse" data-bs-target="#demo{{$id_a}}" class="accordion-toggle">
                                                                            <td width="15%">{{$program['kode']}}</td>
                                                                            <td width="50%">
                                                                                {{$program['deskripsi']}}
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">{{$urusan['kode']}} Urusan</span>
                                                                                <span class="badge bg-warning text-uppercase">{{$program['kode']}} Program</span>
                                                                            </td>
                                                                            <td width="15%"> {{$program['tahun_perubahan']}}</td>
                                                                            <td width="20%">
                                                                                <button class="btn btn-primary waves-effect waves-light mr-2 program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program" data-urusan-id="{{$urusan['id']}}"><i class="fas fa-plus"></i></button>
                                                                                <a class="btn btn-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_program.xlsx') }}" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                                                <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button" data-urusan-id="{{$urusan['id']}}"><i class="fas fa-file-import"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="12" class="hiddenRow">
                                                                                <div class="accordian-body collapse" id="demo{{$id_a}}">
                                                                                    <table class="table table-striped">
                                                                                    <thead>
                                                                                        <tr class="info">
                                                                                        <th>Job</th>
                                                                                        <th>Company</th>
                                                                                        <th>Salary</th>
                                                                                        <th>Date On</th>
                                                                                        <th>Date off</th>
                                                                                        <th>Action</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td data-bs-toggle="collapse" data-bs-target="#demo10" class="accordion-toggle">
                                                                                                <a href="#">Enginner Software</a>
                                                                                            </td>
                                                                                            <td>Google</td>
                                                                                            <td>U$8.00000 </td>
                                                                                            <td> 2016/09/27</td>
                                                                                            <td> 2017/09/27</td>
                                                                                            <td>
                                                                                                <a href="#" class="btn btn-default btn-sm">
                                                                                                    <i class="fas fa-edit text-dark"></i>
                                                                                                </a>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- Kegiatan End --}}
                    </div>
                </div>
            </div>
        </section>
        <!-- Responsive Tabs with Line Title End -->
    </div>
    {{-- Modal Urusan Start--}}
    <div class="modal fade" id="addEditUrusanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="urusan_form_result"></span>
                    <form id="urusan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="urusan_kode" id="urusan_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="urusan_deskripsi" id="urusan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="urusan_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="urusan_tahun_perubahan" id="urusan_tahun_perubahan" class="form-control" required>
                                    <option value="">--- Pilih Tahun Perubahan ---</option>
                                    @foreach ($tahuns as $tahun)
                                        <option value="{{$tahun}}">{{$tahun}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="urusan_aksi" id="urusan_aksi" value="Save">
                    <input type="hidden" name="urusan_hidden_id" id="urusan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="urusan_aksi_button" id="urusan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailUrusanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Detail Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="urusan_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="urusan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Tahun Perubahan</label>
                                <input type="text" class="form-control" id="urusan_detail_tahun_perubahan" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Urusan</label>
                                <div id="div_pivot_perubahan_urusan"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Oke</button>
                </div>
            </div>
        </div>
    </div>

    <div id="importUrusanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importUrusanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.urusan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_urusan" name="impor_urusan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Urusan End --}}

    {{-- Modal Program Start  --}}
    <div class="modal fade" id="addEditProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="program_form_result"></span>
                    <form id="program_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="program_urusan_id" id="program_urusan_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="program_kode" id="program_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="program_deskripsi" id="program_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="program_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="program_tahun_perubahan" id="program_tahun_perubahan" class="form-control" required>
                                    <option value="">--- Pilih Tahun Perubahan ---</option>
                                    @foreach ($tahuns as $tahun)
                                        <option value="{{$tahun}}">{{$tahun}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="program_aksi" id="program_aksi" value="Save">
                    <input type="hidden" name="program_hidden_id" id="program_hidden_id">
                    <button type="submit" class="btn btn-primary" name="program_aksi_button" id="program_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="importProgramModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.program.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="program_impor_urusan_id" id="program_impor_urusan_id">
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_program" name="impor_program" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Detail Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Urusan</label>
                                <textarea id="program_detail_urusan" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="program_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="program_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Perubahan</label>
                                <input id="program_detail_tahun_perubahan" type="text" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Program</label>
                                <div id="div_pivot_perubahan_program" class="scrollBarPagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Oke</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Program End --}}
@endsection

@section('js')
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/bootstrap-submenu.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/cs/responsivetab.js') }}"></script>
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
    // Urusan Start
    $(document).ready(function(){
        $('.dropify').dropify();
        $('.dropify-wrapper').css('line-height', '3rem');

        var dataTables = $('#urusan_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.urusan.index') }}",
            },
            columns:[
                {
                    data: 'DT_RowIndex'
                },
                {
                    data: 'kode',
                    name: 'kode'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'tahun_perubahan',
                    name: 'tahun_perubahan'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false
                },
            ]
        });
    });
    $(document).on('click', '.urusan_detail', function(){
        var id = $(this).attr('id');
        $.ajax({
            url: "{{ url('/admin/urusan/detail') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#pivot_perubahan_urusan').remove();
                $('#div_pivot_perubahan_urusan').append('<div id="pivot_perubahan_urusan"></div>');
                $('#detail-title').text('Detail Data');
                $('#urusan_detail_kode').val(data.result.kode);
                $('#urusan_detail_deskripsi').val(data.result.deskripsi);
                $('#urusan_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                $('#pivot_perubahan_urusan').append(data.result.pivot_perubahan_urusan);
                $('#detailUrusanModal').modal('show');
            }
        });
    });
    $('#urusan_create').click(function(){
        $('#urusan_form')[0].reset();
        $('#urusan_aksi_button').text('Save');
        $('#urusan_aksi_button').prop('disabled', false);
        $('.modal-title').text('Add Data Urusan');
        $('#urusan_aksi_button').val('Save');
        $('#urusan_aksi').val('Save');
        $('#urusan_form_result').html('');
    });
    $('#urusan_form').on('submit', function(e){
        e.preventDefault();
        if($('#urusan_aksi').val() == 'Save')
        {
            $.ajax({
                url: "{{ route('admin.urusan.store') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#urusan_aksi_button').text('Menyimpan...');
                    $('#urusan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#urusan_aksi_button').prop('disabled', false);
                        $('#urusan_form')[0].reset();
                        $('#urusan_aksi_button').text('Save');
                        $('#urusan_table').DataTable().ajax.reload();
                    }
                    if(data.success)
                    {
                        html = '<div class="alert alert-success">'+data.success+'</div>';
                        $('#urusan_aksi_button').prop('disabled', false);
                        $('#urusan_form')[0].reset();
                        $('#urusan_aksi_button').text('Save');
                        $('#urusan_table').DataTable().ajax.reload();
                    }

                    $('#urusan_form_result').html(html);
                }
            });
        }
        if($('#urusan_aksi').val() == 'Edit')
        {
            $.ajax({
                url: "{{ route('admin.urusan.update') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(){
                    $('#urusan_aksi_button').text('Mengubah...');
                    $('#urusan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#urusan_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        // html = '<div class="alert alert-success">'+ data.success +'</div>';
                        $('#urusan_form')[0].reset();
                        $('#urusan_aksi_button').prop('disabled', false);
                        $('#urusan_aksi_button').text('Save');
                        $('#urusan_table').DataTable().ajax.reload();
                        $('#addEditUrusanModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil di ubah',
                            showConfirmButton: true
                        });
                    }

                    $('#urusan_form_result').html(html);
                }
            });
        }
    });
    $(document).on('click', '.urusan_edit', function(){
        var id = $(this).attr('id');
        $('#urusan_form_result').html('');
        $.ajax({
            url: "{{ url('/admin/urusan/edit') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#urusan_kode').val(data.result.kode);
                $('#urusan_deskripsi').val(data.result.deskripsi);
                $("[name='urusan_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                $('#urusan_hidden_id').val(id);
                $('.modal-title').text('Edit Data');
                $('#urusan_aksi_button').text('Edit');
                $('#urusan_aksi_button').prop('disabled', false);
                $('#urusan_aksi_button').val('Edit');
                $('#urusan_aksi').val('Edit');
                $('#addEditUrusanModal').modal('show');
            }
        });
    });

    $(document).on('click', '.urusan_delete',function(){
        var id = $(this).attr('id');
        return new swal({
            title: "Apakah Anda Yakin Menghapus Ini? Menghapus data ini akan menghapus data yang lain!!!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#1976D2",
            confirmButtonText: "Ya"
        }).then((result)=>{
            if(result.value)
            {
                $.ajax({
                    url: "{{ url('/admin/urusan/destroy') }}" + '/' + id,
                    dataType: "json",
                    beforeSend: function()
                    {
                        return new swal({
                            title: "Checking...",
                            text: "Harap Menunggu",
                            imageUrl: "{{ asset('/images/preloader.gif') }}",
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                    },
                    success: function(data)
                    {
                        if(data.errors)
                        {
                            Swal.fire({
                                icon: 'errors',
                                title: data.errors,
                                showConfirmButton: true
                            });
                        }
                        if(data.success)
                        {
                            $('#urusan_table').DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                        }
                    }
                });
            }
        });
    });

    $('#urusan_btn_impor_template').click(function(){
        $('#importUrusanModal').modal('show');
    });

    // Urusan End

    // Program Start
    $('.program_create').click(function(){
        $('#program_urusan_id').val($(this).attr('data-urusan-id'));
        $('#program_form')[0].reset();
        $('#program_aksi_button').text('Save');
        $('#program_aksi_button').prop('disabled', false);
        $('.modal-title').text('Add Data Program');
        $('#program_aksi_button').val('Save');
        $('#program_aksi').val('Save');
        $('#program_form_result').html('');
    });

    $('#program_form').on('submit', function(e){
        e.preventDefault();
        if($('#program_aksi').val() == 'Save')
        {
            $.ajax({
                url: "{{ route('admin.program.store') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#program_aksi_button').text('Menyimpan...');
                    $('#program_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#program_aksi_button').prop('disabled', false);
                        $('#program_form')[0].reset();
                        $('#program_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#program_form_result').html(html);
                }
            });
        }

        if($('#program_aksi').val() == 'Edit')
        {
            $.ajax({
                url: "{{ route('admin.program.update') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#program_aksi_button').text('Menyimpan...');
                    $('#program_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#program_aksi_button').prop('disabled', false);
                        $('#program_form')[0].reset();
                        $('#program_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#program_form_result').html(html);
                }
            });
        }
    });

    $('.program_btn_impor_template').click(function(){
        $('#program_impor_urusan_id').val($(this).attr('data-urusan-id'));
        $('#importProgramModal').modal('show');
    });

    $(document).on('click', '.detail-program', function(){
        var id = $(this).attr('data-program-id');
        $.ajax({
            url: "{{ url('/admin/program/detail') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#pivot_perubahan_program').remove();
                $('#div_pivot_perubahan_program').append('<div id="pivot_perubahan_program"></div>');
                $('#detail-title').text('Detail Data');
                $('#program_detail_urusan').val(data.result.urusan);
                $('#program_detail_kode').val(data.result.kode);
                $('#program_detail_deskripsi').val(data.result.deskripsi);
                $('#pivot_perubahan_program').append(data.result.pivot_perubahan_program);
                $('#program_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                $('#detailProgramModal').modal('show');
            }
        });
    });

    $(document).on('click', '.edit-program', function(){
        var id = $(this).attr('data-program-id');
        $('#program_urusan_id').val($(this).attr('data-urusan-id'));
        $('#form_result').html('');
        $.ajax({
            url: "{{ url('/admin/program/edit') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#program_kode').val(data.result.kode);
                $('#program_deskripsi').val(data.result.deskripsi);
                $("[name='program_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                $('#program_hidden_id').val(id);
                $('.modal-title').text('Edit Data');
                $('#program_aksi_button').text('Edit');
                $('#program_aksi_button').prop('disabled', false);
                $('#program_aksi_button').val('Edit');
                $('#program_aksi').val('Edit');
                $('#addEditProgramModal').modal('show');
            }
        });
    });
    // Program End
</script>
@endsection
