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
        /* .accordion-toggle:after {
            font-family: 'FontAwesome';
            content: "\f068";
            float: right;
        }
        .accordion-toggle.collapsed:after {
            content: "\f067";
        } */
    </style>
@endsection

@section('content')
    @php
        use App\Models\Urusan;
        use App\Models\PivotPerubahanUrusan;
        use App\Models\Program;
        use App\Models\PivotPerubahanProgram;
        use App\Models\Kegiatan;
        use App\Models\PivotPerubahanKegiatan;
        use App\Models\SubKegiatan;
        use App\Models\PivotPerubahanSubKegiatan;

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
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#urusan" role="tab" type="button" aria-selected="true">
                            Urusan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="program_tab_button" class="nav-link" data-bs-toggle="tab" data-bs-target="#program" role="tab" type="button" aria-selected="false">
                                Program
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="kegiatan_tab_button" class="nav-link" data-bs-toggle="tab" data-bs-target="#kegiatan" role="tab" type="button" aria-selected="false">
                                Kegiatan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button id="sub_kegiatan_tab_button" class="nav-link" data-bs-toggle="tab" data-bs-target="#sub_kegiatan" role="tab" type="button" aria-selected="false">
                                Sub Kegiatan
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        {{-- Urusan Start --}}
                            <div class="tab-pane fade active show" id="urusan" role="tabpanel">
                                <div class="border-0 pb-0">
                                    <ul class="nav nav-pills responsive-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active navNomenklaturUrusan" data-bs-toggle="tab" data-bs-target="#nomenklatur_urusan_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                                Semua
                                            </button>
                                        </li>
                                        @foreach ($tahuns as $tahun)
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link navNomenklaturUrusan" data-bs-toggle="tab" data-bs-target="#nomenklatur_urusan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                    {{$tahun}}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="nomenklatur_urusan_semua" role="tabpanel">
                                            <div class="row mb-3">
                                                <div class="col-12" style="text-align: right">
                                                    <button class="btn btn-outline-primary waves-effect waves-light mr-2 urusan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditUrusanModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                                                    <a class="btn btn-outline-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_urusan.xlsx') }}" title="Download Template Import Data"><i class="fas fa-file-excel"></i></a>
                                                    <button class="btn btn-outline-info waves-effect waves-light urusan_btn_impor_template" title="Import Data" type="button"><i class="fas fa-file-import"></i></button>
                                                </div>
                                            </div>

                                            <div class="data-table-rows slim">
                                                <!-- Table Start -->
                                                <div class="data-table-responsive-wrapper">
                                                    <table id="urusan_table_semua" class="data-table w-100">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 0px;">No</th>
                                                                <th style="width: 0px;">Kode</th>
                                                                <th style="width: 0px;">Deskripsi</th>
                                                                <th style="width: 0px;">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <!-- Table End -->
                                            </div>
                                        </div>
                                        @foreach ($tahuns as $tahun)
                                            <div class="tab-pane fade" id="nomenklatur_urusan_{{$tahun}}" role="tabpanel">
                                                <div class="row mb-3">
                                                    <div class="col-12" style="text-align: right">
                                                        <button class="btn btn-outline-primary waves-effect waves-light mr-2 urusan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditUrusanModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                                                        <a class="btn btn-outline-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_urusan.xlsx') }}" title="Download Template Import Data"><i class="fas fa-file-excel"></i></a>
                                                        <button class="btn btn-outline-info waves-effect waves-light urusan_btn_impor_template" title="Import Data" type="button"><i class="fas fa-file-import"></i></button>
                                                    </div>
                                                </div>

                                                <div class="data-table-rows slim">
                                                    <!-- Table Start -->
                                                    <div class="data-table-responsive-wrapper">
                                                        <table id="urusan_table_{{$tahun}}" class="data-table w-100">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width: 0px;">No</th>
                                                                    <th style="width: 0px;">Kode</th>
                                                                    <th style="width: 0px;">Deskripsi</th>
                                                                    <th style="width: 0px;">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                    <!-- Table End -->
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        {{-- Urusan End --}}

                        {{-- Program Start --}}
                        <div class="tab-pane fade" id="program" role="tabpanel">
                            <div class="border-0 pb-0">
                                <ul class="nav nav-pills responsive-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active navNomenklaturProgram" data-bs-toggle="tab" data-bs-target="#nomenklatur_program_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                            Semua
                                        </button>
                                    </li>
                                    @foreach ($tahuns as $tahun)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link navNomenklaturProgram" data-bs-toggle="tab" data-bs-target="#nomenklatur_program_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                {{$tahun}}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="nomenklatur_program_semua" role="tabpanel">
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h2 class="small-title">Filter Data</h2>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Urusan</label>
                                                    <select id="program_filter_urusan_semua" class="form-control program_filter_urusan" data-tahun="semua">
                                                        <option value="">--- Pilih Urusan ---</option>
                                                        @foreach ($urusans as $urusan)
                                                            <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Program</label>
                                                    <select id="program_filter_program_semua" class="form-control program_filter_program" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Program ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="" class="form-label">Aksi Filter</label>
                                                <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                    <button class="btn btn-primary waves-effect waves-light mr-1 program_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                    <button class="btn btn-secondary waves-effect waves-light program_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                                                    <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                                                </div>
                                            </div>
                                            <div class="col-6" style="text-align: right">
                                                <label for="" class="control-label mr-3">Impor Program: </label>
                                                <a class="btn btn-success waves-effect waves-light mr-1" href="{{asset('template/template_impor_program.xlsx')}}" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button"><i class="fas fa-file-import"></i></button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="programDivsemua"></div>
                                    </div>
                                    @foreach ($tahuns as $tahun)
                                        <div class="tab-pane fade" id="nomenklatur_program_{{$tahun}}" role="tabpanel">
                                            <div class="row mb-2">
                                                <div class="col-12">
                                                    <h2 class="small-title">Filter Data</h2>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Urusan</label>
                                                        <select id="program_filter_urusan_{{$tahun}}" class="form-control program_filter_urusan" data-tahun="{{$tahun}}">
                                                            <option value="">--- Pilih Urusan ---</option>
                                                            @foreach ($urusans as $urusan)
                                                                <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Program</label>
                                                        <select id="program_filter_program_{{$tahun}}" class="form-control program_filter_program" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Program ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <label for="" class="form-label">Aksi Filter</label>
                                                    <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                        <button class="btn btn-primary waves-effect waves-light mr-1 program_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                        <button class="btn btn-secondary waves-effect waves-light program_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                                                        <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                                                    </div>
                                                </div>
                                                <div class="col-6" style="text-align: right">
                                                    <label for="" class="control-label mr-3">Impor Program: </label>
                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_program.xlsx')}}" title="Download Template Import Data Program"><i class="fas fa-file-excel"></i></a>
                                                    <button class="btn btn-info waves-effect waves-light program_btn_impor_template" title="Import Data Program" type="button"><i class="fas fa-file-import"></i></button>
                                                </div>
                                            </div>
                                            <hr>
                                            <div id="programDiv{{$tahun}}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- Program End --}}

                        {{-- Kegiatan Start --}}
                        <div class="tab-pane fade" id="kegiatan" role="tabpanel">
                            <div class="border-0 pb-0">
                                <ul class="nav nav-pills responsive-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active navNomenklaturKegiatan" data-bs-toggle="tab" data-bs-target="#nomenklatur_kegiatan_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                            Semua
                                        </button>
                                    </li>
                                    @foreach ($tahuns as $tahun)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link navNomenklaturKegiatan" data-bs-toggle="tab" data-bs-target="#nomenklatur_kegiatan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                {{$tahun}}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="nomenklatur_kegiatan_semua" role="tabpanel">
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h2 class="small-title">Filter Data</h2>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Urusan</label>
                                                    <select id="kegiatan_filter_urusan_semua" class="form-control kegiatan_filter_urusan" data-tahun="semua">
                                                        <option value="">--- Pilih Urusan ---</option>
                                                        @foreach ($urusans as $urusan)
                                                            <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Program</label>
                                                    <select id="kegiatan_filter_program_semua" class="form-control kegiatan_filter_program" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Program ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Kegiatan</label>
                                                    <select id="kegiatan_filter_kegiatan_semua" class="form-control kegiatan_filter_kegiatan" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Kegiatan ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="" class="form-label">Aksi Filter</label>
                                                <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                    <button class="btn btn-primary waves-effect waves-light mr-1 kegiatan_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                    <button class="btn btn-secondary waves-effect waves-light kegiatan_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="onOffTaggingKegiatan" checked>
                                                    <label class="form-check-label" for="onOffTaggingKegiatan">On / Off Tagging</label>
                                                </div>
                                            </div>
                                            <div class="col-6" style="text-align: right;">
                                                <label for="" class="form-label">Impor Data:</label>
                                                <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_kegiatan.xlsx')}}" title="Download Template Import Data Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                <button class="btn btn-info waves-effect waves-light kegiatan_btn_impor_template" title="Import Data Kegiatan" type="button"><i class="fas fa-file-import"></i></button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="kegiatanDivsemua"></div>
                                    </div>
                                    @foreach ($tahuns as $tahun)
                                        <div class="tab-pane fade" id="nomenklatur_kegiatan_{{$tahun}}" role="tabpanel">
                                            <div class="row mb-2">
                                                <div class="col-12">
                                                    <h2 class="small-title">Filter Data</h2>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Urusan</label>
                                                        <select id="kegiatan_filter_urusan_{{$tahun}}" class="form-control kegiatan_filter_urusan" data-tahun="{{$tahun}}">
                                                            <option value="">--- Pilih Urusan ---</option>
                                                            @foreach ($urusans as $urusan)
                                                                <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Program</label>
                                                        <select id="kegiatan_filter_program_{{$tahun}}" class="form-control kegiatan_filter_program" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Program ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Kegiatan</label>
                                                        <select id="kegiatan_filter_kegiatan_{{$tahun}}" class="form-control kegiatan_filter_kegiatan" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Kegiatan ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <label for="" class="form-label">Aksi Filter</label>
                                                    <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                        <button class="btn btn-primary waves-effect waves-light mr-1 kegiatan_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                        <button class="btn btn-secondary waves-effect waves-light kegiatan_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="onOffTaggingKegiatan" checked>
                                                        <label class="form-check-label" for="onOffTaggingKegiatan">On / Off Tagging</label>
                                                    </div>
                                                </div>
                                                <div class="col-6" style="text-align: right;">
                                                    <label for="" class="form-label">Impor Data:</label>
                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_kegiatan.xlsx')}}" title="Download Template Import Data Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                    <button class="btn btn-info waves-effect waves-light kegiatan_btn_impor_template" title="Import Data Kegiatan" type="button"><i class="fas fa-file-import"></i></button>
                                                </div>
                                            </div>
                                            <hr>
                                            <div id="kegiatanDiv{{$tahun}}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- Kegiatan End --}}

                        {{-- Sub Kegiatan Start --}}
                        <div class="tab-pane fade" id="sub_kegiatan" role="tabpanel">
                            <div class="border-0 pb-0">
                                <ul class="nav nav-pills responsive-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active navNomenklaturSubKegiatan" data-bs-toggle="tab" data-bs-target="#nomenklatur_sub_kegiatan_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                            Semua
                                        </button>
                                    </li>
                                    @foreach ($tahuns as $tahun)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link navNomenklaturSubKegiatan" data-bs-toggle="tab" data-bs-target="#nomenklatur_sub_kegiatan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                {{$tahun}}
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="nomenklatur_sub_kegiatan_semua" role="tabpanel">
                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <h2 class="small-title">Filter Data</h2>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Urusan</label>
                                                    <select id="sub_kegiatan_filter_urusan_semua" class="form-control sub_kegiatan_filter_urusan" data-tahun="semua">
                                                        <option value="">--- Pilih Urusan ---</option>
                                                        @foreach ($urusans as $urusan)
                                                            <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Program</label>
                                                    <select id="sub_kegiatan_filter_program_semua" class="form-control sub_kegiatan_filter_program" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Program ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Kegiatan</label>
                                                    <select id="sub_kegiatan_filter_kegiatan_semua" class="form-control sub_kegiatan_filter_kegiatan" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Kegiatan ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group position-relative mb-3">
                                                    <label for="" class="form-label">Sub Kegiatan</label>
                                                    <select id="sub_kegiatan_filter_sub_kegiatan_semua" class="form-control sub_kegiatan_filter_sub_kegiatan" data-tahun="semua" disabled>
                                                        <option value="">--- Pilih Sub Kegiatan ---</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="" class="form-label">Aksi Filter</label>
                                                <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                    <button class="btn btn-primary waves-effect waves-light mr-1 sub_kegiatan_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                    <button class="btn btn-secondary waves-effect waves-light sub_kegiatan_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="onOffTaggingSubKegiatan" checked>
                                                    <label class="form-check-label" for="onOffTaggingSubKegiatan">On / Off Tagging</label>
                                                </div>
                                            </div>
                                            <div class="col-6" style="text-align: right">
                                                <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_sub_kegiatan.xlsx')}}" title="Download Template Import Data Sub Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                <button class="btn btn-info waves-effect waves-light sub_kegiatan_btn_impor_template" title="Import Data Sub Kegiatan" type="button"><i class="fas fa-file-import"></i></button>
                                            </div>
                                        </div>
                                        <hr>
                                        <div id="subKegiatanDivsemua"></div>
                                    </div>
                                    @foreach ($tahuns as $tahun)
                                        <div class="tab-pane fade" id="nomenklatur_sub_kegiatan_{{$tahun}}" role="tabpanel">
                                            <div class="row mb-2">
                                                <div class="col-12">
                                                    <h2 class="small-title">Filter Data</h2>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Urusan</label>
                                                        <select id="sub_kegiatan_filter_urusan_{{$tahun}}" class="form-control sub_kegiatan_filter_urusan" data-tahun="{{$tahun}}">
                                                            <option value="">--- Pilih Urusan ---</option>
                                                            @foreach ($urusans as $urusan)
                                                                <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Program</label>
                                                        <select id="sub_kegiatan_filter_program_{{$tahun}}" class="form-control sub_kegiatan_filter_program" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Program ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Kegiatan</label>
                                                        <select id="sub_kegiatan_filter_kegiatan_{{$tahun}}" class="form-control sub_kegiatan_filter_kegiatan" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Kegiatan ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group position-relative mb-3">
                                                        <label for="" class="form-label">Sub Kegiatan</label>
                                                        <select id="sub_kegiatan_filter_sub_kegiatan_{{$tahun}}" class="form-control sub_kegiatan_filter_sub_kegiatan" data-tahun="{{$tahun}}" disabled>
                                                            <option value="">--- Pilih Sub Kegiatan ---</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <label for="" class="form-label">Aksi Filter</label>
                                                    <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                        <button class="btn btn-primary waves-effect waves-light mr-1 sub_kegiatan_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                        <button class="btn btn-secondary waves-effect waves-light sub_kegiatan_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="onOffTaggingSubKegiatan" checked>
                                                        <label class="form-check-label" for="onOffTaggingSubKegiatan">On / Off Tagging</label>
                                                    </div>
                                                </div>
                                                <div class="col-6" style="text-align: right">
                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_sub_kegiatan.xlsx')}}" title="Download Template Import Data Sub Kegiatan"><i class="fas fa-file-excel"></i></a>
                                                    <button class="btn btn-info waves-effect waves-light sub_kegiatan_btn_impor_template" title="Import Data Sub Kegiatan" type="button"><i class="fas fa-file-import"></i></button>
                                                </div>
                                            </div>
                                            <hr>
                                            <div id="subKegiatanDiv{{$tahun}}"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- Sub Kegiatan End --}}
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
                                <label for="urusan_tahun_perubahan" class="form-label">Tahun</label>
                                <select name="urusan_tahun_perubahan" id="urusan_tahun_perubahan" class="form-control" required>
                                    <option value="">--- Pilih Tahun ---</option>
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

    <div id="indikatorKinerjaProgramModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Program</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.program.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_program_program_id" id="indikator_kinerja_program_program_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <input id="indikator_kinerja_program_deskripsi" name="indikator_kinerja_program_deskripsi"/>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Program End --}}

    {{-- Modal Kegiatan Start --}}
    <div class="modal fade" id="addEditKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="kegiatan_form_result"></span>
                    <form id="kegiatan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kegiatan_program_id" id="kegiatan_program_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="kegiatan_kode" id="kegiatan_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="kegiatan_deskripsi" id="kegiatan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="kegiatan_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="kegiatan_tahun_perubahan" id="kegiatan_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="kegiatan_aksi" id="kegiatan_aksi" value="Save">
                    <input type="hidden" name="kegiatan_hidden_id" id="kegiatan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="kegiatan_aksi_button" id="kegiatan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="importKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.kegiatan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_kegiatan" name="impor_kegiatan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
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
                                <textarea id="kegiatan_detail_urusan" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Program</label>
                                <textarea id="kegiatan_detail_program" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="kegiatan_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="kegiatan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Perubahan</label>
                                <input id="kegiatan_detail_tahun_perubahan" type="text" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Kegiatan</label>
                                <div id="div_pivot_perubahan_kegiatan" class="scrollBarPagination"></div>
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
    {{-- Modal Kegiatan End --}}

    {{-- Modal Sub Kegiatan Start --}}
    <div class="modal fade" id="addEditSubKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="sub_kegiatan_form_result"></span>
                    <form id="sub_kegiatan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sub_kegiatan_kegiatan_id" id="sub_kegiatan_kegiatan_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="sub_kegiatan_kode" id="sub_kegiatan_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="sub_kegiatan_deskripsi" id="sub_kegiatan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sub_kegiatan_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="sub_kegiatan_tahun_perubahan" id="sub_kegiatan_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="sub_kegiatan_aksi" id="sub_kegiatan_aksi" value="Save">
                    <input type="hidden" name="sub_kegiatan_hidden_id" id="sub_kegiatan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="sub_kegiatan_aksi_button" id="sub_kegiatan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailSubKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
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
                                <textarea id="sub_kegiatan_detail_urusan" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Program</label>
                                <textarea id="sub_kegiatan_detail_program" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Kegiatan</label>
                                <textarea id="sub_kegiatan_detail_kegiatan" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="sub_kegiatan_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="sub_kegiatan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Perubahan</label>
                                <input id="sub_kegiatan_detail_tahun_perubahan" type="text" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan SubKegiatan</label>
                                <div id="div_pivot_perubahan_sub_kegiatan" class="scrollBarPagination"></div>
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

    <div id="importSubKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importSubKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sub-kegiatan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_sub_kegiatan" name="impor_sub_kegiatan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Sub Kegiatan End --}}
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

        var dataTables = $('#urusan_table_semua').DataTable({
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
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false
                },
            ]
        });

        $('#program_filter_urusan_semua').select2();
        $('#program_filter_program_semua').select2();

        $('#kegiatan_filter_urusan_semua').select2();
        $('#kegiatan_filter_program_semua').select2();
        $('#kegiatan_filter_kegiatan_semua').select2();

        $('#sub_kegiatan_filter_urusan_semua').select2();
        $('#sub_kegiatan_filter_program_semua').select2();
        $('#sub_kegiatan_filter_kegiatan_semua').select2();
        $('#sub_kegiatan_filter_sub_kegiatan_semua').select2();

        @foreach ($tahuns as $tahun)
            var tahun = "{{$tahun}}";

            $('#program_filter_urusan_'+tahun).select2();
            $('#program_filter_program_'+tahun).select2();

            $('#kegiatan_filter_urusan_'+tahun).select2();
            $('#kegiatan_filter_program_'+tahun).select2();
            $('#kegiatan_filter_kegiatan_'+tahun).select2();

            $('#sub_kegiatan_filter_urusan_'+tahun).select2();
            $('#sub_kegiatan_filter_program_'+tahun).select2();
            $('#sub_kegiatan_filter_kegiatan_'+tahun).select2();
            $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).select2();

            var dataTables = $('#urusan_table_'+tahun).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('/admin/urusan/get-urusan') }}" + '/' + tahun,
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
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false
                    },
                ]
            });
        @endforeach

        new Tagify(document.querySelector('#indikator_kinerja_program_deskripsi'));
    });
    $(document).on('click', '.urusan_detail', function(){
        var id = $(this).attr('id');
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/urusan/detail') }}"+'/'+id + '/' + tahun,
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
    $('.urusan_create').click(function(){
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
        var tahun = $(this).attr('data-tahun');
        $('#urusan_form_result').html('');
        $.ajax({
            url: "{{ url('/admin/urusan/edit') }}"+'/'+id+'/'+tahun,
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

    $('.urusan_btn_impor_template').click(function(){
        $('#importUrusanModal').modal('show');
    });

    // Urusan End

    // Program Start
    $('#program_tab_button').click(function(){
        $.ajax({
            url: "{{ route('admin.nomenklatur.get-program') }}",
            dataType: "json",
            success: function(data)
            {
                $('#programDivsemua').html(data.html);
            }
        });
    });

    $('.navNomenklaturProgram').click(function(){
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/nomenklatur/get-program') }}" + '/' + tahun,
            dataType: "json",
            success: function(data)
            {
                $('#programDiv'+tahun).html(data.html);
            }
        });
    });

    $(document).on('click', '.program_create',function(){
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

    $(document).on('click','.program_btn_impor_template',function(){
        $('#importProgramModal').modal('show');
    });

    $(document).on('click', '.detail-program', function(){
        var id = $(this).attr('data-program-id');
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/program/detail') }}"+'/'+id + '/' + tahun,
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
        var tahun = $(this).attr('data-tahun');
        $('#program_urusan_id').val($(this).attr('data-urusan-id'));
        $('#form_result').html('');
        $.ajax({
            url: "{{ url('/admin/program/edit') }}"+'/'+id + '/' + tahun,
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

    $(document).on('click', '.tambah-program-indikator-kinerja', function(){
        $('#indikator_kinerja_program_program_id').val($(this).attr('data-program-id'));
        $('#indikatorKinerjaProgramModal').modal('show');
    });
    // Program End

    // Kegiatan Start
    $('#kegiatan_tab_button').click(function(){
        $.ajax({
            url: "{{ route('admin.nomenklatur.get-kegiatan') }}",
            dataType: "json",
            success: function(data)
            {
                $('#kegiatanDivsemua').html(data.html);
            }
        });
    });

    $('.navNomenklaturKegiatan').click(function(){
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/nomenklatur/get-kegiatan') }}" + '/' + tahun,
            dataType: "json",
            success: function(data)
            {
                $('#kegiatanDiv'+tahun).html(data.html);
            }
        });
    });

    $(document).on('click', '.kegiatan_create',function(){
        $('#kegiatan_program_id').val($(this).attr('data-program-id'));
        $('#kegiatan_form')[0].reset();
        $('#kegiatan_aksi_button').text('Save');
        $('#kegiatan_aksi_button').prop('disabled', false);
        $('.modal-title').text('Add Data Kegiatan');
        $('#kegiatan_aksi_button').val('Save');
        $('#kegiatan_aksi').val('Save');
        $('#kegiatan_form_result').html('');
    });

    $('#kegiatan_form').on('submit', function(e){
        e.preventDefault();
        if($('#kegiatan_aksi').val() == 'Save')
        {
            $.ajax({
                url: "{{ route('admin.kegiatan.store') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#kegiatan_aksi_button').text('Menyimpan...');
                    $('#kegiatan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#kegiatan_aksi_button').prop('disabled', false);
                        $('#kegiatan_form')[0].reset();
                        $('#kegiatan_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#kegiatan_form_result').html(html);
                }
            });
        }

        if($('#kegiatan_aksi').val() == 'Edit')
        {
            $.ajax({
                url: "{{ route('admin.kegiatan.update') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#kegiatan_aksi_button').text('Menyimpan...');
                    $('#kegiatan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#kegiatan_aksi_button').prop('disabled', false);
                        $('#kegiatan_aksi_button').text('Edit');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#kegiatan_form_result').html(html);
                }
            });
        }
    });

    $(document).on('click', '.detail-kegiatan', function(){
        var id = $(this).attr('data-kegiatan-id');
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/kegiatan/detail') }}"+'/'+id+'/'+tahun,
            dataType: "json",
            success: function(data)
            {
                $('#pivot_perubahan_kegiatan').remove();
                $('#div_pivot_perubahan_kegiatan').append('<div id="pivot_perubahan_kegiatan"></div>');
                $('#detail-title').text('Detail Data');
                $('#kegiatan_detail_urusan').val(data.result.urusan);
                $('#kegiatan_detail_program').val(data.result.program);
                $('#kegiatan_detail_kode').val(data.result.kode);
                $('#kegiatan_detail_deskripsi').val(data.result.deskripsi);
                $('#pivot_perubahan_kegiatan').append(data.result.pivot_perubahan_kegiatan);
                $('#kegiatan_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                $('#detailKegiatanModal').modal('show');
            }
        });
    });

    $(document).on('click', '.edit-kegiatan', function(){
        var id = $(this).attr('data-kegiatan-id');
        var tahun = $(this).attr('data-tahun');
        $('#kegiatan_program_id').val($(this).attr('data-program-id'));
        $('#form_result').html('');
        $.ajax({
            url: "{{ url('/admin/kegiatan/edit') }}"+'/'+id + '/' + tahun,
            dataType: "json",
            success: function(data)
            {
                $('#kegiatan_kode').val(data.result.kode);
                $('#kegiatan_deskripsi').val(data.result.deskripsi);
                $("[name='kegiatan_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                $('#kegiatan_hidden_id').val(id);
                $('.modal-title').text('Edit Data');
                $('#kegiatan_aksi_button').text('Edit');
                $('#kegiatan_aksi_button').prop('disabled', false);
                $('#kegiatan_aksi_button').val('Edit');
                $('#kegiatan_aksi').val('Edit');
                $('#addEditKegiatanModal').modal('show');
            }
        });
    });

    $(document).on('click', '.kegiatan_btn_impor_template',function(){
        $('#importKegiatanModal').modal('show');
    });
    // Kegiatan End

    // Sub Kegiatan Start
    $('#sub_kegiatan_tab_button').click(function(){
        $.ajax({
            url: "{{ route('admin.nomenklatur.get-sub-kegiatan') }}",
            dataType: "json",
            success: function(data)
            {
                $('#subKegiatanDivsemua').html(data.html);
            }
        });
    });

    $('.navNomenklaturSubKegiatan').click(function(){
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ url('/admin/nomenklatur/get-sub-kegiatan') }}" + '/' + tahun,
            dataType: "json",
            success: function(data)
            {
                $('#subKegiatanDiv'+tahun).html(data.html);
            }
        });
    });

    $(document).on('click','.sub_kegiatan_create',function(){
        $('#sub_kegiatan_kegiatan_id').val($(this).attr('data-kegiatan-id'));
        $('#sub_kegiatan_form')[0].reset();
        $('#sub_kegiatan_aksi_button').text('Save');
        $('#sub_kegiatan_aksi_button').prop('disabled', false);
        $('.modal-title').text('Add Data Sub Kegiatan');
        $('#sub_kegiatan_aksi_button').val('Save');
        $('#sub_kegiatan_aksi').val('Save');
        $('#sub_kegiatan_form_result').html('');
    });

    $('#sub_kegiatan_form').on('submit', function(e){
        e.preventDefault();
        if($('#sub_kegiatan_aksi').val() == 'Save')
        {
            $.ajax({
                url: "{{ route('admin.sub-kegiatan.store') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#sub_kegiatan_aksi_button').text('Menyimpan...');
                    $('#sub_kegiatan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#sub_kegiatan_aksi_button').prop('disabled', false);
                        $('#sub_kegiatan_form')[0].reset();
                        $('#sub_kegiatan_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#sub_kegiatan_form_result').html(html);
                }
            });
        }

        if($('#sub_kegiatan_aksi').val() == 'Edit')
        {
            $.ajax({
                url: "{{ route('admin.sub-kegiatan.update') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#sub_kegiatan_aksi_button').text('Menyimpan...');
                    $('#sub_kegiatan_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#sub_kegiatan_aksi_button').prop('disabled', false);
                        $('#sub_kegiatan_aksi_button').text('Edit');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#sub_kegiatan_form_result').html(html);
                }
            });
        }
    });

    $(document).on('click', '.detail-sub-kegiatan', function(){
        var id = $(this).attr('data-sub-kegiatan-id');
        $.ajax({
            url: "{{ url('/admin/sub-kegiatan/detail') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#pivot_perubahan_sub_kegiatan').remove();
                $('#div_pivot_perubahan_sub_kegiatan').append('<div id="pivot_perubahan_sub_kegiatan"></div>');
                $('#detail-title').text('Detail Data');
                $('#sub_kegiatan_detail_urusan').val(data.result.urusan);
                $('#sub_kegiatan_detail_program').val(data.result.program);
                $('#sub_kegiatan_detail_kode').val(data.result.kode);
                $('#sub_kegiatan_detail_deskripsi').val(data.result.deskripsi);
                $('#pivot_perubahan_sub_kegiatan').append(data.result.pivot_perubahan_sub_kegiatan);
                $('#sub_kegiatan_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                $('#sub_kegiatan_detail_kegiatan').val(data.result.kegiatan);
                $('#detailSubKegiatanModal').modal('show');
            }
        });
    });

    $(document).on('click', '.edit-sub-kegiatan', function(){
        var id = $(this).attr('data-sub-kegiatan-id');
        $('#sub_kegiatan_kegiatan_id').val($(this).attr('data-kegiatan-id'));
        $('#sub_kegiatan_form_result').html('');
        $.ajax({
            url: "{{ url('/admin/sub-kegiatan/edit') }}"+'/'+id,
            dataType: "json",
            success: function(data)
            {
                $('#sub_kegiatan_kode').val(data.result.kode);
                $('#sub_kegiatan_deskripsi').val(data.result.deskripsi);
                $("[name='sub_kegiatan_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                $('#sub_kegiatan_hidden_id').val(id);
                $('.modal-title').text('Edit Data');
                $('#sub_kegiatan_aksi_button').text('Edit');
                $('#sub_kegiatan_aksi_button').prop('disabled', false);
                $('#sub_kegiatan_aksi_button').val('Edit');
                $('#sub_kegiatan_aksi').val('Edit');
                $('#addEditSubKegiatanModal').modal('show');
            }
        });
    });

    $(document).on('click','.sub_kegiatan_btn_impor_template',function(){
        $('#importSubKegiatanModal').modal('show');
    });
    // Sub Kegiatan End

    // On / Off Tagging
    $(document).on('change','#onOffTaggingProgram',function(){
        if($(this).prop('checked') == true)
        {
            $('.program-tagging').show();
        } else {
            $('.program-tagging').hide();
        }
    });

    $(document).on('change','#onOffTaggingKegiatan',function(){
        if($(this).prop('checked') == true)
        {
            $('.kegiatan-tagging').show();
        } else {
            $('.kegiatan-tagging').hide();
        }
    });

    $(document).on('change','#onOffTaggingSubKegiatan',function(){
        if($(this).prop('checked') == true)
        {
            $('.sub-kegiatan-tagging').show();
        } else {
            $('.sub-kegiatan-tagging').hide();
        }
    });

    // Filter Data Program
    $('.program_filter_urusan').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun: tahun
                },
                success: function(response){
                    $('#program_filter_program_'+tahun).empty();
                    $('#program_filter_program_'+tahun).prop('disabled', false);
                    $('#program_filter_program_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                    $.each(response, function(key, value){
                        $('#program_filter_program_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#program_filter_program_'+tahun).prop('disabled', true);
        }
    });

    $('.program_btn_filter').click(function(){
        var tahun = $(this).attr('data-tahun');
        var urusan = $('#program_filter_urusan_'+tahun).val();
        var program = $('#program_filter_program_'+tahun).val();

        $.ajax({
            url: "{{ route('admin.nomenklatur.filter.program') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                urusan: urusan,
                program: program,
                tahun: tahun
            },
            success: function(data)
            {
                $('#programDiv'+tahun).html(data.html);
            }
        });
    });

    $('.program_btn_reset').click(function(){
        var tahun = $(this).attr('data-tahun');
        $('#program_filter_program_'+tahun).prop('disabled', true);
        $('#program_filter_urusan_'+tahun).val('').trigger('change');
        $('#program_filter_program_'+tahun).val('').trigger('change');
        $.ajax({
            url: "{{ route('admin.nomenklatur.reset.program') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun
            },
            success: function(data)
            {
                $('#programDiv'+tahun).html(data.html);
            }
        });
    });

    // Filter Data Kegiatan
    $('.kegiatan_filter_urusan').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun : tahun
                },
                success: function(response){
                    $('#kegiatan_filter_program_'+tahun).empty();
                    $('#kegiatan_filter_program_'+tahun).prop('disabled', false);
                    $('#kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                    $('#kegiatan_filter_program_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                    $.each(response, function(key, value){
                        $('#kegiatan_filter_program_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#kegiatan_filter_program_'+tahun).prop('disabled', true);
            $('#kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
        }
    });

    $('.kegiatan_filter_program').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun:tahun
                },
                success: function(response){
                    $('#kegiatan_filter_kegiatan_'+tahun).empty();
                    $('#kegiatan_filter_kegiatan_'+tahun).prop('disabled', false);
                    $('#kegiatan_filter_kegiatan_'+tahun).append('<option value="">--- Pilih Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#kegiatan_filter_kegiatan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
        }
    });

    $('.kegiatan_btn_filter').click(function(){
        var tahun = $(this).attr('data-tahun');
        var urusan = $('#kegiatan_filter_urusan_'+tahun).val();
        var program = $('#kegiatan_filter_program_'+tahun).val();
        var kegiatan = $('#kegiatan_filter_kegiatan_'+tahun).val();

        $.ajax({
            url: "{{ route('admin.nomenklatur.filter.kegiatan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                urusan: urusan,
                program: program,
                kegiatan: kegiatan,
                tahun: tahun
            },
            success: function(data)
            {
                $('#kegiatanDiv'+tahun).html(data.html);
            }
        });
    });

    $('.kegiatan_btn_reset').click(function(){
        var tahun = $(this).attr('data-tahun');
        $('#kegiatan_filter_program_'+tahun).prop('disabled', true);
        $('#kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
        $('#kegiatan_filter_urusan_'+tahun).val('').trigger('change');
        $('#kegiatan_filter_program_'+tahun).val('').trigger('change');
        $('#kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
        $.ajax({
            url: "{{ route('admin.nomenklatur.reset.kegiatan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun
            },
            success: function(data)
            {
                $('#kegiatanDiv'+tahun).html(data.html);
            }
        });
    });

    // Filter Data Sub Kegiatan
    $('.sub_kegiatan_filter_urusan').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun: tahun
                },
                success: function(response){
                    $('#sub_kegiatan_filter_program_'+tahun).empty();
                    $('#sub_kegiatan_filter_program_'+tahun).prop('disabled', false);
                    $('#sub_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                    $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
                    $('#sub_kegiatan_filter_program_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                    $.each(response, function(key, value){
                        $('#sub_kegiatan_filter_program_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#sub_kegiatan_filter_program_'+tahun).prop('disabled', true);
            $('#sub_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
            $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
        }
    });

    $('.sub_kegiatan_filter_program').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun: tahun
                },
                success: function(response){
                    $('#sub_kegiatan_filter_kegiatan_'+tahun).empty();
                    $('#sub_kegiatan_filter_kegiatan_'+tahun).prop('disabled', false);
                    $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
                    $('#sub_kegiatan_filter_kegiatan_'+tahun).append('<option value="">--- Pilih Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#sub_kegiatan_filter_kegiatan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#sub_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
            $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
        }
    });

    $('.sub_kegiatan_filter_kegiatan').on('change', function(){
        var tahun = $(this).attr('data-tahun');
        if($(this).val() != '')
        {
            $.ajax({
                url: "{{ route('admin.nomenklatur.filter.get-sub-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).val(),
                    tahun:tahun
                },
                success: function(response){
                    $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).empty();
                    $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', false);
                    $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).append('<option value="">--- Pilih Sub Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        } else {
            $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
        }
    });

    $('.sub_kegiatan_btn_filter').click(function(){
        var tahun = $(this).attr('data-tahun');
        var urusan = $('#sub_kegiatan_filter_urusan_'+tahun).val();
        var program = $('#sub_kegiatan_filter_program_'+tahun).val();
        var kegiatan = $('#sub_kegiatan_filter_kegiatan_'+tahun).val();
        var sub_kegiatan = $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).val();

        $.ajax({
            url: "{{ route('admin.nomenklatur.filter.sub-kegiatan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                urusan: urusan,
                program: program,
                kegiatan: kegiatan,
                sub_kegiatan: sub_kegiatan,
                tahun: tahun
            },
            success: function(data)
            {
                $('#subKegiatanDiv'+tahun).html(data.html);
            }
        });
    });

    $('.sub_kegiatan_btn_reset').click(function(){
        var tahun = $(this).attr('data-tahun');
        $('#sub_kegiatan_filter_program_'+tahun).prop('disabled', true);
        $('#sub_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
        $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).prop('disabled', true);
        $('#sub_kegiatan_filter_urusan_'+tahun).val('').trigger('change');
        $('#sub_kegiatan_filter_program_'+tahun).val('').trigger('change');
        $('#sub_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
        $('#sub_kegiatan_filter_sub_kegiatan_'+tahun).val('').trigger('change');
        $.ajax({
            url: "{{ route('admin.nomenklatur.reset.sub-kegiatan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun
            },
            success: function(data)
            {
                $('#subKegiatanDiv'+tahun).html(data.html);
            }
        });
    });
</script>
@endsection
