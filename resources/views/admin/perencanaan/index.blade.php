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
                <a class="nav-link" data-bs-toggle="tab" href="#rkpdTab" role="tab" aria-selected="false"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RKPD</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#renjaTab" role="tab" aria-selected="false"><i data-acorn-icon="file-text" class="icon" data-acorn-size="18"></i> RENJA</a>
            </li>
        </ul>

        <div class="card mb-5">
            <div class="card-body">
                <div class="tab-content">
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
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#programNav" role="tab" aria-selected="false" type="button" id="misi_tab_button">
                                        Program
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                {{-- Visi Start --}}
                                <div class="tab-pane fade active show" id="visiNav" role="tabpanel">
                                    <div class="row mb-3">
                                        <div class="col-12" style="text-align: right">
                                            <button class="btn btn-outline-primary waves-effect waves-light mr-2" id="visi_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditVisiModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>

                                    <div class="data-table-rows slim">
                                        <!-- Table Start -->
                                        <div class="data-table-responsive-wrapper">
                                            <table id="visi_table" class="data-table w-100">
                                                <thead>
                                                    <tr>
                                                        <th class="text-muted text-small text-uppercase" width="10%">No</th>
                                                        <th class="text-muted text-small text-uppercase" width="55%">Visi</th>
                                                        <th class="text-muted text-small text-uppercase" width="15%">Tahun Perubahan</th>
                                                        <th class="text-muted text-small text-uppercase" width="20%">Aksi</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <!-- Table End -->
                                    </div>
                                </div>
                                {{-- Visi End --}}

                                {{-- Misi Start --}}
                                <div class="tab-pane fade" id="misiNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="misiLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                                {{-- Misi End --}}

                                {{-- Tujuan Start --}}
                                <div class="tab-pane fade" id="tujuanNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="tujuanLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                                {{-- Tujuan End --}}

                                {{-- Sasaran Start --}}
                                <div class="tab-pane fade" id="sasaranNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="sasaranLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                                {{-- Sasaran End --}}
                                <div class="tab-pane fade" id="programNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="programLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="renstraTab" role="tabpanel">
                        <h5 class="card-title">Second Line Title</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                    <div class="tab-pane fade" id="rkpdTab" role="tabpanel">
                        <h5 class="card-title">Third Line Title</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                    <div class="tab-pane fade" id="renjaTab" role="tabpanel">
                        <h5 class="card-title">Fourth Line Title</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
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
                    <button type="submit" class="btn btn-primary" name="misi_aksi_button" id="misi_aksi_button">Add</button>
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
                                <label for="" class="form-label">Deskripsi</label>
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
                    <button type="submit" class="btn btn-primary" name="tujuan_aksi_button" id="tujuan_aksi_button">Add</button>
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
                        <input type="hidden" name="tujuan_impor_misi_id" id="tujuan_impor_misi_id">
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
                        <input type="hidden" name="sasaran_impor_tujuan_id" id="sasaran_impor_tujuan_id">
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
    {{-- Sasaran End --}}
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
        // Visi Start
        $(document).ready(function(){
            $('.dropify').dropify();
            $('.dropify-wrapper').css('line-height', '3rem');

            var dataTables = $('#visi_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.visi.index') }}",
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
                            $('#visi_form')[0].reset();
                            $('#visi_aksi_button').text('Save');
                            $('#visi_table').DataTable().ajax.reload();
                        }
                        if(data.success)
                        {
                            html = '<div class="alert alert-success">'+data.success+'</div>';
                            $('#visi_aksi_button').prop('disabled', false);
                            $('#visi_form')[0].reset();
                            $('#visi_aksi_button').text('Save');
                            $('#visi_table').DataTable().ajax.reload();
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
                            // html = '<div class="alert alert-success">'+ data.success +'</div>';
                            $('#visi_form')[0].reset();
                            $('#visi_aksi_button').prop('disabled', false);
                            $('#visi_aksi_button').text('Save');
                            $('#visi_table').DataTable().ajax.reload();
                            $('#addEditVisiModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil di ubah',
                                showConfirmButton: true
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
                beforeSend: function()
                {
                    $('#misiLoading').show();
                },
                success: function(data)
                {
                    $('#misiLoading').hide();
                    $('#misiNav').html(data.html);
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

        $('#misi_form').on('submit', function(e){
            e.preventDefault();
            if($('#misi_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.misi.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#misi_aksi_button').text('Menyimpan...');
                        $('#misi_aksi_button').prop('disabled', true);
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
                            $('#addEditMisiModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Misi',
                                showConfirmButton: true
                            });
                            $('#misiNav').html(data.success);
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
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#misi_aksi_button').text('Menyimpan...');
                        $('#misi_aksi_button').prop('disabled', true);
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
                            $('#addEditMisiModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Misi',
                                showConfirmButton: true
                            });
                            $('#misiNav').html(data.success);
                        }

                        $('#misi_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-misi', function(){
            var id = $(this).attr('data-misi-id');
            $.ajax({
                url: "{{ url('/admin/misi/detail') }}"+'/'+id,
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
            $('#misi_visi_id').val($(this).attr('data-visi-id'));
            $('#misi_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/misi/edit') }}"+'/'+id,
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
                beforeSend: function()
                {
                    $('#tujuanLoading').show();
                },
                success: function(data)
                {
                    $('#tujuanLoading').hide();
                    $('#tujuanNav').html(data.html);
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
                            $('#addEditTujuanModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Tujuan',
                                showConfirmButton: true
                            });
                            $('#tujuanNav').html(data.success);
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
                            $('#addEditTujuanModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Tujuan',
                                showConfirmButton: true
                            });
                            $('#tujuanNav').html(data.success);
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click','.tujuan_btn_impor_template',function(){
            $('#tujuan_impor_misi_id').val($(this).attr('data-misi-id'));
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
        // Tujuan End

        // Sasaran Start
        $('#sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-sasaran') }}",
                dataType: "json",
                beforeSend: function()
                {
                    $('#sasaranLoading').show();
                },
                success: function(data)
                {
                    $('#sasaranLoading').hide();
                    $('#sasaranNav').html(data.html);
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
                            $('#addEditSasaranModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Sasaran',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
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
                            $('#addEditSasaranModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Sasaran',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
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
            $('#sasaran_impor_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#importSasaranModal').modal('show');
        });

        // Sasaran End
    </script>
@endsection
