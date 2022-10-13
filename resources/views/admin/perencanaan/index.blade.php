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
                                    <div class="data-table-rows slim" id="tujuan_div_table">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="85%">Visi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi1" class="accordion-toggle"></td>
                                                        <td data-bs-toggle="collapse" data-bs-target="#tujuan_visi1" class="accordion-toggle">
                                                            TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK
                                                            <br>
                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="hiddenRow">
                                                            <div class="accordian-body collapse" id="tujuan_visi1">
                                                                <table class="table table-striped">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi1" class="accordion-toggle">1</td>
                                                                            <td width="50%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi1" class="accordion-toggle">
                                                                                Mewujudkan rasa aman bagi seluruh masyarakat dan aparatur pemerintah Kabupaten Madiun
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                <span class="badge bg-warning text-uppercase">1 Misi</span>
                                                                            </td>
                                                                            <td width="15%" data-bs-toggle="collapse" data-bs-target="#tujuan_misi1" class="accordion-toggle">2020</td>
                                                                            <td>
                                                                                <button class="btn btn-primary waves-effect waves-light mr-2 tujuan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-misi-id="1"><i class="fas fa-plus"></i></button>
                                                                                <a class="btn btn-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_tujuan.xlsx') }}" title="Download Template Import Data Tujuan"><i class="fas fa-file-excel"></i></a>
                                                                                <button class="btn btn-info waves-effect waves-light tujuan_btn_impor_template" title="Import Data Tujuan" type="button" data-misi-id="1"><i class="fas fa-file-import"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="accordian-body collapse" id="tujuan_misi1">
                                                                                    <table class="table table-striped">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td width="15%">1</td>
                                                                                                <td width="50%">
                                                                                                    Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal
                                                                                                    <br>
                                                                                                    <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                    <span class="badge bg-warning text-uppercase">1 Misi</span>
                                                                                                    <span class="badge bg-secondary text-uppercase">1 Tujuan</span>
                                                                                                </td>
                                                                                                <td width="15%">2020</td>
                                                                                                <td width="20%">
                                                                                                    <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-tujuan" data-tujuan-id="1" type="button" title="Detail Tujuan"><i class="fas fa-eye"></i></button>
                                                                                                    <button class="btn btn-icon btn-warning waves-effect waves-light edit-tujuan" data-tujuan-id="1" data-misi-id="1" type="button" title="Edit Tujuan"><i class="fas fa-edit"></i></button>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Tujuan End --}}

                                {{-- Sasaran Start --}}
                                <div class="tab-pane fade" id="sasaranNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="sasaranLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                    <div class="data-table-rows slim" id="sasaran_div_table">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="85%">Visi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi1" class="accordion-toggle"></td>
                                                        <td data-bs-toggle="collapse" data-bs-target="#sasaran_visi1" class="accordion-toggle">
                                                            TERWUJUDNYA KABUPATEN MADIUN AMAN, MANDIRI, SEJAHTERA DAN BERAKHLAK
                                                            <br>
                                                            <span class="badge bg-primary text-uppercase">Visi</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" class="hiddenRow">
                                                            <div class="accordian-body collapse" id="sasaran_visi1">
                                                                <table class="table table-striped">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi1" class="accordion-toggle">1</td>
                                                                            <td width="70%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi1" class="accordion-toggle">
                                                                                Mewujudkan rasa aman bagi seluruh masyarakat dan aparatur pemerintah Kabupaten Madiun
                                                                                <br>
                                                                                <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                <span class="badge bg-warning text-uppercase">1 Misi</span>
                                                                            </td>
                                                                            <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_misi1" class="accordion-toggle">2020</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td colspan="4" class="hiddenRow">
                                                                                <div class="accordian-body collapse" id="sasaran_misi1">
                                                                                    <table class="table table-striped">
                                                                                        <tbody>
                                                                                            <tr>
                                                                                                <td data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan1" class="accordion-toggle" width="15%">1</td>
                                                                                                <td width="50%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan1" class="accordion-toggle">
                                                                                                    Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal
                                                                                                    <br>
                                                                                                    <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                    <span class="badge bg-warning text-uppercase">1 Misi</span>
                                                                                                    <span class="badge bg-secondary text-uppercase">1 Tujuan</span>
                                                                                                </td>
                                                                                                <td width="15%" data-bs-toggle="collapse" data-bs-target="#sasaran_tujuan1" class="accordion-toggle">2020</td>
                                                                                                <td>
                                                                                                    <button class="btn btn-primary waves-effect waves-light mr-2 sasaran_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditMisiModal" title="Tambah Data Misi" data-misi-id="1"><i class="fas fa-plus"></i></button>
                                                                                                    <a class="btn btn-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_sasaran.xlsx') }}" title="Download Template Import Data Sasaran"><i class="fas fa-file-excel"></i></a>
                                                                                                    <button class="btn btn-info waves-effect waves-light sasaran_btn_impor_template" title="Import Data Sasaran" type="button" data-misi-id="1"><i class="fas fa-file-import"></i></button>
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td colspan="4" class="hiddenRow">
                                                                                                    <div class="accordian-body collapse" id="sasaran_tujuan1">
                                                                                                        <table class="table table-striped">
                                                                                                            <tbody>
                                                                                                                <tr>
                                                                                                                    <td width="15%">1</td>
                                                                                                                    <td width="50%">
                                                                                                                        Membangun Harmonisasi Sosial yang berpondasi dari nilai religius dan Kearifan Lokal
                                                                                                                        <br>
                                                                                                                        <span class="badge bg-primary text-uppercase">Visi</span>
                                                                                                                        <span class="badge bg-warning text-uppercase">1 Misi</span>
                                                                                                                        <span class="badge bg-secondary text-uppercase">1 Tujuan</span>
                                                                                                                        <span class="badge bg-danger text-uppercase">1 Sasaran</span>
                                                                                                                    </td>
                                                                                                                    <td width="15%">2020</td>
                                                                                                                    <td width="20%">
                                                                                                                        <button class="btn btn-icon btn-info waves-effect waves-light mr-1 detail-sasaran" data-sasaran-id="1" type="button" title="Detail Sasaran"><i class="fas fa-eye"></i></button>
                                                                                                                        <button class="btn btn-icon btn-warning waves-effect waves-light edit-sasaran" data-sasaran-id="1" data-tujuan-id="1" type="button" title="Edit Sasaran"><i class="fas fa-edit"></i></button>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </div>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
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

        // Tujuan End
    </script>
@endsection
