@extends('admin.layouts.app')
@section('title', 'Admin | Perencanaan')

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
        .box {
            float: left;
            height: 20px;
            width: 20px;
            margin-bottom: 15px;
            border: 1px solid black;
            clear: both;
        }
    </style>
@endsection

@section('content')
    @php
        use Carbon\Carbon;
        use App\Models\TahunPeriode;
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
        use App\Models\Urusan;
        use App\Models\PivotPerubahanUrusan;
        use App\Models\MasterOpd;
        use App\Models\PivotSasaranIndikatorProgramRpjmd;
        use App\Models\Program;
        use App\Models\PivotPerubahanProgram;
        use App\Models\PivotProgramKegiatanRenstra;
        use App\Models\TargetRpPertahunProgram;
        use App\Models\RenstraKegiatan;
        use App\Models\PivotPerubahanKegiatan;
        use App\Models\Kegiatan;
        use App\Models\PivotOpdRentraKegiatan;
        use App\Models\TargetRpPertahunRenstraKegiatan;
        use App\Models\SubKegiatan;
        use App\Models\PivotPerubahanSubKegiatan;
        use App\Models\TujuanIndikatorKinerja;
        use App\Models\TujuanTargetSatuanRpRealisasi;
        use App\Models\SasaranIndikatorKinerja;
        use App\Models\SasaranTargetSatuanRpRealisasi;
    @endphp
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Perencanaan</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Perencanaan</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <ul class="nav nav-tabs nav-tabs-title nav-tabs-line-title responsive-tabs" id="lineTitleTabsContainer" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#rpjmdTab" role="tab" aria-selected="true"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RPJMD</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#renstraTab" role="tab" aria-selected="false"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RENSTRA</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#rkpdTab" role="tab" aria-selected="false" id="buttonRkpdTab"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RKPD</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#renjaTab" role="tab" aria-selected="false"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RENJA</a>
            </li>
        </ul>

        <div class="card mb-5">
            <div class="card-body">
                <div class="tab-content">
                    {{-- RPJMD Start --}}
                    <div class="tab-pane fade active show" id="rpjmdTab" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#visiNav" role="tab" aria-selected="true" type="button" id="visi_tab_button">
                                        Visi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#misiNav" role="tab" aria-selected="false" type="button" id="misi_tab_button">
                                        Misi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tujuanNav" role="tab" aria-selected="false" type="button" id="tujuan_tab_button">
                                        Tujuan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sasaranNav" role="tab" aria-selected="false" type="button" id="sasaran_tab_button">
                                        Sasaran
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#programNav" role="tab" aria-selected="false" type="button" id="program_tab_button">
                                        Program
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                {{-- Visi Start --}}
                                <div class="tab-pane fade active show" id="visiNav" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRpjmdVisi" data-bs-toggle="tab" data-bs-target="#rpjmd_visi_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="rpjmd_visi_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-3">
                                                        <div class="col-12" style="text-align: right">
                                                            <button class="btn btn-outline-primary waves-effect waves-light mr-2" id="visi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditVisiModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="data-table-rows slim">
                                                        <!-- Table Start -->
                                                        <div class="table-responsive-sm">
                                                            <table id="visi_table_{{$tahun}}" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 0px;">No</th>
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
                                {{-- Visi End --}}

                                {{-- Misi Start --}}
                                <div class="tab-pane fade" id="misiNav" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active navRpjmdMisi" data-bs-toggle="tab" data-bs-target="#rpjmd_misi_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                                    Semua
                                                </button>
                                            </li>
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link navRpjmdMisi" data-bs-toggle="tab" data-bs-target="#rpjmd_misi_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="rpjmd_misi_semua" role="tabpanel">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <h2 class="small-title">Filter Data</h2>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Visi</label>
                                                            <select id="misi_filter_visi_semua" class="form-control misi_filter_visi" data-tahun="semua">
                                                                <option value="">--- Pilih Visi ---</option>
                                                                <option value="aman">Aman</option>
                                                                <option value="mandiri">Mandiri</option>
                                                                <option value="sejahtera">Sejahtera</option>
                                                                <option value="berahlak">Berahlak</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Misi</label>
                                                            <select id="misi_filter_misi_semua" class="form-control" disabled>
                                                                <option value="">--- Pilih Misi ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <label for="" class="form-label">Filter Aksi</label>
                                                        <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                            <button class="btn btn-primary waves-effect waves-light mr-1 misi_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                            <button class="btn btn-secondary waves-effect waves-light misi_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="onOffTaggingMisi" checked>
                                                            <label class="form-check-label" for="onOffTaggingMisi">On / Off Tagging</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div id="misiNavDivsemua"></div>
                                            </div>
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade" id="rpjmd_misi_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="misi_filter_visi_{{$tahun}}" class="form-control misi_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="misi_filter_misi_{{$tahun}}" class="form-control" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Filter Aksi</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 misi_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light misi_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingMisi" checked>
                                                                <label class="form-check-label" for="onOffTaggingMisi">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="misiNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Misi End --}}

                                {{-- Tujuan Start --}}
                                <div class="tab-pane fade" id="tujuanNav" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active navRpjmdTujuan" data-bs-toggle="tab" data-bs-target="#rpjmd_tujuan_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                                    Semua
                                                </button>
                                            </li>
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link navRpjmdTujuan" data-bs-toggle="tab" data-bs-target="#rpjmd_tujuan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="rpjmd_tujuan_semua" role="tabpanel">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <h2 class="small-title">Filter Data</h2>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Visi</label>
                                                            <select id="tujuan_filter_visi_semua" class="form-control tujuan_filter_visi" data-tahun="semua">
                                                                <option value="">--- Pilih Visi ---</option>
                                                                <option value="aman">Aman</option>
                                                                <option value="mandiri">Mandiri</option>
                                                                <option value="sejahtera">Sejahtera</option>
                                                                <option value="berahlak">Berahlak</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Misi</label>
                                                            <select id="tujuan_filter_misi_semua" class="form-control tujuan_filter_misi" data-tahun="semua" disabled>
                                                                <option value="">--- Pilih Misi ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Tujuan</label>
                                                            <select id="tujuan_filter_tujuan_semua" class="form-control tujuan_filter_tujuan" data-tahun="semua" disabled>
                                                                <option value="">--- Pilih Tujuan ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <label for="" class="form-label">Aksi Filter</label>
                                                        <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                            <button class="btn btn-primary waves-effect waves-light mr-1 tujuan_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                            <button class="btn btn-secondary waves-effect waves-light tujuan_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="onOffTaggingTujuan" checked>
                                                            <label class="form-check-label" for="onOffTaggingTujuan">On / Off Tagging</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6" style="text-align: right">
                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_tujuan.xlsx')}}" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                        <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" ><i class="fas fa-file-import"></i></button>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div id="tujuanNavDivsemua"></div>
                                            </div>
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade" id="rpjmd_tujuan_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="tujuan_filter_visi_{{$tahun}}" class="form-control tujuan_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="tujuan_filter_misi_{{$tahun}}" class="form-control tujuan_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="tujuan_filter_tujuan_{{$tahun}}" class="form-control tujuan_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 tujuan_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light tujuan_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingTujuan" checked>
                                                                <label class="form-check-label" for="onOffTaggingTujuan">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6" style="text-align: right">
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_tujuan.xlsx')}}" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" ><i class="fas fa-file-import"></i></button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="tujuanNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Tujuan End --}}

                                {{-- Sasaran Start --}}
                                <div class="tab-pane fade" id="sasaranNav" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            <li class="nav-item active" role="presentation">
                                                <button class="nav-link active navRpjmdSasaran" data-bs-toggle="tab" data-bs-target="#rpjmd_sasaran_semua" role="tab" aria-selected="true" type="button" data-tahun="semua">
                                                    Semua
                                                </button>
                                            </li>
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link navRpjmdSasaran" data-bs-toggle="tab" data-bs-target="#rpjmd_sasaran_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="tab-pane fade active show" id="rpjmd_sasaran_semua" role="tabpanel">
                                                <div class="row mb-2">
                                                    <div class="col-12">
                                                        <h2 class="small-title">Filter Data</h2>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Visi</label>
                                                            <select id="sasaran_filter_visi_semua" class="form-control sasaran_filter_visi" data-tahun="semua">
                                                                <option value="">--- Pilih Visi ---</option>
                                                                <option value="aman">Aman</option>
                                                                <option value="mandiri">Mandiri</option>
                                                                <option value="sejahtera">Sejahtera</option>
                                                                <option value="berahlak">Berahlak</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Misi</label>
                                                            <select id="sasaran_filter_misi_semua" class="form-control sasaran_filter_misi" data-tahun="semua" disabled>
                                                                <option value="">--- Pilih Misi ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Tujuan</label>
                                                            <select id="sasaran_filter_tujuan_semua" class="form-control sasaran_filter_tujuan" data-tahun="semua" disabled>
                                                                <option value="">--- Pilih Tujuan ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="form-group position-relative mb-3">
                                                            <label for="" class="form-label">Sasaran</label>
                                                            <select id="sasaran_filter_sasaran_semua" class="form-control sasaran_filter_sasaran" data-tahun="semua" disabled>
                                                                <option value="">--- Pilih Sasaran ---</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <label for="" class="form-label">Aksi Filter</label>
                                                        <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                            <button class="btn btn-primary waves-effect waves-light mr-1 sasaran_btn_filter" type="button" data-tahun="semua">Filter Data</button>
                                                            <button class="btn btn-secondary waves-effect waves-light sasaran_btn_reset" type="button" data-tahun="semua">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="onOffTaggingSasaran" checked>
                                                            <label class="form-check-label" for="onOffTaggingSasaran">On / Off Tagging</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-6" style="text-align: right">
                                                        <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_sasaran.xlsx')}}" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                        <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button"><i class="fas fa-file-import"></i></button>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div id="sasaranNavDivsemua"></div>
                                            </div>
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade" id="rpjmd_sasaran_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="sasaran_filter_visi_{{$tahun}}" class="form-control sasaran_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="sasaran_filter_misi_{{$tahun}}" class="form-control sasaran_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="sasaran_filter_tujuan_{{$tahun}}" class="form-control sasaran_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Sasaran</label>
                                                                <select id="sasaran_filter_sasaran_{{$tahun}}" class="form-control sasaran_filter_sasaran" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Sasaran ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 sasaran_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light sasaran_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingSasaran" checked>
                                                                <label class="form-check-label" for="onOffTaggingSasaran">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-6" style="text-align: right">
                                                            <a class="btn btn-success waves-effect waves-light mr-2" href="{{asset('template/template_impor_sasaran.xlsx')}}" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                            <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button"><i class="fas fa-file-import"></i></button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="sasaranNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Sasaran End --}}
                                {{-- Program RPJMD Start --}}
                                <div class="tab-pane fade" id="programNav" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRpjmdProgram" data-bs-toggle="tab" data-bs-target="#rpjmd_program_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="rpjmd_program_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="program_filter_visi_{{$tahun}}" class="form-control program_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="program_filter_misi_{{$tahun}}" class="form-control program_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="program_filter_tujuan_{{$tahun}}" class="form-control program_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Sasaran</label>
                                                                <select id="program_filter_sasaran_{{$tahun}}" class="form-control program_filter_sasaran" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Sasaran ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col" style="text-align: right">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 program_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light program_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-3 justify-content-center align-self-center">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingProgram" checked>
                                                                <label class="form-check-label" for="onOffTaggingProgram">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <form>
                                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                                    <input type="radio" class="btn-check status_program_option" name="status_program_option_{{$tahun}}" id="status_program_option1_{{$tahun}}" value="semua" checked="checked"/>
                                                                    <label class="btn btn-outline-primary" for="status_program_option1_{{$tahun}}">Semua</label>
                                                                    <input type="radio" class="btn-check status_program_option" name="status_program_option_{{$tahun}}" id="status_program_option2_{{$tahun}}" value="prioritas"/>
                                                                    <label class="btn btn-outline-primary" for="status_program_option2_{{$tahun}}">Prioritas</label>
                                                                    <input type="radio" class="btn-check status_program_option" name="status_program_option_{{$tahun}}" id="status_program_option3_{{$tahun}}" value="pendukung"/>
                                                                    <label class="btn btn-outline-primary" for="status_program_option3_{{$tahun}}">Pendukung</label>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-6" style="text-align: right">
                                                            <button class="btn btn-primary waves-effect waves-light btn-icon program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program"><i class="fas fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="programNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Program RPJMD End --}}
                            </div>
                        </div>
                    </div>
                    {{-- RPJMD End --}}
                    {{-- Renstra Start --}}
                    <div class="tab-pane fade" id="renstraTab" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="renstra_tujuan_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_tujuan_pd" role="tab" aria-selected="true" type="button">Tujuan PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renstra_sasaran_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_sasaran_pd" role="tab" aria-selected="false" type="button">Sasaran PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renstra_program_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_program" role="tab" aria-selected="false" type="button">Program</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renstra_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_kegiatan" role="tab" aria-selected="false" type="button">Kegiatan</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                {{-- Renstra Tujuan Start --}}
                                <div class="tab-pane fade active show" id="renstra_tujuan_pd" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRenstraTujuan" data-bs-toggle="tab" data-bs-target="#renstra_tujuan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="renstra_tujuan_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="renstra_tujuan_filter_visi_{{$tahun}}" class="form-control renstra_tujuan_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="renstra_tujuan_filter_misi_{{$tahun}}" class="form-control renstra_tujuan_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="renstra_tujuan_filter_tujuan_{{$tahun}}" class="form-control renstra_tujuan_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 renstra_tujuan_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light renstra_tujuan_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraTujuan" checked>
                                                                <label class="form-check-label" for="onOffTaggingRenstraTujuan">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="renstraTujuanNavDiv{{$tahun}}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Tujuan End --}}
                                {{-- Renstra Sasaran Start --}}
                                <div class="tab-pane fade" id="renstra_sasaran_pd" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRenstraSasaran" data-bs-toggle="tab" data-bs-target="#renstra_sasaran_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="renstra_sasaran_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="renstra_sasaran_filter_visi_{{$tahun}}" class="form-control renstra_sasaran_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="renstra_sasaran_filter_misi_{{$tahun}}" class="form-control renstra_sasaran_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="renstra_sasaran_filter_tujuan_{{$tahun}}" class="form-control renstra_sasaran_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Sasaran</label>
                                                                <select id="renstra_sasaran_filter_sasaran_{{$tahun}}" class="form-control renstra_sasaran_filter_sasaran" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Sasaran ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 renstra_sasaran_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light renstra_sasaran_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraSasaran" checked>
                                                                <label class="form-check-label" for="onOffTaggingRenstraSasaran">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="renstraSasaranNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Sasaran End --}}
                                {{-- Renstra Program Start --}}
                                <div class="tab-pane fade" id="renstra_program" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRenstraProgram" data-bs-toggle="tab" data-bs-target="#renstra_program_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="renstra_program_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="renstra_program_filter_visi_{{$tahun}}" class="form-control renstra_program_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="renstra_program_filter_misi_{{$tahun}}" class="form-control renstra_program_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="renstra_program_filter_tujuan_{{$tahun}}" class="form-control renstra_program_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Sasaran</label>
                                                                <select id="renstra_program_filter_sasaran_{{$tahun}}" class="form-control renstra_program_filter_sasaran" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Sasaran ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Program</label>
                                                                <select id="renstra_program_filter_program_{{$tahun}}" class="form-control renstra_program_filter_program" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Program ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                                                <button class="btn btn-primary waves-effect waves-light mr-1 renstra_program_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                                                <button class="btn btn-secondary waves-effect waves-light renstra_program_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraProgram" checked>
                                                                <label class="form-check-label" for="onOffTaggingRenstraProgram">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="renstraProgramNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Program End --}}
                                {{-- Renstra Kegiatan Start --}}
                                <div class="tab-pane fade" id="renstra_kegiatan" role="tabpanel">
                                    <div class="border-0 pb-0">
                                        <ul class="nav nav-pills responsive-tabs" role="tablist">
                                            @foreach ($tahuns as $tahun)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRenstraKegiatan" data-bs-toggle="tab" data-bs-target="#renstra_kegiatan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                                        {{$tahun}}
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            @foreach ($tahuns as $tahun)
                                                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="renstra_kegiatan_{{$tahun}}" role="tabpanel">
                                                    <div class="row mb-2">
                                                        <div class="col-12">
                                                            <h2 class="small-title">Filter Data</h2>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Visi</label>
                                                                <select id="renstra_kegiatan_filter_visi_{{$tahun}}" class="form-control renstra_kegiatan_filter_visi" data-tahun="{{$tahun}}">
                                                                    <option value="">--- Pilih Visi ---</option>
                                                                    <option value="aman">Aman</option>
                                                                    <option value="mandiri">Mandiri</option>
                                                                    <option value="sejahtera">Sejahtera</option>
                                                                    <option value="berahlak">Berahlak</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Misi</label>
                                                                <select id="renstra_kegiatan_filter_misi_{{$tahun}}" class="form-control renstra_kegiatan_filter_misi" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Misi ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Tujuan</label>
                                                                <select id="renstra_kegiatan_filter_tujuan_{{$tahun}}" class="form-control renstra_kegiatan_filter_tujuan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Tujuan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Sasaran</label>
                                                                <select id="renstra_kegiatan_filter_sasaran_{{$tahun}}" class="form-control renstra_kegiatan_filter_sasaran" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Sasaran ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Program</label>
                                                                <select id="renstra_kegiatan_filter_program_{{$tahun}}" class="form-control renstra_kegiatan_filter_program" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Program ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="form-group position-relative mb-3">
                                                                <label for="" class="form-label">Kegiatan</label>
                                                                <select id="renstra_kegiatan_filter_kegiatan_{{$tahun}}" class="form-control renstra_kegiatan_filter_kegiatan" data-tahun="{{$tahun}}" disabled>
                                                                    <option value="">--- Pilih Kegiatan ---</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label for="" class="form-label">Aksi Filter</label>
                                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                                <button class="btn btn-primary btn-icon waves-effect waves-light mr-1 renstra_kegiatan_btn_filter" type="button" data-tahun="{{$tahun}}"><i class="fas fa-filter"></i></button>
                                                                <button class="btn btn-secondary btn-icon waves-effect waves-light renstra_kegiatan_btn_reset" type="button" data-tahun="{{$tahun}}"><i class="fas fa-recycle"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraKegiatan" checked>
                                                                <label class="form-check-label" for="onOffTaggingRenstraKegiatan">On / Off Tagging</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div id="renstraKegiatanNavDiv{{$tahun}}"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Kegiatan End --}}
                            </div>
                        </div>
                    </div>
                    {{-- Renstra End --}}
                    {{-- RKPD Start --}}
                    <div class="tab-pane fade" id="rkpdTab" role="tabpanel">
                        @include('admin.perencanaan.rkpd.index')
                    </div>
                    {{-- RKPD End --}}
                    {{-- Renja Start --}}
                    <div class="tab-pane fade" id="renjaTab" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="renja_tujuan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_tujuan_pd" role="tab" aria-selected="true" type="button">Tujuan PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renja_sasaran_tab_button" data-bs-toggle="tab" data-bs-target="#renja_sasaran_pd" role="tab" aria-selected="false" type="button">Sasaran PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renja_program_tab_button" data-bs-toggle="tab" data-bs-target="#renja_program" role="tab" aria-selected="false" type="button">Program</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renja_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_kegiatan" role="tab" aria-selected="false" type="button">Kegiatan</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="renja_sub_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_sub_kegiatan" role="tab" aria-selected="false" type="button">Sub Kegiatan</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="renja_tujuan_pd" role="tabpanel">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <h2 class="small-title">Filter Data</h2>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Visi</label>
                                                <select id="renja_tujuan_filter_visi" class="form-control renja_tujuan_filter_visi">
                                                    <option value="">--- Pilih Visi ---</option>
                                                    <option value="aman">Aman</option>
                                                    <option value="mandiri">Mandiri</option>
                                                    <option value="sejahtera">Sejahtera</option>
                                                    <option value="berahlak">Berahlak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Misi</label>
                                                <select id="renja_tujuan_filter_misi" class="form-control renja_tujuan_filter_misi" disabled>
                                                    <option value="">--- Pilih Misi ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Tujuan</label>
                                                <select id="renja_tujuan_filter_tujuan" class="form-control renja_tujuan_filter_tujuan" disabled>
                                                    <option value="">--- Pilih Tujuan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="" class="form-label">Aksi Filter</label>
                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                <button class="btn btn-primary waves-effect waves-light mr-1 renja_tujuan_btn_filter" type="button">Filter Data</button>
                                                <button class="btn btn-secondary waves-effect waves-light renja_tujuan_btn_reset" type="button">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenjaTujuan" checked>
                                                <label class="form-check-label" for="onOffTaggingRenjaTujuan">On / Off Tagging</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="renjaTujuanNavDiv">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="renja_sasaran_pd" role="tabpanel">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <h2 class="small-title">Filter Data</h2>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Visi</label>
                                                <select id="renja_sasaran_filter_visi" class="form-control renja_sasaran_filter_visi">
                                                    <option value="">--- Pilih Visi ---</option>
                                                    <option value="aman">Aman</option>
                                                    <option value="mandiri">Mandiri</option>
                                                    <option value="sejahtera">Sejahtera</option>
                                                    <option value="berahlak">Berahlak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Misi</label>
                                                <select id="renja_sasaran_filter_misi" class="form-control renja_sasaran_filter_misi" disabled>
                                                    <option value="">--- Pilih Misi ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Tujuan</label>
                                                <select id="renja_sasaran_filter_tujuan" class="form-control renja_sasaran_filter_tujuan" disabled>
                                                    <option value="">--- Pilih Tujuan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Sasaran</label>
                                                <select id="renja_sasaran_filter_sasaran" class="form-control renja_sasaran_filter_sasaran" disabled>
                                                    <option value="">--- Pilih Sasaran ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="" class="form-label">Aksi Filter</label>
                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                <button class="btn btn-primary waves-effect waves-light mr-1 renja_sasaran_btn_filter" type="button">Filter Data</button>
                                                <button class="btn btn-secondary waves-effect waves-light renja_sasaran_btn_reset" type="button">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenjaSasaran" checked>
                                                <label class="form-check-label" for="onOffTaggingRenjaSasaran">On / Off Tagging</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="renjaSasaranNavDiv"></div>
                                </div>
                                <div class="tab-pane fade" id="renja_program" role="tabpanel">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <h2 class="small-title">Filter Data</h2>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Visi</label>
                                                <select id="renja_program_filter_visi" class="form-control renja_program_filter_visi">
                                                    <option value="">--- Pilih Visi ---</option>
                                                    <option value="aman">Aman</option>
                                                    <option value="mandiri">Mandiri</option>
                                                    <option value="sejahtera">Sejahtera</option>
                                                    <option value="berahlak">Berahlak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Misi</label>
                                                <select id="renja_program_filter_misi" class="form-control renja_program_filter_misi" disabled>
                                                    <option value="">--- Pilih Misi ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Tujuan</label>
                                                <select id="renja_program_filter_tujuan" class="form-control renja_program_filter_tujuan" disabled>
                                                    <option value="">--- Pilih Tujuan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Sasaran</label>
                                                <select id="renja_program_filter_sasaran" class="form-control renja_program_filter_sasaran" disabled>
                                                    <option value="">--- Pilih Sasaran ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Program</label>
                                                <select id="renja_program_filter_program" class="form-control renja_program_filter_program" disabled>
                                                    <option value="">--- Pilih Program ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="" class="form-label">Aksi Filter</label>
                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                                <button class="btn btn-primary waves-effect waves-light mr-1 renja_program_btn_filter" type="button">Filter Data</button>
                                                <button class="btn btn-secondary waves-effect waves-light renja_program_btn_reset" type="button">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenjaProgram" checked>
                                                <label class="form-check-label" for="onOffTaggingRenjaProgram">On / Off Tagging</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="renjaProgramNavDiv"></div>
                                </div>
                                <div class="tab-pane fade" id="renja_kegiatan" role="tabpanel">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <h2 class="small-title">Filter Data</h2>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Visi</label>
                                                <select id="renja_kegiatan_filter_visi" class="form-control renja_kegiatan_filter_visi">
                                                    <option value="">--- Pilih Visi ---</option>
                                                    <option value="aman">Aman</option>
                                                    <option value="mandiri">Mandiri</option>
                                                    <option value="sejahtera">Sejahtera</option>
                                                    <option value="berahlak">Berahlak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Misi</label>
                                                <select id="renja_kegiatan_filter_misi" class="form-control renja_kegiatan_filter_misi" disabled>
                                                    <option value="">--- Pilih Misi ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Tujuan</label>
                                                <select id="renja_kegiatan_filter_tujuan" class="form-control renja_kegiatan_filter_tujuan" disabled>
                                                    <option value="">--- Pilih Tujuan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Sasaran</label>
                                                <select id="renja_kegiatan_filter_sasaran" class="form-control renja_kegiatan_filter_sasaran" disabled>
                                                    <option value="">--- Pilih Sasaran ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Program</label>
                                                <select id="renja_kegiatan_filter_program" class="form-control renja_kegiatan_filter_program" disabled>
                                                    <option value="">--- Pilih Program ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Kegiatan</label>
                                                <select id="renja_kegiatan_filter_kegiatan_{{$tahun}}" class="form-control renja_kegiatan_filter_kegiatan" disabled>
                                                    <option value="">--- Pilih Kegiatan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="" class="form-label">Aksi Filter</label>
                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                <button class="btn btn-primary btn-icon waves-effect waves-light mr-1 renja_kegiatan_btn_filter" type="button"><i class="fas fa-filter"></i></button>
                                                <button class="btn btn-secondary btn-icon waves-effect waves-light renja_kegiatan_btn_reset" type="button"><i class="fas fa-recycle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenjaKegiatan" checked>
                                                <label class="form-check-label" for="onOffTaggingRenjaKegiatan">On / Off Tagging</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="renjaKegiatanNavDiv"></div>
                                </div>
                                <div class="tab-pane fade" id="renja_sub_kegiatan" role="tabpanel">
                                    <div class="row mb-2">
                                        <div class="col-12">
                                            <h2 class="small-title">Filter Data</h2>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Visi</label>
                                                <select id="renja_sub_kegiatan_filter_visi" class="form-control renja_sub_kegiatan_filter_visi">
                                                    <option value="">--- Pilih Visi ---</option>
                                                    <option value="aman">Aman</option>
                                                    <option value="mandiri">Mandiri</option>
                                                    <option value="sejahtera">Sejahtera</option>
                                                    <option value="berahlak">Berahlak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Misi</label>
                                                <select id="renja_sub_kegiatan_filter_misi" class="form-control renja_sub_kegiatan_filter_misi" disabled>
                                                    <option value="">--- Pilih Misi ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Tujuan</label>
                                                <select id="renja_sub_kegiatan_filter_tujuan" class="form-control renja_sub_kegiatan_filter_tujuan" disabled>
                                                    <option value="">--- Pilih Tujuan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Sasaran</label>
                                                <select id="renja_sub_kegiatan_filter_sasaran" class="form-control renja_sub_kegiatan_filter_sasaran" disabled>
                                                    <option value="">--- Pilih Sasaran ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Program</label>
                                                <select id="renja_sub_kegiatan_filter_program" class="form-control renja_sub_kegiatan_filter_program" disabled>
                                                    <option value="">--- Pilih Program ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Kegiatan</label>
                                                <select id="renja_sub_kegiatan_filter_kegiatan" class="form-control renja_sub_kegiatan_filter_kegiatan" disabled>
                                                    <option value="">--- Pilih Kegiatan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group position-relative mb-3">
                                                <label for="" class="form-label">Sub Kegiatan</label>
                                                <select id="renja_sub_kegiatan_filter_kegiatan" class="form-control renja_sub_kegiatan_filter_kegiatan" disabled>
                                                    <option value="">--- Pilih Sub Kegiatan ---</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <label for="" class="form-label">Aksi Filter</label>
                                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                                <button class="btn btn-primary btn-icon waves-effect waves-light mr-1 renja_sub_kegiatan_btn_filter" type="button"><i class="fas fa-filter"></i></button>
                                                <button class="btn btn-secondary btn-icon waves-effect waves-light renja_sub_kegiatan_btn_reset" type="button"><i class="fas fa-recycle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="onOffTaggingRenjaSubKegiatan" checked>
                                                <label class="form-check-label" for="onOffTaggingRenjaSubKegiatan">On / Off Tagging</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="renjaSubKegiatanNavDiv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Renja End --}}
                </div>
            </div>
        </div>
    </div>
    {{-- Visi Modal Start --}}
    <div class="modal fade" id="addEditVisiModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="visi_form_result"></span>
                    <form id="visi_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label for="" class="form-label">Visi</label>
                                <textarea name="visi_deskripsi" id="visi_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label for="" class="form-label">Tahun Perubahan</label>
                                <select name="visi_tahun_perubahan" id="visi_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="visi_aksi" id="visi_aksi" value="Save">
                    <input type="hidden" name="visi_hidden_id" id="visi_hidden_id">
                    <button type="submit" class="btn btn-primary" name="visi_aksi_button" id="visi_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailVisiModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
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
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="visi_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Tahun Perubahan</label>
                                <input type="text" class="form-control" id="visi_detail_tahun_perubahan" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Visi</label>
                                <div id="div_pivot_perubahan_visi"></div>
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
    {{-- Visi Modal End --}}

    {{-- Misi Modal Start--}}
    <div class="modal fade" id="addEditMisiModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="misi_form_result"></span>
                    <form id="misi_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="misi_visi_id" id="misi_visi_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="misi_kode" id="misi_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="misi_deskripsi" id="misi_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="misi_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="misi_tahun_perubahan" id="misi_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="misi_aksi" id="misi_aksi" value="Save">
                    <input type="hidden" name="misi_hidden_id" id="misi_hidden_id">
                    <button type="button" class="btn btn-primary" name="misi_aksi_button" id="misi_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal modal-right large scroll-out-negative fade" id="detailMisiModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="scroll-track-visible">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Visi</label>
                            <div class="input-group">
                                <div class="input-group-text"></div>
                                <textarea id="misi_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>

                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="misi_detail_kode"></span></div>
                                <textarea id="misi_detail_deskripsi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>

                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Perubahan Misi</label>
                            <div id="div_pivot_perubahan_misi" class="scrollBarPagination"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Misi Modal End--}}

    {{-- Tujuan Start --}}
    <div class="modal fade" id="addEditTujuanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="tujuan_form_result"></span>
                    <form id="tujuan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tujuan_misi_id" id="tujuan_misi_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="tujuan_kode" id="tujuan_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="tujuan_deskripsi" class="form-label">Deskripsi</label>
                                <textarea name="tujuan_deskripsi" id="tujuan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tujuan_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="tujuan_tahun_perubahan" id="tujuan_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="tujuan_aksi" id="tujuan_aksi" value="Save">
                    <input type="hidden" name="tujuan_hidden_id" id="tujuan_hidden_id">
                    <button type="button" class="btn btn-primary" name="tujuan_aksi_button" id="tujuan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="importTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importTujuanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.tujuan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_tujuan" name="impor_tujuan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailTujuanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
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
                                <label for="" class="form-label">Visi</label>
                                <textarea id="tujuan_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Misi</label>
                                <textarea id="tujuan_detail_misi" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="tujuan_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="tujuan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Perubahan</label>
                                <input id="tujuan_detail_tahun_perubahan" type="text" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Tujuan</label>
                                <div id="div_pivot_perubahan_tujuan" class="scrollBarPagination"></div>
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

    <div id="indikatorKinerjaTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaTujuanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Tujuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        {{-- action="{{ route('admin.tujuan.indikator-kinerja.tambah') }}" --}}
                        @csrf
                        <input type="hidden" name="indikator_kinerja_tujuan_tujuan_id" id="indikator_kinerja_tujuan_tujuan_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <textarea name="indikator_kinerja_tujuan_deskripsi" id="indikator_kinerja_tujuan_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_tujuan_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="indikator_kinerja_tujuan_satuan" name="indikator_kinerja_tujuan_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_tujuan_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" class="form-control" id="indikator_kinerja_tujuan_kondisi_target_kinerja_awal" name="indikator_kinerja_tujuan_kondisi_target_kinerja_awal" step="any" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light" id="indikator_kinerja_tujuan_btn" type="button">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetTujuanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        {{-- action="{{ route('admin.tujuan.indikator.target-satuan-rp-realisasi_update') }}" --}}
                        @csrf
                        <input type="hidden" name="tujuan_target_satuan_rp_realisasi" id="tujuan_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="tujuan_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="tujuan_edit_target" name="tujuan_edit_target" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light" type="button" id="tujuan_edit_btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editIndikatorKinerjaTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editIndikatorKinerjaTujuanModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Indikator Kinerja Tujuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- action="{{ route('admin.tujuan.indikator-kinerja.update') }}" --}}
                        <input type="hidden" name="edit_indikator_kinerja_tujuan_id" id="edit_indikator_kinerja_tujuan_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Indikator Kinerja</label>
                            <textarea name="edit_indikator_kinerja_tujuan_deskripsi" id="edit_indikator_kinerja_tujuan_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_tujuan_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_tujuan_satuan" name="edit_indikator_kinerja_tujuan_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" class="form-control" id="edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal" name="edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal" step="any" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light" type="button" id="edit_indikator_kinerja_tujuan_btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="hapusTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="hapusTujuanModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Hapus Tujuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        {{-- action="{{ route('admin.tujuan.hapus') }}" --}}
                        @csrf
                        <input type="hidden" name="hapus_tujuan_id" id="hapus_tujuan_id">
                        <div class="position-relative form-group mb-3">
                            <label for="hapus_tujuan_tahun" class="form-label">Pilih Tahun</label>
                            <select name="hapus_tujuan_tahun" id="hapus_tujuan_tahun" class="form-control" required>
                                <option value="semua">Semua</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{$tahun}}">{{$tahun}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light" type="button" id="hapus_tujuan_btn">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Tujuan End --}}

    {{-- Sasaran Start --}}
    <div class="modal fade" id="addEditSasaranModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="sasaran_form_result"></span>
                    <form id="sasaran_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_tujuan_id" id="sasaran_tujuan_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="sasaran_kode" id="sasaran_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="sasaran_deskripsi" id="sasaran_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sasaran_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="sasaran_tahun_perubahan" id="sasaran_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="sasaran_aksi" id="sasaran_aksi" value="Save">
                    <input type="hidden" name="sasaran_hidden_id" id="sasaran_hidden_id">
                    <button type="submit" class="btn btn-primary" name="sasaran_aksi_button" id="sasaran_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal modal-right large scroll-out-negative fade" id="detailSasaranModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="scroll-track-visible">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Visi</label>
                            <div class="input-group">
                                <div class="input-group-text"></div>
                                <textarea id="sasaran_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_misi_kode"></span></div>
                                <textarea id="sasaran_detail_misi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_tujuan_kode"></span></div>
                                <textarea id="sasaran_detail_tujuan" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>

                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_kode"></span></div>
                                <textarea id="sasaran_detail_deskripsi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Perubahan Sasaran</label>
                            <div id="div_pivot_perubahan_sasaran" class="scrollBarPagination"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="importSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_sasaran" name="impor_sasaran" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="indikatorKinerjaSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Sasaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_sasaran_sasaran_id" id="indikator_kinerja_sasaran_sasaran_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <textarea name="indikator_kinerja_sasaran_deskripsi" id="indikator_kinerja_sasaran_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_sasaran_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="indikator_kinerja_sasaran_satuan" name="indikator_kinerja_sasaran_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_sasaran_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" name="indikator_kinerja_sasaran_kondisi_target_kinerja_awal" id="indikator_kinerja_sasaran_kondisi_target_kinerja_awal" step="any" class="form-control" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.indikator.target-satuan-rp-realisasi_update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_target_satuan_rp_realisasi" id="sasaran_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="sasaran_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="sasaran_edit_target" name="sasaran_edit_target" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editIndikatorKinerjaSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editIndikatorKinerjaSasaranModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Indikator Kinerja Sasaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.indikator-kinerja.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_indikator_kinerja_sasaran_id" id="edit_indikator_kinerja_sasaran_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Indikator Kinerja</label>
                            <textarea name="edit_indikator_kinerja_sasaran_deskripsi" id="edit_indikator_kinerja_sasaran_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_sasaran_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_sasaran_satuan" name="edit_indikator_kinerja_sasaran_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" name="edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal" id="edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal" step="any" class="form-control" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="hapusSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="hapusSasaranModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Hapus Sasaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.hapus') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="hapus_sasaran_id" id="hapus_sasaran_id">
                        <div class="position-relative form-group mb-3">
                            <label for="hapus_sasaran_tahun" class="form-label">Pilih Tahun</label>
                            <select name="hapus_sasaran_tahun" id="hapus_sasaran_tahun" class="form-control" required>
                                <option value="semua">Semua</option>
                                @foreach ($tahuns as $tahun)
                                    <option value="{{$tahun}}">{{$tahun}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Sasaran End --}}

    {{-- Sasaran Indikator Start --}}
    <div class="modal fade" id="addEditSasaranIndikatorModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="sasaran_indikator_form_result"></span>
                    <form id="sasaran_indikator_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_indikator_sasaran_id" id="sasaran_indikator_sasaran_id">
                        <div class="mb-3">
                            <label class="form-label">Indikator</label>
                            <textarea name="sasaran_indikator_indikator" id="sasaran_indikator_indikator" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Target</label>
                            <input type="number" name="sasaran_indikator_target" id="sasaran_indikator_target" class="form-control" step="any" required>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="sasaran_indikator_satuan" id="sasaran_indikator_satuan" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="sasaran_indikator_aksi" id="sasaran_indikator_aksi" value="Save">
                    <input type="hidden" name="sasaran_indikator_hidden_id" id="sasaran_indikator_hidden_id">
                    <button type="submit" class="btn btn-primary" name="sasaran_indikator_aksi_button" id="sasaran_indikator_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal modal-right large scroll-out-negative fade" id="detailSasaranIndikatorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group position-relative mb-3">
                                <label for="" class="form-label">Indikator</label>
                                <textarea id="sasaran_indikator_detail_indikator" rows="5" class="form-control" disabled></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Target</label>
                                    <input type="text" class="form-label" id="sasaran_indikator_detail_target" required>
                                </div>
                                <div class="col-12">
                                    <label for="" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="sasaran_indikator_detail_satuan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="data-table-rows slim">
                        <!-- Table Start -->
                        <div class="data-table-responsive-wrapper">
                            <table id="visi_table" class="data-table w-100">
                                <thead>
                                    <tr>
                                        <th class="text-muted text-small text-uppercase" width="10%">No</th>
                                        <th class="text-muted text-small text-uppercase" width="55%">Target</th>
                                        <th class="text-muted text-small text-uppercase" width="15%">Satuan</th>
                                        <th class="text-muted text-small text-uppercase" width="20%">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- Table End -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- Sassaran Indikator End --}}

    {{-- Program RPJMD Start --}}
    <div class="modal fade" id="addEditProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="program_form_result"></span>
                    <form id="program_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h2 class="small-title">Atur Program</h2>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Urusan</label>
                                    <select name="program_urusan_id" id="program_urusan_id" class="form-control" required>
                                        <option value="">--- Pilih Urusan ---</option>
                                        @foreach ($urusans as $urusan)
                                            <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Program</label>
                                    <select name="program_program_id" id="program_program_id" class="form-control" disabled required>
                                        <option value="">--- Pilih Program ---</option>
                                    </select>
                                </div>
                                <div class="form-group position-relative mb-3">
                                    <label for="program_status_program" class="form-label">Status Program</label>
                                    <select name="program_status_program" id="program_status_program" class="form-control" required>
                                        <option value="">--- Pilih Status Program ---</option>
                                        <option value="Prioritas">Prioritas</option>
                                        <option value="Pendukung">Pendukung</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <h2 class="small-title">Atur Indikator Kinerja Sasaran Yang Terkait</h2>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Misi</label>
                                    <select name="program_misi_id" id="program_misi_id" class="form-control" required>
                                        <option value="">--- Pilih Misi ---</option>
                                        @foreach ($misis as $misi)
                                            <option value="{{$misi['id']}}">{{$misi['kode']}}. {{$misi['deskripsi']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="program_tujuan_id" id="program_tujuan_id" class="form-control" disabled required>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="program_sasaran_id" id="program_sasaran_id" class="form-control" disabled required>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran Indikator</label>
                                    <select name="program_sasaran_indikator_id[]" id="program_sasaran_indikator_id" class="form-control" multiple="multiple" disabled required>
                                    </select>
                                </div>
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
    <div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="program_edit_form_result"></span>
                    <form id="program_edit_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <h2 class="small-title">Atur Program</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <select name="program_edit_urusan_id" id="program_edit_urusan_id" class="form-control" required>
                                <option value="">--- Pilih Urusan ---</option>
                                @foreach ($urusans as $urusan)
                                    <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Program</label>
                            <select name="program_edit_program_id" id="program_edit_program_id" class="form-control" disabled required>
                                <option value="">--- Pilih Program ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Status Program</label>
                            <select name="program_edit_status_program" id="program_edit_status_program" class="form-control" required>
                                <option value="">--- Pilih Status Program ---</option>
                                <option value="Program Prioritas">Program Prioritas</option>
                                <option value="Program Pendukung">Program Pendukung</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pagu</label>
                            <input type="number" name="program_edit_pagu" id="program_edit_pagu" class="form-control" required>
                        </div>
                        <h2 class="small-title">Atur Sasaran Indikator</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <select name="program_edit_misi_id" id="program_edit_misi_id" class="form-control" required>
                                <option value="">--- Pilih Misi ---</option>
                                @foreach ($misis as $misi)
                                    <option value="{{$misi['id']}}">{{$misi['kode']}}. {{$misi['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <select name="program_edit_tujuan_id" id="program_edit_tujuan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Tujuan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <select name="program_edit_sasaran_id" id="program_edit_sasaran_id" class="form-control" disabled required>
                                <option value="">--- Pilih Sasaran ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran Indikator</label>
                            <select name="program_edit_sasaran_indikator_id" id="program_edit_sasaran_indikator_id" class="form-control" disabled required>
                            </select>
                        </div>
                        <h2 class="small-title">Atur OPD Terkait</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pilih OPD</label>
                            <select name="program_edit_opd_id" id="program_edit_opd_id" class="form-control" required>
                                @foreach ($opds as $id => $nama)
                                    <option value="{{$id}}">{{$nama}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="program_edit_aksi" id="program_edit_aksi" value="Save">
                    <input type="hidden" name="program_edit_hidden_id" id="program_edit_hidden_id">
                    <button type="submit" class="btn btn-primary" name="program_edit_aksi_button" id="program_edit_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Program RPJMD End --}}

    {{-- Kegiatan Renstra Start --}}
    <div class="modal fade" id="addEditRenstraKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="renstra_kegiatan_form_result"></span>
                    <form id="renstra_kegiatan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="renstra_kegiatan_program_rpjmd_id" id="renstra_kegiatan_program_rpjmd_id">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <select name="renstra_kegiatan_kegiatan_id" id="renstra_kegiatan_kegiatan_id" class="form-control" required>
                                <option value="">--- Pilih Kegiatan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="renstra_kegiatan_opd_id" class="form-label">OPD</label>
                            <select name="renstra_kegiatan_opd_id[]" id="renstra_kegiatan_opd_id" class="form-control" multiple required></select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="renstra_kegiatan_pagu" class="form-label">Pagu</label>
                            <input type="number" name="renstra_kegiatan_pagu" id="renstra_kegiatan_pagu" class="form-control" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="renstra_kegiatan_aksi" id="renstra_kegiatan_aksi" value="Save">
                    <input type="hidden" name="renstra_kegiatan_hidden_id" id="renstra_kegiatan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="renstra_kegiatan_aksi_button" id="renstra_kegiatan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal modal-right large scroll-out-negative fade" id="detailRenstraKegiatanModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="scroll-track-visible">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Visi</label>
                            <div class="input-group">
                                <div class="input-group-text"></div>
                                <textarea id="renstra_kegiatan_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="renstra_kegiatan_detail_misi_kode"></span></div>
                                <textarea id="renstra_kegiatan_detail_misi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="renstra_kegiatan_detail_tujuan_kode"></span></div>
                                <textarea id="renstra_kegiatan_detail_tujuan" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="renstra_kegiatan_detail_sasaran_kode"></span></div>
                                <textarea id="renstra_kegiatan_detail_sasaran" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="data-table-rows slim">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Sasaran Indikator</label>
                                </div>
                            </div>
                            <!-- Table Start -->
                            <div class="data-table-responsive-wrapper">
                                <table class="data-table w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-muted text-small text-uppercase" width="50%">Indikator</th>
                                            <th class="text-muted text-small text-uppercase" width="25%">Target</th>
                                            <th class="text-muted text-small text-uppercase" width="25%">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="renstra_kegiatan_tbody_detail_sasaran_indikator">

                                    </tbody>
                                </table>
                            </div>
                            <!-- Table End -->
                        </div>
                        <hr>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Program</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="renstra_kegiatan_detail_program_kode"></span></div>
                                <textarea id="renstra_kegiatan_detail_program" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="renstra_kegiatan_detail_kegiatan_kode"></span></div>
                                <textarea id="renstra_kegiatan_detail_kegiatan" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <hr>
                        <div id="renstra_kegiatan_atur_target_rp_pertahun"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Kegiatan Renstra End --}}
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
    @stack('script_rkpd')
    <script>
        var tahun_awal = "{{$tahun_awal}}";
        // Visi Start
        $(document).ready(function(){
            $('#program_urusan_id').select2();
            $('#program_program_id').select2();
            $('#program_misi_id').select2();
            $('#program_tujuan_id').select2();
            $('#program_sasaran_id').select2();
            $('#program_sasaran_indikator_id').select2();
            $('#program_opd_id').select2();
            $('#renstra_kegiatan_kegiatan_id').select2();
            $('#renstra_kegiatan_opd_id').select2();

            $('#renja_filter_opd').select2();
            $('#renja_filter_program').select2();
            $('#renja_filter_kegiatan').select2();
            $('#renja_filter_sub_kegiatan').select2();
            $('#rkpd_opd_tahun_pembangunan_opd_id').select2();

            $('.dropify').dropify();
            $('.dropify-wrapper').css('line-height', '3rem');
            @foreach ($tahuns as $tahun)
                var tahun = "{{$tahun}}";

                // $('#misi_filter_visi_'+tahun).select2();
                // $('#misi_filter_misi_'+tahun).select2();

                // $('#tujuan_filter_visi_'+tahun).select2();
                // $('#tujuan_filter_misi_'+tahun).select2();
                // $('#tujuan_filter_tujuan_'+tahun).select2();

                // $('#sasaran_filter_visi_'+tahun).select2();
                // $('#sasaran_filter_misi_'+tahun).select2();
                // $('#sasaran_filter_tujuan_'+tahun).select2();
                // $('#sasaran_filter_sasaran_'+tahun).select2();

                // $('#program_filter_visi_'+tahun).select2();
                // $('#program_filter_misi_'+tahun).select2();
                // $('#program_filter_tujuan_'+tahun).select2();
                // $('#program_filter_sasaran_'+tahun).select2();

                // $('#renstra_tujuan_filter_visi_'+tahun).select2();
                // $('#renstra_tujuan_filter_misi_'+tahun).select2();
                // $('#renstra_tujuan_filter_tujuan_'+tahun).select2();

                // $('#renstra_sasaran_filter_visi_'+tahun).select2();
                // $('#renstra_sasaran_filter_misi_'+tahun).select2();
                // $('#renstra_sasaran_filter_tujuan_'+tahun).select2();
                // $('#renstra_sasaran_filter_sasaran_'+tahun).select2();

                // $('#renstra_program_filter_visi_'+tahun).select2();
                // $('#renstra_program_filter_misi_'+tahun).select2();
                // $('#renstra_program_filter_tujuan_'+tahun).select2();
                // $('#renstra_program_filter_sasaran_'+tahun).select2();
                // $('#renstra_program_filter_program_'+tahun).select2();

                // $('#renstra_kegiatan_filter_visi_'+tahun).select2();
                // $('#renstra_kegiatan_filter_misi_'+tahun).select2();
                // $('#renstra_kegiatan_filter_tujuan_'+tahun).select2();
                // $('#renstra_kegiatan_filter_sasaran_'+tahun).select2();
                // $('#renstra_kegiatan_filter_program_'+tahun).select2();
                // $('#renstra_kegiatan_filter_kegiatan_'+tahun).select2();

                // $('#rkpd_filter_opd_'+tahun).select2();

                var dataTables = $('#visi_table_'+tahun).DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('/admin/visi/get-visi') }}" + '/' + tahun,
                    },
                    columns:[
                        {
                            data: 'DT_RowIndex'
                        },
                        {
                            data: 'deskripsi',
                            name: 'deskripsi'
                        },
                        // {
                        //     data: 'tahun_perubahan',
                        //     name: 'tahun_perubahan'
                        // },
                        {
                            data: 'aksi',
                            name: 'aksi',
                            orderable: false
                        },
                    ]
                });
            @endforeach

            // new Tagify(document.querySelector('#indikator_kinerja_tujuan_deskripsi'));
            // new Tagify(document.querySelector('#indikator_kinerja_sasaran_deskripsi'));
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv2019').html(data.html);
                }
            });

            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });

            var dataTables = $('#rkpd_tahun_pembangunan_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan') }}",
                },
                columns:[
                    {
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false
                    },
                ]
            });

            $.ajax({
                url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd') }}" + '/' + tahun_awal,
                dataType: "json",
                success: function(data)
                {
                    $('#rkpdNavDiv'+tahun_awal).html(data.html);
                }
            });

            $.ajax({
                url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/get-opd') }}" + '/' + tahun_awal,
                dataType: "json",
                success: function(data)
                {
                    $('#rkpd_filter_opd_'+tahun_awal).empty();
                    $('#rkpd_filter_opd_'+tahun_awal).append('<option value="">--- Pilih OPD ---</option>');
                    $.each(data, function(key, value){
                        $('#rkpd_filter_opd_'+tahun_awal).append(new Option(value.nama, value.id));
                    });
                }
            });

        });
        $(document).on('click', '.visi_detail', function(){
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ url('/admin/visi/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_visi').remove();
                    $('#div_pivot_perubahan_visi').append('<div id="pivot_perubahan_visi"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#visi_detail_deskripsi').val(data.result.deskripsi);
                    $('#visi_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#pivot_perubahan_visi').append(data.result.pivot_perubahan_visi);
                    $('#detailVisiModal').modal('show');
                }
            });
        });
        $('#visi_create').click(function(){
            $('#visi_form')[0].reset();
            $('#visi_aksi_button').text('Save');
            $('#visi_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data');
            $('#visi_aksi_button').val('Save');
            $('#visi_aksi').val('Save');
            $('#visi_form_result').html('');
        });
        $('#visi_form').on('submit', function(e){
            e.preventDefault();
            if($('#visi_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.visi.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#visi_aksi_button').text('Menyimpan...');
                        $('#visi_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#visi_aksi_button').prop('disabled', false);
                            $('#visi_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#visi_form_result').html(html);
                    }
                });
            }
            if($('#visi_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.visi.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function(){
                        $('#visi_aksi_button').text('Mengubah...');
                        $('#visi_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#visi_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#visi_form_result').html(html);
                    }
                });
            }
        });
        $(document).on('click', '.visi_edit', function(){
            var id = $(this).attr('id');
            $('#visi_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/visi/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#visi_deskripsi').val(data.result.deskripsi);
                    $("[name='visi_tahun_perubahan']").val(data.result.tahun_perubahan);
                    $('#visi_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#visi_aksi_button').text('Edit');
                    $('#visi_aksi_button').prop('disabled', false);
                    $('#visi_aksi_button').val('Edit');
                    $('#visi_aksi').val('Edit');
                    $('#addEditVisiModal').modal('show');
                }
            });
        });
        // Visi End

        // Misi Start
        $('#misi_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-misi') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#misiNavDivsemua').html(data.html);
                }
            });
        });

        $('.navRpjmdMisi').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/get-misi') }}" + '/' + tahun,
                dataType: "json",
                success: function (data)
                {
                    $('#misiNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('click','.misi_create',function(){
            $('#misi_visi_id').val($(this).attr('data-visi-id'));
            $('#misi_form')[0].reset();
            $('#misi_aksi_button').text('Save');
            $('#misi_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Misi');
            $('#misi_aksi_button').val('Save');
            $('#misi_aksi').val('Save');
            $('#misi_form_result').html('');
        });

        $('#misi_aksi_button').click(function(){
            var misi_visi_id = $('#misi_visi_id').val();
            var misi_kode = $('#misi_kode').val();
            var misi_deskripsi = $('#misi_deskripsi').val();
            var misi_tahun_perubahan = $('#misi_tahun_perubahan').val();
            var misi_hidden_id = $('#misi_hidden_id').val();
            var nav_rpjmd_misi_tahun = $('.navRpjmdMisi.active').attr('data-tahun');
            var misi_filter_visi = $('#misi_filter_visi_'+nav_rpjmd_misi_tahun).val();
            var misi_filter_misi = $('#misi_filter_misi_'+nav_rpjmd_misi_tahun).val();

            if($('#misi_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.misi.store') }}",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        misi_visi_id:misi_visi_id,
                        misi_kode:misi_kode,
                        misi_deskripsi:misi_deskripsi,
                        misi_tahun_perubahan:misi_tahun_perubahan,
                        misi_hidden_id:misi_hidden_id,
                        nav_rpjmd_misi_tahun:nav_rpjmd_misi_tahun,
                        misi_filter_visi:misi_filter_visi,
                        misi_filter_misi:misi_filter_misi
                    },
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
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#misi_aksi_button').prop('disabled', false);
                            $('#misi_form')[0].reset();
                            $('#misi_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                            $('#misiNavDiv'+nav_rpjmd_misi_tahun).html(data.html);
                            $('#addEditMisiModal').modal('hide');
                        }

                        $('#misi_form_result').html(html);
                    }
                });
            }

            if($('#misi_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.misi.update') }}",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        misi_visi_id:misi_visi_id,
                        misi_kode:misi_kode,
                        misi_deskripsi:misi_deskripsi,
                        misi_tahun_perubahan:misi_tahun_perubahan,
                        misi_hidden_id:misi_hidden_id,
                        nav_rpjmd_misi_tahun:nav_rpjmd_misi_tahun,
                        misi_filter_visi:misi_filter_visi,
                        misi_filter_misi:misi_filter_misi
                    },
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
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#misi_aksi_button').prop('disabled', false);
                            $('#misi_form')[0].reset();
                            $('#misi_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                            $('#misiNavDiv'+nav_rpjmd_misi_tahun).html(data.html);
                            $('#addEditMisiModal').modal('hide');
                        }

                        $('#misi_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-misi', function(){
            var id = $(this).attr('data-misi-id');
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/misi/detail') }}"+'/'+id+'/'+tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_misi').remove();
                    $('#div_pivot_perubahan_misi').append('<div id="pivot_perubahan_misi"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#misi_detail_visi').val(data.result.visi);
                    $('#misi_detail_kode').text(data.result.kode);
                    $('#misi_detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_misi').append(data.result.pivot_perubahan_misi);
                    $('#misi_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#detailMisiModal').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-misi', function(){
            var id = $(this).attr('data-misi-id');
            var tahun = $(this).attr('data-tahun');
            $('#misi_visi_id').val($(this).attr('data-visi-id'));
            $('#misi_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/misi/edit') }}"+'/'+id+'/'+tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#misi_kode').val(data.result.kode);
                    $('#misi_deskripsi').val(data.result.deskripsi);
                    $("[name='misi_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                    $('#misi_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#misi_aksi_button').text('Edit');
                    $('#misi_aksi_button').prop('disabled', false);
                    $('#misi_aksi_button').val('Edit');
                    $('#misi_aksi').val('Edit');
                    $('#addEditMisiModal').modal('show');
                }
            });
        });
        // Misi End

        // Tujuan Start
        $('#tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#tujuanNavDivsemua').html(data.html);
                }
            });
        });

        $('.navRpjmdTujuan').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/get-tujuan') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#tujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('click','.tujuan_create',function(){
            $('#tujuan_misi_id').val($(this).attr('data-misi-id'));
            $('#tujuan_form')[0].reset();
            $('#tujuan_aksi_button').text('Save');
            $('#tujuan_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Tujuan');
            $('#tujuan_aksi_button').val('Save');
            $('#tujuan_aksi').val('Save');
            $('#tujuan_form_result').html('');
        });

        $('#tujuan_form').on('submit', function(e){
            e.preventDefault();
            if($('#tujuan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#tujuan_aksi_button').text('Menyimpan...');
                        $('#tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }

            if($('#tujuan_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#tujuan_aksi_button').text('Menyimpan...');
                        $('#tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }
        });

        $('#tujuan_aksi_button').click(function(){
            var tujuan_misi_id = $('#tujuan_misi_id').val();
            var tujuan_kode = $('#tujuan_kode').val();
            var tujuan_deskripsi = $('#tujuan_deskripsi').val();
            var tujuan_tahun_perubahan = $('#tujuan_tahun_perubahan').val();
            var tujuan_hidden_id = $('#tujuan_hidden_id').val();
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

            if($('#tujuan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.store') }}",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        tujuan_misi_id:tujuan_misi_id,
                        tujuan_kode:tujuan_kode,
                        tujuan_deskripsi:tujuan_deskripsi,
                        tujuan_tahun_perubahan:tujuan_tahun_perubahan,
                        tujuan_hidden_id:tujuan_hidden_id,
                        nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                        tujuan_filter_visi:tujuan_filter_visi,
                        tujuan_filter_misi:tujuan_filter_misi,
                        tujuan_filter_tujuan:tujuan_filter_tujuan
                    },
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
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                            $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                            $('#addEditTujuanModal').modal('hide');
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }

            if($('#tujuan_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.update') }}",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        tujuan_misi_id:tujuan_misi_id,
                        tujuan_kode:tujuan_kode,
                        tujuan_deskripsi:tujuan_deskripsi,
                        tujuan_tahun_perubahan:tujuan_tahun_perubahan,
                        tujuan_hidden_id:tujuan_hidden_id,
                        nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                        tujuan_filter_visi:tujuan_filter_visi,
                        tujuan_filter_misi:tujuan_filter_misi,
                        tujuan_filter_tujuan:tujuan_filter_tujuan
                    },
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
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                            $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                            $('#addEditTujuanModal').modal('hide');
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click','.tujuan_btn_impor_template',function(){
            $('.modal-title').text('Import Data Tujuan');
            $('#importTujuanModal').modal('show');
        });

        $(document).on('click', '.detail-tujuan', function(){
            var id = $(this).attr('data-tujuan-id');
            $.ajax({
                url: "{{ url('/admin/tujuan/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_tujuan').remove();
                    $('#div_pivot_perubahan_tujuan').append('<div id="pivot_perubahan_tujuan"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#tujuan_detail_visi').val(data.result.visi);
                    $('#tujuan_detail_misi').val(data.result.misi);
                    $('#tujuan_detail_kode').val(data.result.kode);
                    $('#tujuan_detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_tujuan').append(data.result.pivot_perubahan_tujuan);
                    $('#tujuan_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#detailTujuanModal').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-tujuan', function(){
            var id = $(this).attr('data-tujuan-id');
            $('#tujuan_misi_id').val($(this).attr('data-misi-id'));
            $('#tujuan_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/tujuan/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#tujuan_kode').val(data.result.kode);
                    $('#tujuan_deskripsi').val(data.result.deskripsi);
                    $("[name='tujuan_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                    $('#tujuan_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#tujuan_aksi_button').text('Edit');
                    $('#tujuan_aksi_button').prop('disabled', false);
                    $('#tujuan_aksi_button').val('Edit');
                    $('#tujuan_aksi').val('Edit');
                    $('#addEditTujuanModal').modal('show');
                }
            });
        });

        $(document).on('click', '.tambah-tujuan-indikator-kinerja', function(){
            $('#indikator_kinerja_tujuan_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#indikatorKinerjaTujuanModal').modal('show');
        });

        $(document).on('click', '.btn-edit-tujuan-indikator-kinerja', function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/admin/tujuan/indikator-kinerja/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#edit_indikator_kinerja_tujuan_id').val(id);
                    $('#edit_indikator_kinerja_tujuan_deskripsi').val(data.result.deskripsi);
                    $('#edit_indikator_kinerja_tujuan_satuan').val(data.result.satuan);
                    $('#edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal').val(data.result.kondisi_target_kinerja_awal);
                    $('#editIndikatorKinerjaTujuanModal').modal('show');
                }
            });
        })

        $(document).on('click', '.btn-hapus-tujuan-indikator-kinerja', function(){
            var tujuan_indikator_kinerja_id = $(this).attr('data-tujuan-indikator-kinerja-id');
            var tujuan_id = $(this).attr('data-tujuan-id');
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

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
                        url: "{{ route('admin.tujuan.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tujuan_id:tujuan_id,
                            tujuan_indikator_kinerja_id: tujuan_indikator_kinerja_id,
                            nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                            tujuan_filter_visi:tujuan_filter_visi,
                            tujuan_filter_misi:tujuan_filter_misi,
                            tujuan_filter_tujuan:tujuan_filter_tujuan
                        },
                        success: function(data)
                        {
                            if(data.errors)
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.errors,
                                    showConfirmButton: true
                                });
                            }
                            if(data.success)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                });
                                $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-tujuan-target-satuan-rp-realisasi', function(){
            var tujuan_indikator_kinerja_id = $(this).attr('data-tujuan-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.tujuan-add-target.'+tahun+'.data-tujuan-indikator-kinerja-'+tujuan_indikator_kinerja_id).val();

            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('admin.tujuan.indikator.target-satuan-rp-realisasi') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            tujuan_indikator_kinerja_id:tujuan_indikator_kinerja_id,
                            target:target,
                            nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                            tujuan_filter_visi:tujuan_filter_visi,
                            tujuan_filter_misi:tujuan_filter_misi,
                            tujuan_filter_tujuan:tujuan_filter_tujuan
                        },
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
                                    icon: 'error',
                                    title: data.errors,
                                    showConfirmButton: true
                                });
                            }
                            if(data.success)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                });
                                $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-tujuan-edit-target-satuan-rp-realisasi', function(){
            var tujuan_indikator_kinerja_id = $(this).attr('data-tujuan-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var tujuan_target_satuan_rp_realisasi = $(this).attr('data-tujuan-target-satuan-rp-realisasi');
            var target = $('.tujuan-span-target.'+tahun+'.data-tujuan-indikator-kinerja-'+tujuan_indikator_kinerja_id).text();

            $('#tujuan_target_satuan_rp_realisasi').val(tujuan_target_satuan_rp_realisasi);
            $('#tujuan_edit_target').val(target);

            $('#editTargetTujuanModal').modal('show');
        });

        $(document).on('click', '.hapus-tujuan', function(){
            var tujuan_id = $(this).attr('data-tujuan-id');
            $('#hapus_tujuan_id').val(tujuan_id);
            $('#hapusTujuanModal').modal('show');
        });

        $('#indikator_kinerja_tujuan_btn').click(function(){
            var indikator_kinerja_tujuan_tujuan_id = $('#indikator_kinerja_tujuan_tujuan_id').val();
            var indikator_kinerja_tujuan_deskripsi = $('#indikator_kinerja_tujuan_deskripsi').val();
            var indikator_kinerja_tujuan_satuan = $('#indikator_kinerja_tujuan_satuan').val();
            var indikator_kinerja_tujuan_kondisi_target_kinerja_awal = $('#indikator_kinerja_tujuan_kondisi_target_kinerja_awal').val();
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();
            $.ajax({
                url: "{{ route('admin.tujuan.indikator-kinerja.tambah') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    indikator_kinerja_tujuan_tujuan_id:indikator_kinerja_tujuan_tujuan_id,
                    indikator_kinerja_tujuan_deskripsi:indikator_kinerja_tujuan_deskripsi,
                    indikator_kinerja_tujuan_satuan:indikator_kinerja_tujuan_satuan,
                    indikator_kinerja_tujuan_kondisi_target_kinerja_awal:indikator_kinerja_tujuan_kondisi_target_kinerja_awal,
                    nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                    tujuan_filter_visi:tujuan_filter_visi,
                    tujuan_filter_misi:tujuan_filter_misi,
                    tujuan_filter_tujuan:tujuan_filter_tujuan
                },
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
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                        $('#indikatorKinerjaTujuanModal').modal('hide');
                    }
                }
            });
        });

        $('#hapus_tujuan_btn').click(function(){
            var hapus_tujuan_id = $('#hapus_tujuan_id').val();
            var hapus_tujuan_tahun = $('#hapus_tujuan_tahun').val();
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

            $.ajax({
                url: "{{ route('admin.tujuan.hapus') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    hapus_tujuan_id:hapus_tujuan_id,
                    hapus_tujuan_tahun:hapus_tujuan_tahun,
                    nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                    tujuan_filter_visi:tujuan_filter_visi,
                    tujuan_filter_misi:tujuan_filter_misi,
                    tujuan_filter_tujuan:tujuan_filter_tujuan
                },
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
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                        $('#hapusTujuanModal').modal('hide');
                    }
                }
            });
        });

        $('#edit_indikator_kinerja_tujuan_btn').click(function(){
            var edit_indikator_kinerja_tujuan_id = $('#edit_indikator_kinerja_tujuan_id').val();
            var edit_indikator_kinerja_tujuan_deskripsi = $('#edit_indikator_kinerja_tujuan_deskripsi').val();
            var edit_indikator_kinerja_tujuan_satuan = $('#edit_indikator_kinerja_tujuan_satuan').val();
            var edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal = $('#edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal').val();
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

            $.ajax({
                url: "{{ route('admin.tujuan.indikator-kinerja.update') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    edit_indikator_kinerja_tujuan_id:edit_indikator_kinerja_tujuan_id,
                    edit_indikator_kinerja_tujuan_deskripsi:edit_indikator_kinerja_tujuan_deskripsi,
                    edit_indikator_kinerja_tujuan_satuan:edit_indikator_kinerja_tujuan_satuan,
                    edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal:edit_indikator_kinerja_tujuan_kondisi_target_kinerja_awal,
                    nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                    tujuan_filter_visi:tujuan_filter_visi,
                    tujuan_filter_misi:tujuan_filter_misi,
                    tujuan_filter_tujuan:tujuan_filter_tujuan
                },
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
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                        $('#editIndikatorKinerjaTujuanModal').modal('hide');
                    }
                }
            });
        });

        $('#tujuan_edit_btn').click(function(){
            var tujuan_target_satuan_rp_realisasi = $('#tujuan_target_satuan_rp_realisasi').val();
            var tujuan_edit_target = $('#tujuan_edit_target').val();
            var nav_rpjmd_tujuan_tahun = $('.navRpjmdTujuan.active').attr('data-tahun');
            var tujuan_filter_visi = $('#tujuan_filter_visi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_misi = $('#tujuan_filter_misi_'+nav_rpjmd_tujuan_tahun).val();
            var tujuan_filter_tujuan = $('#tujuan_filter_tujuan_'+nav_rpjmd_tujuan_tahun).val();

            $.ajax({
                url: "{{ route('admin.tujuan.indikator.target-satuan-rp-realisasi_update') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    tujuan_target_satuan_rp_realisasi:tujuan_target_satuan_rp_realisasi,
                    tujuan_edit_target:tujuan_edit_target,
                    nav_rpjmd_tujuan_tahun:nav_rpjmd_tujuan_tahun,
                    tujuan_filter_visi:tujuan_filter_visi,
                    tujuan_filter_misi:tujuan_filter_misi,
                    tujuan_filter_tujuan:tujuan_filter_tujuan
                },
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
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#tujuanNavDiv'+nav_rpjmd_tujuan_tahun).html(data.html);
                        $('#editTargetTujuanModal').modal('hide');
                    }
                }
            });
        });
        // Tujuan End

        // Sasaran Start
        $('#sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#sasaranNavDivsemua').html(data.html);
                }
            });
        });

        $('.navRpjmdSasaran').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/get-sasaran') }}" + '/' + tahun,
                dataType: "json",
                success:function(data)
                {
                    $('#sasaranNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('click','.sasaran_create',function(){
            $('#sasaran_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#sasaran_form')[0].reset();
            $('#sasaran_aksi_button').text('Save');
            $('#sasaran_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Sasaran');
            $('#sasaran_aksi_button').val('Save');
            $('#sasaran_aksi').val('Save');
            $('#sasaran_form_result').html('');
        });

        $('#sasaran_form').on('submit', function(e){
            e.preventDefault();
            if($('#sasaran_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_aksi_button').text('Menyimpan...');
                        $('#sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_aksi_button').prop('disabled', false);
                            $('#sasaran_form')[0].reset();
                            $('#sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#sasaran_form_result').html(html);
                    }
                });
            }

            if($('#sasaran_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_aksi_button').text('Menyimpan...');
                        $('#sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_aksi_button').prop('disabled', false);
                            $('#sasaran_form')[0].reset();
                            $('#sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#sasaran_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-sasaran', function(){
            var id = $(this).attr('data-sasaran-id');
            $.ajax({
                url: "{{ url('/admin/sasaran/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_sasaran').remove();
                    $('#div_pivot_perubahan_sasaran').append('<div id="pivot_perubahan_sasaran"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#sasaran_detail_visi').val(data.result.visi);
                    $('#sasaran_detail_misi').val(data.result.misi);
                    $('#sasaran_detail_tujuan').val(data.result.tujuan);
                    $('#sasaran_detail_kode').text(data.result.kode);
                    $('#sasaran_detail_misi_kode').text(data.result.kode_misi);
                    $('#sasaran_detail_tujuan_kode').text(data.result.kode_tujuan);
                    $('#sasaran_detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_sasaran').append(data.result.pivot_perubahan_sasaran);
                    $('#sasaran_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#detailSasaranModal').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-sasaran', function(){
            var id = $(this).attr('data-sasaran-id');
            $('#sasaran_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#sasaran_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/sasaran/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#sasaran_kode').val(data.result.kode);
                    $('#sasaran_deskripsi').val(data.result.deskripsi);
                    $("[name='sasaran_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                    $('#sasaran_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#sasaran_aksi_button').text('Edit');
                    $('#sasaran_aksi_button').prop('disabled', false);
                    $('#sasaran_aksi_button').val('Edit');
                    $('#sasaran_aksi').val('Edit');
                    $('#addEditSasaranModal').modal('show');
                }
            });
        });

        $(document).on('click','.sasaran_btn_impor_template',function(){
            $('.modal-title').text('Import Data Sasaran');
            $('#importSasaranModal').modal('show');
        });

        $(document).on('click', '.tambah-sasaran-indikator-kinerja', function(){
            $('#indikator_kinerja_sasaran_sasaran_id').val($(this).attr('data-sasaran-id'));
            $('#indikatorKinerjaSasaranModal').modal('show');
        });

        $(document).on('click', '.btn-edit-sasaran-indikator-kinerja', function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/admin/sasaran/indikator-kinerja/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#edit_indikator_kinerja_sasaran_id').val(id);
                    $('#edit_indikator_kinerja_sasaran_deskripsi').val(data.result.deskripsi);
                    $('#edit_indikator_kinerja_sasaran_satuan').val(data.result.satuan);
                    $('#edit_indikator_kinerja_sasaran_kondisi_target_kinerja_awal').val(data.result.kondisi_target_kinerja_awal);
                    $('#editIndikatorKinerjaSasaranModal').modal('show');
                }
            });
        });

        $(document).on('click', '.btn-hapus-sasaran-indikator-kinerja', function(){
            var sasaran_indikator_kinerja_id = $(this).attr('data-sasaran-indikator-kinerja-id');
            var sasaran_id = $(this).attr('data-sasaran-id');

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
                        url: "{{ route('admin.sasaran.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sasaran_id:sasaran_id,
                            sasaran_indikator_kinerja_id: sasaran_indikator_kinerja_id
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('admin.perencanaan.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sasaran-target-satuan-rp-realisasi', function(){
            var sasaran_indikator_kinerja_id = $(this).attr('data-sasaran-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.sasaran-add-target.'+tahun+'.data-sasaran-indikator-kinerja-'+sasaran_indikator_kinerja_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('admin.sasaran.indikator.target-satuan-rp-realisasi') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            sasaran_indikator_kinerja_id:sasaran_indikator_kinerja_id,
                            target:target,
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('admin.perencanaan.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sasaran-edit-target-satuan-rp-realisasi', function(){
            var sasaran_indikator_kinerja_id = $(this).attr('data-sasaran-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var sasaran_target_satuan_rp_realisasi = $(this).attr('data-sasaran-target-satuan-rp-realisasi');
            var target = $('.sasaran-span-target.'+tahun+'.data-sasaran-indikator-kinerja-'+sasaran_indikator_kinerja_id).text();

            $('#sasaran_target_satuan_rp_realisasi').val(sasaran_target_satuan_rp_realisasi);
            $('#sasaran_edit_target').val(target);

            $('#editTargetSasaranModal').modal('show');
        });

        $(document).on('click', '.hapus-sasaran', function(){
            var sasaran_id = $(this).attr('data-sasaran-id');
            $('#hapus_sasaran_id').val(sasaran_id);
            $('#hapusSasaranModal').modal('show');
        });
        // Sasaran End

        // Program Start
        var program_edit_program_id = 0;
        var program_edit_tujuan_id = 0;
        var program_edit_sasaran_id = 0;
        var program_edit_sasaran_indikator_id = 0;

        $('#program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#programNavDiv'+tahun_awal).html(data.html);
                }
            });
        });

        $('.navRpjmdProgram').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/get-program') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#programNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('#program_urusan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_program_id').empty();
                        $('#program_program_id').prop('disabled', false);
                        $('#program_program_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#program_program_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_program_id').prop('disabled', true);
            }
        });

        $('#program_misi_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_tujuan_id').empty();
                        $('#program_tujuan_id').prop('disabled', false);
                        $('#program_sasaran_id').prop('disabled', true);
                        $('#program_sasaran_indikator_id').prop('disabled', true);
                        $('#program_tujuan_id').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#program_tujuan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_tujuan_id').prop('disabled', true);
                $('#program_sasaran_id').prop('disabled', true);
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('#program_tujuan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_sasaran_id').empty();
                        $('#program_sasaran_id').prop('disabled', false);
                        $('#program_sasaran_indikator_id').prop('disabled', true);
                        $('#program_sasaran_id').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#program_sasaran_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_sasaran_id').prop('disabled', true);
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('#program_sasaran_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-sasaran-indikator') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_sasaran_indikator_id').empty();
                        $('#program_sasaran_indikator_id').prop('disabled', false);
                        $('#program_sasaran_indikator_id').append('<option value="">--- Pilih Sasaran Indikator ---</option>');
                        $.each(response, function(key, value){
                            $('#program_sasaran_indikator_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('.program_create').click(function(){
            program_edit_program_id = 0;
            program_edit_tujuan_id = 0;
            program_edit_sasaran_id = 0;
            program_edit_sasaran_indikator_id = 0;
            $('#program_form')[0].reset();
            $('#program_aksi_button').text('Save');
            $('#program_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Program');
            $('#program_aksi_button').val('Save');
            $('#program_aksi').val('Save');
            $('#program_form_result').html('');
            $("[name='program_urusan_id']").val('').trigger('change');
            $("[name='program_program_id']").val('').trigger('change');
            $("[name='program_misi_id']").val('').trigger('change');
            $("[name='program_tujuan_id']").val('').trigger('change');
            $("[name='program_sasaran_id']").val('').trigger('change');
            $("[name='program_sasaran_indikator_id[]']").val('').trigger('change');
            $('#program_filter_visi').select2('destroy');
            $('#program_filter_misi').select2('destroy');
            $('#program_filter_tujuan').select2('destroy');
            $('#program_filter_sasaran').select2('destroy');
            // $('#MyID').siblings('.select2').children('.selection').children('.select2-selection').css('border-color', 'red')
        });

        $('#addEditProgramModal').on('hidden.bs.modal', function () {
            $('#program_filter_visi').select2();
            $('#program_filter_misi').select2();
            $('#program_filter_tujuan').select2();
            $('#program_filter_sasaran').select2();

            $(".select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow").css({"position":"absolute", "top":"1px", "right":"1px", "width":"20px"})
        });

        $('#program_form').on('submit', function(e){
            e.preventDefault();
            if($('#program_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.store') }}",
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Program',
                            }).then(function() {
                                window.location.href = "{{ route('admin.perencanaan.index') }}";
                            });
                        }

                        $('#program_form_result').html(html);
                    }
                });
            }

            if($('#program_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.update') }}",
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
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Program',
                            },
                            function(){
                                location.reload();
                            });
                        }

                        $('#program_form_result').html(html);
                    }
                });
            }
        });

        $('#edit_program_form').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.program-rpjmd.update') }}",
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function()
                {
                    $('#edit_program_aksi_button').text('Menyimpan...');
                    $('#edit_program_aksi_button').prop('disabled', true);
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        html = '<div class="alert alert-danger">'+data.errors+'</div>';
                        $('#edit_program_aksi_button').prop('disabled', false);
                        $('#edit_program_form')[0].reset();
                        $('#edit_program_aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Merubah Program',
                        },
                        function(){
                            location.reload();
                        });
                    }

                    $('#edit_program_form_result').html(html);
                }
            });
        });

        $(document).on('click', '.btn-hapus-pivot-sasaran-indikator-program-rpjmd', function(){
            var pivot_sasaran_indikator_program_rpjmd_id = $(this).attr('data-pivot-sasaran-indikator-program-rpjmd-id');
            var program_rpjmd_id = $(this).attr('data-program-rpjmd-id');
            var sasaran_indikator_kinerja_id = $(this).attr('data-sasaran-indikator-kinerja-id');
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
                        url: "{{ route('admin.program-rpjmd.pivot-sasaran-indikator-program-rpmjd.delete') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            pivot_sasaran_indikator_program_rpjmd_id:pivot_sasaran_indikator_program_rpjmd_id,
                            program_rpjmd_id: program_rpjmd_id,
                            sasaran_indikator_kinerja_id : sasaran_indikator_kinerja_id
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('admin.perencanaan.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });
        // Program End

        $(document).on('click', '.button-target-rp-pertahun', function(){
            var tahun = $(this).attr('data-tahun');
            var opd_id = $(this).attr('data-opd-id');
            var program_rpjmd_id = $(this).attr('data-program-rpjmd-id');
            var target_rp_pertahun_program_id = $(this).attr('data-target-rp-pertahun-program-id');

            var target = $('.add-target.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).val();
            var satuan = $('.add-satuan.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).val();
            var rp = $('.add-rp.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('admin.program-rpjmd.detail.target-rp-pertahun') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            opd_id:opd_id,
                            program_rpjmd_id:program_rpjmd_id,
                            target:target,
                            satuan:satuan,
                            rp:rp,
                            target_rp_pertahun_program_id:target_rp_pertahun_program_id
                        },
                        dataType: "json",
                        success: function(data)
                        {
                            if(data.errors)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: data.errors,
                                    showConfirmButton: true
                                });
                            }

                            if(data.success)
                            {
                                $('#program_atur_target_rp_pertahun').html(data.success);
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-edit-target-rp-pertahun', function(){
            var tahun = $(this).attr('data-tahun');
            var opd_id = $(this).attr('data-opd-id');
            var program_rpjmd_id = $(this).attr('data-program-rpjmd-id');
            var target_rp_pertahun_program_id = $(this).attr('data-target-rp-pertahun-program-id');
            var target = $('.span-target.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).text();
            var satuan = $('.span-satuan.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).text();
            var rp = $('.span-rp.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).text();

            target_rp_pertahun = '<td><input type="number" class="form-control add-target '+tahun+' data-opd-'+opd_id+' data-program-rpjmd-'+program_rpjmd_id+'" value="'+target+'"></td>';
            target_rp_pertahun += '<td><input type="text" class="form-control add-satuan '+tahun+' data-opd-'+opd_id+' data-program-rpjmd-'+program_rpjmd_id+'" value="'+satuan+'"></td>';
            target_rp_pertahun += '<td><input type="text" class="form-control add-rp '+tahun+' data-opd-'+opd_id+' data-program-rpjmd-'+program_rpjmd_id+'" value="'+rp+'"></td>';
            target_rp_pertahun += '<td>'+tahun+'</td>';
            target_rp_pertahun += '<td>'+
                                        '<button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun" type="button" data-opd-id="'+opd_id+'" data-tahun="'+tahun+'" data-program-rpjmd-id="'+program_rpjmd_id+'" data-target-rp-pertahun-program-id='+target_rp_pertahun_program_id+'>'+
                                        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>'+
                                        '</button>'+
                                    '</td>';
            $('.tr-target-rp.'+tahun+'.data-opd-'+opd_id+'.data-program-rpjmd-'+program_rpjmd_id).html(target_rp_pertahun);
        });

        $(document).on('change', '#onOffTaggingMisi',function(){
            if($(this).prop('checked') == true)
            {
                $('.misi-tagging').show();
            } else {
                $('.misi-tagging').hide();
            }
        });

        $(document).on('change', '#onOffTaggingTujuan',function(){
            if($(this).prop('checked') == true)
            {
                $('.tujuan-tagging').show();
            } else {
                $('.tujuan-tagging').hide();
            }
        });

        $(document).on('change', '#onOffTaggingSasaran',function(){
            if($(this).prop('checked') == true)
            {
                $('.sasaran-tagging').show();
            } else {
                $('.sasaran-tagging').hide();
            }
        });

        $(document).on('change', '#onOffTaggingProgram',function(){
            if($(this).prop('checked') == true)
            {
                $('.program-tagging').show();
            } else {
                $('.program-tagging').hide();
            }
        });

        // Filter Data Misi
        $('.misi_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#misi_filter_misi_'+tahun).empty();
                        $('#misi_filter_misi_'+tahun).prop('disabled', false);
                        $('#misi_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#misi_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#misi_filter_misi_'+tahun).prop('disabled', true);
            }
        });

        $('.misi_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#misi_filter_visi_'+tahun).val();
            var misi = $('#misi_filter_misi_'+tahun).val();
            $.ajax({
                url: "{{ route('admin.perencanaan.filter.misi') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tahun: tahun,
                },
                success: function(data)
                {
                    $('#misiNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.misi_btn_reset').click(function(){
            $('#misi_filter_misi').prop('disabled', true);
            $("[name='misi_filter_visi']").val('').trigger('change');
            $("[name='misi_filter_misi']").val('').trigger('change');
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ route('admin.perencanaan.reset.misi') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    tahun:tahun
                },
                success: function(data)
                {
                    $('#misiNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Filter Data Tujuan
        $('.tujuan_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#tujuan_filter_misi_'+tahun).empty();
                        $('#tujuan_filter_misi_'+tahun).prop('disabled', false);
                        $('#tujuan_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#tujuan_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#tujuan_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#tujuan_filter_misi_'+tahun).prop('disabled', true);
                $('#tujuan_filter_tujuan_'+tahun).prop('disabled', true);
            }
        });

        $('.tujuan_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#tujuan_filter_tujuan_'+tahun).empty();
                        $('#tujuan_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#tujuan_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#tujuan_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#tujuan_filter_tujuan_'+tahun).prop('disabled', true);
            }
        });

        $('.tujuan_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#tujuan_filter_visi_'+tahun).val();
            var misi = $('#tujuan_filter_misi_'+tahun).val();
            var tujuan = $('#tujuan_filter_tujuan_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.filter.tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    tahun:tahun
                },
                success: function(data)
                {
                    $('#tujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.tujuan_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#tujuan_filter_misi_'+tahun).prop('disabled', true);
            $('#tujuan_filter_tujuan_'+tahun).prop('disabled', true);
            $('#tujuan_filter_visi_'+tahun).val('').trigger('change');
            $('#tujuan_filter_misi_'+tahun).val('').trigger('change');
            $('#tujuan_filter_tujuan_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.reset.tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#tujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Filter Data Sasaran
        $('.sasaran_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#sasaran_filter_misi_'+tahun).empty();
                        $('#sasaran_filter_misi_'+tahun).prop('disabled', false);
                        $('#sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#sasaran_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#sasaran_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#sasaran_filter_misi_'+tahun).prop('disabled', true);
                $('#sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.sasaran_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#sasaran_filter_tujuan_'+tahun).empty();
                        $('#sasaran_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#sasaran_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#sasaran_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.sasaran_filter_tujuan').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#sasaran_filter_sasaran_'+tahun).empty();
                        $('#sasaran_filter_sasaran_'+tahun).prop('disabled', false);
                        $('#sasaran_filter_sasaran_'+tahun).append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#sasaran_filter_sasaran_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.sasaran_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#sasaran_filter_visi_'+tahun).val();
            var misi = $('#sasaran_filter_misi_'+tahun).val();
            var tujuan = $('#sasaran_filter_tujuan_'+tahun).val();
            var sasaran = $('#sasaran_filter_sasaran_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.filter.sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    tahun: tahun
                },
                success: function(data)
                {
                    $('#sasaranNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.sasaran_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#sasaran_filter_misi_'+tahun).prop('disabled', true);
            $('#sasaran_filter_tujuan_'+tahun).prop('disabled', true);
            $('#sasaran_filter_sasaran_'+tahun).prop('disabled', true);
            $('#sasaran_filter_visi_'+tahun).val('').trigger('change');
            $('#sasaran_filter_misi_'+tahun).val('').trigger('change');
            $('#sasaran_filter_tujuan_'+tahun).val('').trigger('change');
            $('#sasaran_filter_sasaran_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.reset.sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#sasaranNavDiv'+tahun).html(data.html);
                }
            });
        });
        // Filter Data Program
        $('.program_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_filter_misi_'+tahun).empty();
                        $('#program_filter_misi_'+tahun).prop('disabled', false);
                        $('#program_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#program_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#program_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#program_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_filter_misi_'+tahun).prop('disabled', true);
                $('#program_filter_tujuan_'+tahun).prop('disabled', true);
                $('#program_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.program_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_filter_tujuan_'+tahun).empty();
                        $('#program_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#program_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#program_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>_'+tahun);
                        $.each(response, function(key, value){
                            $('#program_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_filter_tujuan_'+tahun).prop('disabled', true);
                $('#program_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.program_filter_tujuan').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_filter_sasaran_'+tahun).empty();
                        $('#program_filter_sasaran_'+tahun).prop('disabled', false);
                        $('#program_filter_sasaran_'+tahun).append('<option value="">--- Pilih Sasaran ---</option>_'+tahun);
                        $.each(response, function(key, value){
                            $('#program_filter_sasaran_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_filter_sasaran_'+tahun).prop('disabled', true);
            }
        });

        $('.program_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#program_filter_visi_'+tahun).val();
            var misi = $('#program_filter_misi_'+tahun).val();
            var tujuan = $('#program_filter_tujuan_'+tahun).val();
            var sasaran = $('#program_filter_sasaran_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.filter.program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    tahun:tahun
                },
                success: function(data)
                {
                    $('#programNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.program_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#program_filter_misi_'+tahun).prop('disabled', true);
            $('#program_filter_tujuan_'+tahun).prop('disabled', true);
            $('#program_filter_sasaran_'+tahun).prop('disabled', true);
            $('#program_filter_visi_'+tahun).val('').trigger('change');
            $('#program_filter_misi_'+tahun).val('').trigger('change');
            $('#program_filter_tujuan_'+tahun).val('').trigger('change');
            $('#program_filter_sasaran_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.reset.program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#programNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Renstra Tujuan
        $('#renstra_tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv'+tahun_awal).html(data.html);
                }
            });
        });

        $(document).on('click', '.btn-open-tujuan-pd', function(){
            var value = $(this).val();
            var tujuan_pd_id = $(this).attr('data-tujuan-pd-id');
            $('.btn-open-tujuan-pd.data-tujuan-pd-'+tujuan_pd_id).empty();
            if(value == 'close')
            {
                $('.btn-open-tujuan-pd.data-tujuan-pd-'+tujuan_pd_id).val('open');
                $('.btn-open-tujuan-pd.data-tujuan-pd-'+tujuan_pd_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-tujuan-pd.data-tujuan-pd-'+tujuan_pd_id).val('close');
                $('.btn-open-tujuan-pd.data-tujuan-pd-'+tujuan_pd_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $('.navRenstraTujuan').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/renstra/get-tujuan') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraTujuan',function(){
            if($(this).prop('checked') == true)
            {
                $('.tujuan-renstra-tagging').show();
            } else {
                $('.tujuan-renstra-tagging').hide();
            }
        });

        // Filter Data Renstra Tujuan
        $('.renstra_tujuan_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_misi_'+tahun).empty();
                        $('#renstra_tujuan_filter_misi_'+tahun).prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#renstra_tujuan_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_misi_'+tahun).prop('disabled', true);
                $('#renstra_tujuan_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_tujuan_filter_misi_'+tahun).val('').trigger('change');
                $('#renstra_tujuan_filter_tujuan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_tujuan_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_tujuan_'+tahun).empty();
                        $('#renstra_tujuan_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_tujuan_filter_tujuan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_tujuan_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#renstra_tujuan_filter_visi_'+tahun).val();
            var misi = $('#renstra_tujuan_filter_misi_'+tahun).val();
            var tujuan = $('#renstra_tujuan_filter_tujuan_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.filter.tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    tahun: tahun
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.renstra_tujuan_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renstra_tujuan_filter_misi_'+tahun).prop('disabled', true);
            $('#renstra_tujuan_filter_tujuan_'+tahun).prop('disabled', true);
            $('#renstra_tujuan_filter_visi_'+tahun).val('').trigger('change');
            $('#renstra_tujuan_filter_misi_'+tahun).val('').trigger('change');
            $('#renstra_tujuan_filter_tujuan_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.reset.tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Renstra Sasaran
        $('#renstra_sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.get-sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraSasaranNavDiv'+tahun_awal).html(data.html);
                }
            });
        });

        $(document).on('click', '.btn-open-sasaran-pd', function(){
            var value = $(this).val();
            var sasaran_pd_id = $(this).attr('data-sasaran-pd-id');
            $('.btn-open-sasaran-pd.data-sasaran-pd-'+sasaran_pd_id).empty();
            if(value == 'close')
            {
                $('.btn-open-sasaran-pd.data-sasaran-pd-'+sasaran_pd_id).val('open');
                $('.btn-open-sasaran-pd.data-sasaran-pd-'+sasaran_pd_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-sasaran-pd.data-sasaran-pd-'+sasaran_pd_id).val('close');
                $('.btn-open-sasaran-pd.data-sasaran-pd-'+sasaran_pd_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $('.navRenstraSasaran').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/renstra/get-sasaran') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#renstraSasaranNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraSasaran',function(){
            if($(this).prop('checked') == true)
            {
                $('.sasaran-renstra-tagging').show();
            } else {
                $('.sasaran-renstra-tagging').hide();
            }
        });

        // Filter Data Sasaran
        $('.renstra_sasaran_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_misi_'+tahun).empty();
                        $('#renstra_sasaran_filter_misi_'+tahun).prop('disabled', false);
                        $('#renstra_sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_sasaran_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_misi_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_misi_'+tahun).val('').trigger('change');
                $('#renstra_sasaran_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_sasaran_filter_sasaran_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_sasaran_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_tujuan_'+tahun).empty();
                        $('#renstra_sasaran_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_sasaran_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_sasaran_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_sasaran_filter_tujuan').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_sasaran_'+tahun).empty();
                        $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran_'+tahun).append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_sasaran_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_sasaran_filter_sasaran_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_sasaran_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#renstra_sasaran_filter_visi_'+tahun).val();
            var misi = $('#renstra_sasaran_filter_misi_'+tahun).val();
            var tujuan = $('#renstra_sasaran_filter_tujuan_'+tahun).val();
            var sasaran = $('#renstra_sasaran_filter_sasaran_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.filter.sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    tahun: tahun
                },
                success: function(data)
                {
                    $('#renstraSasaranNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.renstra_sasaran_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renstra_sasaran_filter_misi_'+tahun).prop('disabled', true);
            $('#renstra_sasaran_filter_tujuan_'+tahun).prop('disabled', true);
            $('#renstra_sasaran_filter_sasaran_'+tahun).prop('disabled', true);
            $('#renstra_sasaran_filter_visi_'+tahun).val('').trigger('change');
            $('#renstra_sasaran_filter_misi_'+tahun).val('').trigger('change');
            $('#renstra_sasaran_filter_tujuan_'+tahun).val('').trigger('change');
            $('#renstra_sasaran_filter_sasaran_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.reset.sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraSasaranNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Renstra Program
        $('#renstra_program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.get-program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraProgramNavDiv'+tahun_awal).html(data.html);
                }
            });
        });

        $(document).on('click', '.btn-open-program-indikator-kinerja', function(){
            var value = $(this).val();
            var prograrm_indikator_kinerja_id = $(this).attr('data-program-indikator-kinerja-id');
            $('.btn-open-program-indikator-kinerja.data-program-indikator-kinerja-'+prograrm_indikator_kinerja_id).empty();
            if(value == 'close')
            {
                $('.btn-open-program-indikator-kinerja.data-program-indikator-kinerja-'+prograrm_indikator_kinerja_id).val('open');
                $('.btn-open-program-indikator-kinerja.data-program-indikator-kinerja-'+prograrm_indikator_kinerja_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-program-indikator-kinerja.data-program-indikator-kinerja-'+prograrm_indikator_kinerja_id).val('close');
                $('.btn-open-program-indikator-kinerja.data-program-indikator-kinerja-'+prograrm_indikator_kinerja_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $('.navRenstraProgram').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/renstra/get-program') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#renstraProgramNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraProgram',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-program-tagging').show();
            } else {
                $('.renstra-program-tagging').hide();
            }
        });

        // Filter Data Program
        $('.renstra_program_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_misi_'+tahun).empty();
                        $('#renstra_program_filter_misi_'+tahun).prop('disabled', false);
                        $('#renstra_program_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_misi_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_misi_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_program_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_program_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_tujuan_'+tahun).empty();
                        $('#renstra_program_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_program_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_program_filter_tujuan').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_sasaran_'+tahun).empty();
                        $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', false);
                        $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_program_filter_sasaran_'+tahun).append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_sasaran_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_program_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_program_filter_sasaran').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_program_'+tahun).empty();
                        $('#renstra_program_filter_program_'+tahun).prop('disabled', false);
                        $('#renstra_program_filter_program_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_program_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_program_filter_program_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_program_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#renstra_program_filter_visi_'+tahun).val();
            var misi = $('#renstra_program_filter_misi_'+tahun).val();
            var tujuan = $('#renstra_program_filter_tujuan_'+tahun).val();
            var sasaran = $('#renstra_program_filter_sasaran_'+tahun).val();
            var program = $('#renstra_program_filter_program_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.filter.program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    program: program,
                    tahun: tahun
                },
                success: function(data)
                {
                    $('#renstraProgramNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.renstra_program_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renstra_program_filter_misi_'+tahun).prop('disabled', true);
            $('#renstra_program_filter_tujuan_'+tahun).prop('disabled', true);
            $('#renstra_program_filter_sasaran_'+tahun).prop('disabled', true);
            $('#renstra_program_filter_program_'+tahun).prop('disabled', true);
            $('#renstra_program_filter_visi_'+tahun).val('').trigger('change');
            $('#renstra_program_filter_misi_'+tahun).val('').trigger('change');
            $('#renstra_program_filter_tujuan_'+tahun).val('').trigger('change');
            $('#renstra_program_filter_sasaran_'+tahun).val('').trigger('change');
            $('#renstra_program_filter_program_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.reset.program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraProgramNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Renstra Kegiatan
        $('#renstra_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.get-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv'+tahun_awal).html(data.html);
                }
            });
        });

        $(document).on('click', '.btn-open-kegiatan-indikator-kinerja', function(){
            var value = $(this).val();
            var kegiatan_indikator_kinerja_id = $(this).attr('data-kegiatan-indikator-kinerja-id');
            $('.btn-open-kegiatan-indikator-kinerja.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).empty();
            if(value == 'close')
            {
                $('.btn-open-kegiatan-indikator-kinerja.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).val('open');
                $('.btn-open-kegiatan-indikator-kinerja.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-kegiatan-indikator-kinerja.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).val('close');
                $('.btn-open-kegiatan-indikator-kinerja.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $('.navRenstraKegiatan').click(function(){
            var tahun = $(this).attr('data-tahun');
            $.ajax({
                url: "{{ url('/admin/perencanaan/renstra/get-kegiatan') }}" + '/' + tahun,
                dataType: "json",
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraKegiatan',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-kegiatan-tagging').show();
            } else {
                $('.renstra-kegiatan-tagging').hide();
            }
        });

        $(document).on('click','.renstra_kegiatan_create',function(){
            $('#renstra_kegiatan_form')[0].reset();
            $("[name='renstra_kegiatan_kegiatan_id']").val('').trigger('change');
            $('#renstra_kegiatan_aksi_button').text('Save');
            $('#renstra_kegiatan_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Kegiatan');
            $('#renstra_kegiatan_aksi_button').val('Save');
            $('#renstra_kegiatan_aksi').val('Save');
            $('#kegiatan_renstra_form_result').html('');
            $('#renstra_kegiatan_program_rpjmd_id').val($(this).attr('data-program-rpjmd-id'));
            $.ajax({
                url: "{{ route('admin.renstra.get-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).attr('data-program-id')
                },
                success: function(response){
                    $('#renstra_kegiatan_kegiatan_id').empty();
                    $('#renstra_kegiatan_kegiatan_id').append('<option value="">--- Pilih Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#renstra_kegiatan_kegiatan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });

            $.ajax({
                url: "{{ route('admin.renstra.get-opd') }}",
                method: 'POST',
                data: {
                    "_token" : "{{ csrf_token() }}",
                    id: $(this).attr('data-program-rpjmd-id')
                },
                success: function(response)
                {
                    $('#renstra_kegiatan_opd_id').empty();
                    $('#renstra_kegiatan_opd_id').append('<option value="">--- Pilih OPD ---</option>');
                    $.each(response, function(key, value){
                        $('#renstra_kegiatan_opd_id').append(new Option(value.nama, value.id));
                    });
                }
            });
        });

        $('#renstra_kegiatan_form').on('submit', function(e){
            e.preventDefault();
            if($('#renstra_kegiatan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.renstra.kegiatan.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#renstra_kegiatan_aksi_button').text('Menyimpan...');
                        $('#renstra_kegiatan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#renstra_kegiatan_aksi_button').prop('disabled', false);
                            $('#renstra_kegiatan_form')[0].reset();
                            $('#renstra_kegiatan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Kegiatan Renstra',
                            },
                            function(){
                                location.reload();
                            });
                        }

                        $('#renstra_kegiatan_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-renstra-kegiatan', function(){
            var id = $(this).attr('data-renstra-kegiatan-id');
            $.ajax({
                url: "{{ url('/admin/renstra/kegiatan/detail') }}" + '/' +id,
                dataType: "json",
                success: function(data)
                {
                    $('#detail-title').text('Detail Renstra Kegiatan');
                    $('#renstra_kegiatan_detail_visi').val(data.result.visi);
                    $('#renstra_kegiatan_detail_misi').val(data.result.misi);
                    $('#renstra_kegiatan_detail_misi_kode').text(data.result.misi_kode);
                    $('#renstra_kegiatan_detail_tujuan').val(data.result.tujuan);
                    $('#renstra_kegiatan_detail_tujuan_kode').text(data.result.tujuan_kode);
                    $('#renstra_kegiatan_detail_sasaran').val(data.result.sasaran);
                    $('#renstra_kegiatan_detail_sasaran_kode').text(data.result.sasaran_kode);
                    $('#renstra_kegiatan_tbody_detail_sasaran_indikator').html(data.result.sasaran_indikator);
                    $('#renstra_kegiatan_detail_program').val(data.result.program);
                    $('#renstra_kegiatan_detail_program_kode').text(data.result.program_kode);
                    $('#renstra_kegiatan_detail_kegiatan').val(data.result.kegiatan);
                    $('#renstra_kegiatan_detail_kegiatan_kode').text(data.result.kegiatan_kode);
                    $('#renstra_kegiatan_atur_target_rp_pertahun').html(data.result.target_rp_pertahun);
                    $('#detailRenstraKegiatanModal').modal('show');
                }
            });
        });

        $(document).on('click', '.button-target-rp-pertahun-renstra-kegiatan', function(){
            var tahun = $(this).attr('data-tahun');
            var opd_id = $(this).attr('data-opd-id');
            var renstra_kegiatan_id = $(this).attr('data-renstra-kegiatan-id');
            var target_rp_pertahun_renstra_kegiatan_id = $(this).attr('data-target-rp-pertahun-renstra-kegiatan-id');

            var target = $('.add-target.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).val();
            var satuan = $('.add-satuan.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).val();
            var rp = $('.add-rp.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('admin.renstra.kegiatan.detail.target-rp-pertahun') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            opd_id:opd_id,
                            renstra_kegiatan_id:renstra_kegiatan_id,
                            target:target,
                            satuan:satuan,
                            rp:rp,
                            target_rp_pertahun_renstra_kegiatan_id:target_rp_pertahun_renstra_kegiatan_id
                        },
                        dataType: "json",
                        success: function(data)
                        {
                            if(data.errors)
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: data.errors,
                                    showConfirmButton: true
                                });
                            }

                            if(data.success)
                            {
                                $('#renstra_kegiatan_atur_target_rp_pertahun').html(data.success);
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-edit-target-rp-pertahun-renstra-kegiatan', function(){
            var tahun = $(this).attr('data-tahun');
            var opd_id = $(this).attr('data-opd-id');
            var renstra_kegiatan_id = $(this).attr('data-renstra-kegiatan-id');
            var target_rp_pertahun_renstra_kegiatan_id = $(this).attr('data-target-rp-pertahun-renstra-kegiatan-id');
            var target = $('.span-target.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).text();
            var satuan = $('.span-satuan.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).text();
            var rp = $('.span-rp.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).text();

            target_rp_pertahun = '<td><input type="number" class="form-control add-target '+tahun+' data-opd-'+opd_id+' data-renstra-kegiatan-'+renstra_kegiatan_id+'" value="'+target+'"></td>';
            target_rp_pertahun += '<td><input type="text" class="form-control add-satuan '+tahun+' data-opd-'+opd_id+' data-renstra-kegiatan-'+renstra_kegiatan_id+'" value="'+satuan+'"></td>';
            target_rp_pertahun += '<td><input type="text" class="form-control add-rp '+tahun+' data-opd-'+opd_id+' data-renstra-kegiatan-'+renstra_kegiatan_id+'" value="'+rp+'"></td>';
            target_rp_pertahun += '<td>'+tahun+'</td>';
            target_rp_pertahun += '<td>'+
                                        '<button class="btn btn-sm btn-icon btn-icon-only btn-outline-secondary mb-1 button-target-rp-pertahun-renstra-kegiatan" type="button" data-opd-id="'+opd_id+'" data-tahun="'+tahun+'" data-renstra-kegiatan-id="'+renstra_kegiatan_id+'" data-target-rp-pertahun-renstra-kegiatan-id='+target_rp_pertahun_renstra_kegiatan_id+'>'+
                                        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="acorn-icons acorn-icons-plus undefined"><path d="M10 17 10 3M3 10 17 10"></path></svg>'+
                                        '</button>'+
                                    '</td>';
            $('.tr-target-rp.'+tahun+'.data-opd-'+opd_id+'.data-renstra-kegiatan-'+renstra_kegiatan_id).html(target_rp_pertahun);
        });

        // Filter Data Kegiatan
        $('.renstra_kegiatan_filter_visi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_misi_'+tahun).empty();
                        $('#renstra_kegiatan_filter_misi_'+tahun).prop('disabled', false);
                        $('#renstra_kegiatan_filter_tujuan_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_misi_'+tahun).append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_misi_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_misi_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_misi_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_program_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_kegiatan_filter_misi').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_tujuan_'+tahun).empty();
                        $('#renstra_kegiatan_filter_tujuan_'+tahun).prop('disabled', false);
                        $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_tujuan_'+tahun).append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_tujuan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_tujuan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_tujuan_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_program_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_kegiatan_filter_tujuan').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_sasaran_'+tahun).empty();
                        $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', false);
                        $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_sasaran_'+tahun).append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_sasaran_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_sasaran_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_program_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_kegiatan_filter_sasaran').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_program_'+tahun).empty();
                        $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', false);
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                        $('#renstra_kegiatan_filter_program_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_program_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.program_rpjmd_id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_program_'+tahun).val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_kegiatan_filter_program').on('change', function(){
            var tahun = $(this).attr('data-tahun');
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-kegiatan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).empty();
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', false);
                        $('#renstra_kegiatan_filter_kegiatan_'+tahun).append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_kegiatan_'+tahun).append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            }
        });

        $('.renstra_kegiatan_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#renstra_kegiatan_filter_visi_'+tahun).val();
            var misi = $('#renstra_kegiatan_filter_misi_'+tahun).val();
            var tujuan = $('#renstra_kegiatan_filter_tujuan_'+tahun).val();
            var sasaran = $('#renstra_kegiatan_filter_sasaran_'+tahun).val();
            var program = $('#renstra_kegiatan_filter_program_'+tahun).val();
            var kegiatan = $('#renstra_kegiatan_filter_kegiatan_'+tahun).val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.filter.kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    program: program,
                    kegiatan: kegiatan,
                    tahun: tahun
                },
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv'+tahun).html(data.html);
                }
            });
        });

        $('.renstra_kegiatan_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renstra_kegiatan_filter_misi_'+tahun).prop('disabled', true);
            $('#renstra_kegiatan_filter_tujuan_'+tahun).prop('disabled', true);
            $('#renstra_kegiatan_filter_sasaran_'+tahun).prop('disabled', true);
            $('#renstra_kegiatan_filter_program_'+tahun).prop('disabled', true);
            $('#renstra_kegiatan_filter_kegiatan_'+tahun).prop('disabled', true);
            $('#renstra_kegiatan_filter_visi_'+tahun).val('').trigger('change');
            $('#renstra_kegiatan_filter_misi_'+tahun).val('').trigger('change');
            $('#renstra_kegiatan_filter_tujuan_'+tahun).val('').trigger('change');
            $('#renstra_kegiatan_filter_sasaran_'+tahun).val('').trigger('change');
            $('#renstra_kegiatan_filter_program_'+tahun).val('').trigger('change');
            $('#renstra_kegiatan_filter_kegiatan_'+tahun).val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renstra.reset.program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Filter Program
        $('input[type=radio].status_program_option').change(function() {
            var tahun = $('.navRpjmdProgram.active').attr('data-tahun');
            $.ajax({
                url: "{{ route('admin.perencanaan.rpjmd.filter.program.status') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    tahun:tahun,
                    value:$(this).val()
                },
                success: function(data)
                {
                    $('#programNavDiv'+tahun).html(data.html);
                }
            });
        });

        // Renja Tujuan Pd
        $('#renja_tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });
        });

        // Filter Renja Data Tujuan
        $('.renja_tujuan_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_tujuan_filter_misi').empty();
                        $('#renja_tujuan_filter_misi').prop('disabled', false);
                        $('#renja_tujuan_filter_tujuan').prop('disabled', true);
                        $('#renja_tujuan_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_tujuan_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_tujuan_filter_misi_').prop('disabled', true);
                $('#renja_tujuan_filter_tujuan_').prop('disabled', true);
            }
        });

        $('.renja_tujuan_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_tujuan_filter_tujuan').empty();
                        $('#renja_tujuan_filter_tujuan').prop('disabled', false);
                        $('#renja_tujuan_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_tujuan_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_tujuan_filter_tujuan').prop('disabled', true);
            }
        });

        $('.renja_tujuan_btn_filter').click(function(){
            var tahun = $(this).attr('data-tahun');
            var visi = $('#renja_tujuan_filter_visi').val();
            var misi = $('#renja_tujuan_filter_misi').val();
            var tujuan = $('#renja_tujuan_filter_tujuan').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-tujuan.filter') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    tahun:tahun
                },
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });
        });

        $('.renja_tujuan_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renja_tujuan_filter_misi').prop('disabled', true);
            $('#renja_tujuan_filter_tujuan').prop('disabled', true);
            $('#renja_tujuan_filter_visi').val('').trigger('change');
            $('#renja_tujuan_filter_misi').val('').trigger('change');
            $('#renja_tujuan_filter_tujuan').val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-tujuan.reset') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });
        });

        // Renja Sasaran PD
        $('#renja_sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaSasaranNavDiv').html(data.html)
                }
            });
        });

        $('.renja_sasaran_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_sasaran_filter_misi').empty();
                        $('#renja_sasaran_filter_misi').prop('disabled', false);
                        $('#renja_sasaran_filter_tujuan').prop('disabled', true);
                        $('#renja_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renja_sasaran_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_sasaran_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_sasaran_filter_misi').prop('disabled', true);
                $('#renja_sasaran_filter_tujuan').prop('disabled', true);
                $('#renja_sasaran_filter_sasaran').prop('disabled', true);
                $('#renja_sasaran_filter_misi').val('').trigger('change');
                $('#renja_sasaran_filter_tujuan').val('').trigger('change');
                $('#renja_sasaran_filter_sasaran').val('').trigger('change');
            }
        });

        $('.renja_sasaran_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_sasaran_filter_tujuan').empty();
                        $('#renja_sasaran_filter_tujuan').prop('disabled', false);
                        $('#renja_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renja_sasaran_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_sasaran_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_sasaran_filter_tujuan').prop('disabled', true);
                $('#renja_sasaran_filter_tujuan').val('').trigger('change');
                $('#renja_sasaran_filter_sasaran').prop('disabled', true);
                $('#renja_sasaran_filter_sasaran').val('').trigger('change');
            }
        });

        $('.renja_sasaran_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_sasaran_filter_sasaran').empty();
                        $('#renja_sasaran_filter_sasaran').prop('disabled', false);
                        $('#renja_sasaran_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_sasaran_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_sasaran_filter_sasaran').prop('disabled', true);
                $('#renja_sasaran_filter_sasaran').val('').trigger('change');
            }
        });

        $('.renja_sasaran_btn_filter').click(function(){
            var visi = $('#renja_sasaran_filter_visi').val();
            var misi = $('#renja_sasaran_filter_misi').val();
            var tujuan = $('#renja_sasaran_filter_tujuan').val();
            var sasaran = $('#renja_sasaran_filter_sasaran').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-sasaran.filter') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran
                },
                success: function(data)
                {
                    $('#renjaSasaranNavDiv').html(data.html);
                }
            });
        });

        $('.renja_sasaran_btn_reset').click(function(){
            var tahun = $(this).attr('data-tahun');
            $('#renja_sasaran_filter_misi').prop('disabled', true);
            $('#renja_sasaran_filter_tujuan').prop('disabled', true);
            $('#renja_sasaran_filter_sasaran').prop('disabled', true);
            $('#renja_sasaran_filter_visi').val('').trigger('change');
            $('#renja_sasaran_filter_misi').val('').trigger('change');
            $('#renja_sasaran_filter_tujuan').val('').trigger('change');
            $('#renja_sasaran_filter_sasaran').val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-sasaran.reset') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renjaSasaranNavDiv').html(data.html);
                }
            });
        });

        // Renja Program
        $('#renja_program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaProgramNavDiv').html(data.html)
                }
            });
        });

        $('.renja_program_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_program_filter_misi').empty();
                        $('#renja_program_filter_misi').prop('disabled', false);
                        $('#renja_program_filter_tujuan').prop('disabled', true);
                        $('#renja_program_filter_sasaran').prop('disabled', true);
                        $('#renja_program_filter_program').prop('disabled', true);
                        $('#renja_program_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_program_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_program_filter_misi').prop('disabled', true);
                $('#renja_program_filter_tujuan').prop('disabled', true);
                $('#renja_program_filter_sasaran').prop('disabled', true);
                $('#renja_program_filter_program').prop('disabled', true);
                $('#renja_program_filter_misi').val('').trigger('change');
                $('#renja_program_filter_tujuan').val('').trigger('change');
                $('#renja_program_filter_sasaran').val('').trigger('change');
                $('#renja_program_filter_program').val('').trigger('change');
            }
        });

        $('.renja_program_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_program_filter_tujuan').empty();
                        $('#renja_program_filter_tujuan').prop('disabled', false);
                        $('#renja_program_filter_sasaran').prop('disabled', true);
                        $('#renja_program_filter_program').prop('disabled', true);
                        $('#renja_program_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_program_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_program_filter_tujuan').prop('disabled', true);
                $('#renja_program_filter_tujuan').val('').trigger('change');
                $('#renja_program_filter_sasaran').prop('disabled', true);
                $('#renja_program_filter_sasaran').val('').trigger('change');
                $('#renja_program_filter_program').prop('disabled', true);
                $('#renja_program_filter_program').val('').trigger('change');
            }
        });

        $('.renja_program_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_program_filter_sasaran').empty();
                        $('#renja_program_filter_sasaran').prop('disabled', false);
                        $('#renja_program_filter_program').prop('disabled', true);
                        $('#renja_program_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_program_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_program_filter_sasaran').prop('disabled', true);
                $('#renja_program_filter_sasaran').val('').trigger('change');
                $('#renja_program_filter_program').prop('disabled', true);
                $('#renja_program_filter_program').val('').trigger('change');
            }
        });

        $('.renja_program_filter_sasaran').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_program_filter_program').empty();
                        $('#renja_program_filter_program').prop('disabled', false);
                        $('#renja_program_filter_program').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_program_filter_program').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_program_filter_program').prop('disabled', true);
                $('#renja_program_filter_program').val('').trigger('change');
            }
        });

        $('.renja_program_btn_filter').click(function(){
            var visi = $('#renja_program_filter_visi').val();
            var misi = $('#renja_program_filter_misi').val();
            var tujuan = $('#renja_program_filter_tujuan').val();
            var sasaran = $('#renja_program_filter_sasaran').val();
            var program = $('#renja_program_filter_program').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-program.filter') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    program: program
                },
                success: function(data)
                {
                    $('#renjaProgramNavDiv').html(data.html);
                }
            });
        });

        $('.renja_program_btn_reset').click(function(){
            $('#renja_program_filter_misi').prop('disabled', true);
            $('#renja_program_filter_tujuan').prop('disabled', true);
            $('#renja_program_filter_sasaran').prop('disabled', true);
            $('#renja_program_filter_program').prop('disabled', true);
            $('#renja_program_filter_visi').val('').trigger('change');
            $('#renja_program_filter_misi').val('').trigger('change');
            $('#renja_program_filter_tujuan').val('').trigger('change');
            $('#renja_program_filter_sasaran').val('').trigger('change');
            $('#renja_program_filter_program').val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-program.reset') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renjaProgramNavDiv').html(data.html);
                }
            });
        });

        // Renja Kegiatan
        $('#renja_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaKegiatanNavDiv').html(data.html)
                }
            });
        });

        $('.renja_kegiatan_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_kegiatan_filter_misi').empty();
                        $('#renja_kegiatan_filter_misi').prop('disabled', false);
                        $('#renja_kegiatan_filter_tujuan').prop('disabled', true);
                        $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
                        $('#renja_kegiatan_filter_program').prop('disabled', true);
                        $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renja_kegiatan_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_kegiatan_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_kegiatan_filter_misi').prop('disabled', true);
                $('#renja_kegiatan_filter_tujuan').prop('disabled', true);
                $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
                $('#renja_kegiatan_filter_program').prop('disabled', true);
                $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                $('#renja_kegiatan_filter_misi').val('').trigger('change');
                $('#renja_kegiatan_filter_tujuan').val('').trigger('change');
                $('#renja_kegiatan_filter_sasaran').val('').trigger('change');
                $('#renja_kegiatan_filter_program').val('').trigger('change');
                $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            }
        });

        $('.renja_kegiatan_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_kegiatan_filter_tujuan').empty();
                        $('#renja_kegiatan_filter_tujuan').prop('disabled', false);
                        $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
                        $('#renja_kegiatan_filter_program').prop('disabled', true);
                        $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renja_kegiatan_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_kegiatan_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_kegiatan_filter_tujuan').prop('disabled', true);
                $('#renja_kegiatan_filter_tujuan').val('').trigger('change');
                $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
                $('#renja_kegiatan_filter_sasaran').val('').trigger('change');
                $('#renja_kegiatan_filter_program').prop('disabled', true);
                $('#renja_kegiatan_filter_program').val('').trigger('change');
                $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            }
        });

        $('.renja_kegiatan_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_kegiatan_filter_sasaran').empty();
                        $('#renja_kegiatan_filter_sasaran').prop('disabled', false);
                        $('#renja_kegiatan_filter_program').prop('disabled', true);
                        $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renja_kegiatan_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_kegiatan_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
                $('#renja_kegiatan_filter_sasaran').val('').trigger('change');
                $('#renja_kegiatan_filter_program').prop('disabled', true);
                $('#renja_kegiatan_filter_program').val('').trigger('change');
                $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            }
        });

        $('.renja_kegiatan_filter_sasaran').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_kegiatan_filter_program').empty();
                        $('#renja_kegiatan_filter_program').prop('disabled', false);
                        $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renja_kegiatan_filter_program').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_kegiatan_filter_program').append(new Option(value.kode +'. '+value.deskripsi, value.program_rpjmd_id));
                        });
                    }
                });
            } else {
                $('#renja_kegiatan_filter_program').prop('disabled', true);
                $('#renja_kegiatan_filter_program').val('').trigger('change');
                $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            }
        });

        $('.renja_kegiatan_filter_program').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.perencanaan.filter.get-kegiatan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renja_kegiatan_filter_kegiatan').empty();
                        $('#renja_kegiatan_filter_kegiatan').prop('disabled', false);
                        $('#renja_kegiatan_filter_kegiatan').append('<option value="">--- Pilih Kegiatan ---</option>');
                        $.each(response, function(key, value){
                            $('#renja_kegiatan_filter_kegiatan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
                $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            }
        });

        $('.renja_kegiatan_btn_filter').click(function(){
            var visi = $('#renja_kegiatan_filter_visi').val();
            var misi = $('#renja_kegiatan_filter_misi').val();
            var tujuan = $('#renja_kegiatan_filter_tujuan').val();
            var sasaran = $('#renja_kegiatan_filter_sasaran').val();
            var program = $('#renja_kegiatan_filter_program').val();
            var kegiatan = $('#renja_kegiatan_filter_kegiatan').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-kegiatan.filter') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    program: program,
                    kegiatan: kegiatan
                },
                success: function(data)
                {
                    $('#renjaKegiatanNavDiv').html(data.html);
                }
            });
        });

        $('.renja_kegiatan_btn_reset').click(function(){
            $('#renja_kegiatan_filter_misi').prop('disabled', true);
            $('#renja_kegiatan_filter_tujuan').prop('disabled', true);
            $('#renja_kegiatan_filter_sasaran').prop('disabled', true);
            $('#renja_kegiatan_filter_program').prop('disabled', true);
            $('#renja_kegiatan_filter_kegiatan').prop('disabled', true);
            $('#renja_kegiatan_filter_visi').val('').trigger('change');
            $('#renja_kegiatan_filter_misi').val('').trigger('change');
            $('#renja_kegiatan_filter_tujuan').val('').trigger('change');
            $('#renja_kegiatan_filter_sasaran').val('').trigger('change');
            $('#renja_kegiatan_filter_program').val('').trigger('change');
            $('#renja_kegiatan_filter_kegiatan').val('').trigger('change');
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-kegiatan.reset') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renjaKegiatanNavDiv').html(data.html);
                }
            });
        });

        // Renja Sub Kegiatan
        $('#renja_sub_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.renja.get-sub-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaSubKegiatanNavDiv').html(data.html)
                }
            });
        });

        $(document).on('click', '.btn-open-sub-kegiatan-indikator-kinerja', function(){
            var value = $(this).val();
            var sub_kegiatan_indikator_kinerja_id = $(this).attr('data-sub-kegiatan-indikator-kinerja-id');
            $('.btn-open-sub-kegiatan-indikator-kinerja.data-sub-kegiatan-indikator-kinerja-'+sub_kegiatan_indikator_kinerja_id).empty();
            if(value == 'close')
            {
                $('.btn-open-sub-kegiatan-indikator-kinerja.data-sub-kegiatan-indikator-kinerja-'+sub_kegiatan_indikator_kinerja_id).val('open');
                $('.btn-open-sub-kegiatan-indikator-kinerja.data-sub-kegiatan-indikator-kinerja-'+sub_kegiatan_indikator_kinerja_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-sub-kegiatan-indikator-kinerja.data-sub-kegiatan-indikator-kinerja-'+sub_kegiatan_indikator_kinerja_id).val('close');
                $('.btn-open-sub-kegiatan-indikator-kinerja.data-sub-kegiatan-indikator-kinerja-'+sub_kegiatan_indikator_kinerja_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });
    </script>
@endsection
