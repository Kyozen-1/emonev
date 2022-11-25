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
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-pills responsive-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="renstra_misi_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_misi_pd" role="tab" aria-selected="true" type="button">Misi</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renstra_tujuan_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_tujuan_pd" role="tab" aria-selected="true" type="button">Tujuan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renstra_sasaran_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_sasaran_pd" role="tab" aria-selected="false" type="button">Sasaran</button>
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
                    {{-- Misi Start --}}
                    <div class="tab-pane fade active show" id="renstra_misi_pd" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_misi_filter_visi" id="renstra_misi_filter_visi" class="form-control">
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
                                    <select name="renstra_misi_filter_misi" id="renstra_misi_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_misi_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_misi_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraMisi" checked>
                                            <label class="form-check-label" for="onOffTaggingRenstraMisi">On / Off Tagging</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraMisiNavDiv"></div>
                    </div>
                    {{-- Misi End --}}
                    {{-- Tujuan Start --}}
                    <div class="tab-pane fade" id="renstra_tujuan_pd" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_tujuan_filter_visi" id="renstra_tujuan_filter_visi" class="form-control">
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
                                    <select name="renstra_tujuan_filter_misi" id="renstra_tujuan_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_tujuan_filter_tujuan" id="renstra_tujuan_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_tujuan_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_tujuan_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraTujuan" checked>
                                            <label class="form-check-label" for="onOffTaggingTujuan">On / Off Tagging</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraTujuanNavDiv"></div>
                    </div>
                    {{-- Tujuan End --}}
                    {{-- Sasaran Start --}}
                    <div class="tab-pane fade" id="renstra_sasaran_pd" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_sasaran_filter_visi" id="renstra_sasaran_filter_visi" class="form-control">
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
                                    <select name="renstra_sasaran_filter_misi" id="renstra_sasaran_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_sasaran_filter_tujuan" id="renstra_sasaran_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_sasaran_filter_sasaran" id="renstra_sasaran_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_sasaran_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_sasaran_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraSasaran" checked>
                                            <label class="form-check-label" for="onOffTaggingRenstraSasaran">On / Off Tagging</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraSasaranNavDiv"></div>
                    </div>
                    {{-- Sasaran End --}}
                    {{-- Program Start --}}
                    <div class="tab-pane fade" id="renstra_program" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_program_filter_visi" id="renstra_program_filter_visi" class="form-control">
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
                                    <select name="renstra_program_filter_misi" id="renstra_program_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_program_filter_tujuan" id="renstra_program_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_program_filter_sasaran" id="renstra_program_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Program</label>
                                    <select name="renstra_program_filter_program" id="renstra_program_filter_program" class="form-control" disabled>
                                        <option value="">--- Pilih Program ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_program_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_program_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="onOffTaggingRenstraProgram" checked>
                                            <label class="form-check-label" for="onOffTaggingRenstraProgram">On / Off Tagging</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraProgramNavDiv"></div>
                    </div>
                    {{-- Program End --}}
                    {{-- Kegiatan Start --}}
                    <div class="tab-pane fade" id="renstra_kegiatan" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_kegiatan_filter_visi" id="renstra_kegiatan_filter_visi" class="form-control">
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
                                    <select name="renstra_kegiatan_filter_misi" id="renstra_kegiatan_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_kegiatan_filter_tujuan" id="renstra_kegiatan_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_kegiatan_filter_sasaran" id="renstra_kegiatan_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Program</label>
                                    <select name="renstra_kegiatan_filter_program" id="renstra_kegiatan_filter_program" class="form-control" disabled>
                                        <option value="">--- Pilih Program ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Kegiatan</label>
                                    <select name="renstra_kegiatan_filter_kegiatan" id="renstra_kegiatan_filter_kegiatan" class="form-control" disabled>
                                        <option value="">--- Pilih Kegiatan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1 mb-2" type="button" id="renstra_kegiatan_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_kegiatan_btn_reset">Reset</button>
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
                        <div id="renstraKegiatanNavDiv"></div>
                    </div>
                    {{-- Kegiatan End --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Tujuan PD Start --}}
    <div id="tambahTujuanPDModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tambahTujuanPDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Tujuan PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tujuan-pd.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tambah_tujuan_pd_tujuan_id" id="tambah_tujuan_pd_tujuan_id">
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_tujuan_pd_kode" class="form-label">Kode</label>
                            <input type="number" class="form-control" id="tambah_tujuan_pd_kode" name="tambah_tujuan_pd_kode" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_tujuan_pd_deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="tambah_tujuan_pd_deskripsi" id="tambah_tujuan_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_tujuan_pd_tahun_perubahan" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="tambah_tujuan_pd_tahun_perubahan" name="tambah_tujuan_pd_tahun_perubahan" required>
                        </div>
                        <div class="form-group position-relative" style="text-align: right">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTujuanPDModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTujuanPDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Tujuan PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tujuan-pd.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_tujuan_pd_tujuan_pd_id" id="edit_tujuan_pd_tujuan_pd_id">
                        <input type="hidden" name="edit_tujuan_pd_tujuan_id" id="edit_tujuan_pd_tujuan_id">
                        <div class="form-group position-relative mb-3">
                            <label for="edit_tujuan_pd_kode" class="form-label">Kode</label>
                            <input type="number" class="form-control" id="edit_tujuan_pd_kode" name="edit_tujuan_pd_kode" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="edit_tujuan_pd_deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="edit_tujuan_pd_deskripsi" id="edit_tujuan_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="edit_tujuan_pd_tahun_perubahan" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="edit_tujuan_pd_tahun_perubahan" name="edit_tujuan_pd_tahun_perubahan" required>
                        </div>
                        <div class="form-group position-relative" style="text-align: right">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="indikatorKinerjaTujuanPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaTujuanPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Tujuan PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tujuan-pd.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_tujuan_pd_tujuan_pd_id" id="indikator_kinerja_tujuan_pd_tujuan_pd_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <textarea name="indikator_kinerja_tujuan_pd_deskripsi" id="indikator_kinerja_tujuan_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_tujuan_pd_status_indikator" class="form-label">Status Indikator</label>
                            <select name="indikator_kinerja_tujuan_pd_status_indikator" id="indikator_kinerja_tujuan_pd_status_indikator" class="form-control" required>
                                <option value=""> --- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_tujuan_pd_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="indikator_kinerja_tujuan_pd_satuan" name="indikator_kinerja_tujuan_pd_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="text" class="form-control" id="indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" name="indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editIndikatorKinerjaTujuanPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editIndikatorKinerjaTujuanPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Indikator Kinerja Tujuan PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tujuan-pd.indikator-kinerja.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_tujuan_pd_id" id="indikator_kinerja_tujuan_pd_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Indikator Kinerja</label>
                            <input id="edit_indikator_kinerja_tujuan_pd_deskripsi" name="edit_indikator_kinerja_tujuan_pd_deskripsi" class="form-control" required/>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_tujuan_pd_status_indikator" class="form-label">Status Indikator</label>
                            <select name="edit_indikator_kinerja_tujuan_pd_status_indikator" id="edit_indikator_kinerja_tujuan_pd_status_indikator" class="form-control" required>
                                <option value=""> --- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_tujuan_pd_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_tujuan_pd_satuan" name="edit_indikator_kinerja_tujuan_pd_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" name="edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetTujuanPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetTujuanPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tujuan-pd.indikator-kinerja.target-satuan-realisasi.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tujuan_pd_target_satuan_rp_realisasi" id="tujuan_pd_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="tujuan_pd_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="tujuan_pd_edit_target" name="tujuan_pd_edit_target" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Tujuan PD End --}}

    {{-- Sasaran PD Start --}}
    <div id="tambahSasaranPDModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tambahSasaranPDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Sasaran PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tambah_sasaran_pd_sasaran_id" id="tambah_sasaran_pd_sasaran_id">
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_sasaran_pd_kode" class="form-label">Kode</label>
                            <input type="number" class="form-control" id="tambah_sasaran_pd_kode" name="tambah_sasaran_pd_kode" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_sasaran_pd_deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="tambah_sasaran_pd_deskripsi" id="tambah_sasaran_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="tambah_sasaran_pd_tahun_perubahan" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="tambah_sasaran_pd_tahun_perubahan" name="tambah_sasaran_pd_tahun_perubahan" required>
                        </div>
                        <div class="form-group position-relative" style="text-align: right">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editSasaranPDModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editSasaranPDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Sasaran PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_sasaran_pd_sasaran_pd_id" id="edit_sasaran_pd_sasaran_pd_id">
                        <input type="hidden" name="edit_sasaran_pd_sasaran_id" id="edit_sasaran_pd_sasaran_id">
                        <div class="form-group position-relative mb-3">
                            <label for="edit_sasaran_pd_kode" class="form-label">Kode</label>
                            <input type="number" class="form-control" id="edit_sasaran_pd_kode" name="edit_sasaran_pd_kode" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="edit_sasaran_pd_deskripsi" class="form-label">Deskripsi</label>
                            <textarea name="edit_sasaran_pd_deskripsi" id="edit_sasaran_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="edit_sasaran_pd_tahun_perubahan" class="form-label">Tahun</label>
                            <input type="number" class="form-control" id="edit_sasaran_pd_tahun_perubahan" name="edit_sasaran_pd_tahun_perubahan" required>
                        </div>
                        <div class="form-group position-relative" style="text-align: right">
                            <button class="btn btn-primary waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="indikatorKinerjaSasaranPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaSasaranPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Sasaran PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_sasaran_pd_sasaran_pd_id" id="indikator_kinerja_sasaran_pd_sasaran_pd_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <textarea name="indikator_kinerja_sasaran_pd_deskripsi" id="indikator_kinerja_sasaran_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_sasaran_pd_status_indikator" class="form-label">Status Indikator</label>
                            <select name="indikator_kinerja_sasaran_pd_status_indikator" id="indikator_kinerja_sasaran_pd_status_indikator" class="form-control" required>
                                <option value="">--- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_sasaran_pd_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="indikator_kinerja_sasaran_pd_satuan" name="indikator_kinerja_sasaran_pd_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" class="form-control" id="indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" name="indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editIndikatorKinerjaSasaranPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editIndikatorKinerjaSasaranPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Indikator Kinerja Sasaran PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.indikator-kinerja.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_sasaran_pd_id" id="indikator_kinerja_sasaran_pd_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Indikator Kinerja</label>
                            <textarea name="edit_indikator_kinerja_sasaran_pd_deskripsi" id="edit_indikator_kinerja_sasaran_pd_deskripsi" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_sasaran_pd_status_indikator" class="form-label">Status Indikator</label>
                            <select name="edit_indikator_kinerja_sasaran_pd_status_indikator" id="edit_indikator_kinerja_sasaran_pd_status_indikator" class="form-control" required>
                                <option value="">--- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_sasaran_pd_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_sasaran_pd_satuan" name="edit_indikator_kinerja_sasaran_pd_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" class="form-label">Kondisi Target Kinerja Awal</label>
                            <input type="number" class="form-control" id="edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" name="edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetSasaranPdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetSasaranPdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.indikator-kinerja.target-satuan-realisasi.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_pd_target_satuan_rp_realisasi" id="sasaran_pd_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="sasaran_pd_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="sasaran_pd_edit_target" name="sasaran_pd_edit_target" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="tambahSasaranPdProgramRpjmdModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tambahSasaranPdProgramRpjmdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Atur Kelompok Program Terhadap Sasaran PD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.sasaran-pd.sasaran-pd-program-rpjmd.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_pd_program_rpjmd_sasaran_pd_id" id="sasaran_pd_program_rpjmd_sasaran_pd_id">
                        <div class="form-group position-relative mb-3">
                            <label for="sasaran_pd_program_rpjmd_program_rpjmd_id" class="form-label">Program RPJMD</label>
                            <select name="sasaran_pd_program_rpjmd_program_rpjmd_id[]" id="sasaran_pd_program_rpjmd_program_rpjmd_id" class="form-control" multiple required></select>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Sasaran PD End --}}

    {{-- Program Start --}}
    <div id="editTargetProgramModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.program.indikator.target-satuan-rp-realisasi.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="program_target_satuan_rp_realisasi" id="program_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="program_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="program_edit_realisasi" name="program_edit_realisasi" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="program_edit_realisasi_rp" class="form-label">RP</label>
                            <input type="text" class="form-control" id="program_edit_realisasi_rp" name="program_edit_realisasi_rp" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Program End --}}

    {{-- Kegiatan Start --}}
    <div id="indikatorKinerjaKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Kegiatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.kegiatan.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_kegiatan_kegiatan_id" id="indikator_kinerja_kegiatan_kegiatan_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <textarea name="indikator_kinerja_kegiatan_deskripsi" id="indikator_kinerja_kegiatan_deskripsi" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_kegiatan_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="indikator_kinerja_kegiatan_satuan" name="indikator_kinerja_kegiatan_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" class="form-label">Target Kinerja Awal</label>
                            <input type="text" class="form-control" id="indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" name="indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" class="form-label">Target Anggaran Awal</label>
                            <input type="text" class="form-control" id="indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" name="indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="indikator_kinerja_kegiatan_status_indikator" class="control-label">Status Indikator</label>
                            <select name="indikator_kinerja_kegiatan_status_indikator" id="indikator_kinerja_kegiatan_status_indikator" class="form-control" required>
                                <option value="">--- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editIndikatorKinerjaKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editIndikatorKinerjaKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Indikator Kinerja Kegiatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.kegiatan.indikator-kinerja.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_kegiatan_id" id="indikator_kinerja_kegiatan_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Indikator Kinerja</label>
                            <textarea name="edit_indikator_kinerja_kegiatan_deskripsi" id="edit_indikator_kinerja_kegiatan_deskripsi" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_kegiatan_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_kegiatan_satuan" name="edit_indikator_kinerja_kegiatan_satuan" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" class="form-label">Target Kinerja Awal</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" name="edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" class="form-label">Target Anggaran Awal</label>
                            <input type="text" class="form-control" id="edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" name="edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <label for="edit_indikator_kinerja_kegiatan_status_indikator" class="control-label">Status Indikator</label>
                            <select name="edit_indikator_kinerja_kegiatan_status_indikator" id="edit_indikator_kinerja_kegiatan_status_indikator" class="form-control" required>
                                <option value="">--- Pilih Status Indikator ---</option>
                                <option value="Target NSPK">Target NSPK</option>
                                <option value="Target IKK">Target IKK</option>
                                <option value="Target Indikator Lainnya">Target Indikator Lainnya</option>
                            </select>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.kegiatan.indikator-kinerja.target-satuan-realisasi.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kegiatan_target_satuan_rp_realisasi" id="kegiatan_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="kegiatan_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="kegiatan_edit_target" name="kegiatan_edit_target" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="kegiatan_edit_target_rp" class="form-label">Target RP</label>
                            <input type="text" class="form-control" id="kegiatan_edit_target_rp" name="kegiatan_edit_target_rp" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Kegiatan End --}}
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
        $(document).ready(function(){
            $('#renstra_misi_filter_visi').select2();
            $('#renstra_misi_filter_misi').select2();

            $('#renstra_tujuan_filter_visi').select2();
            $('#renstra_tujuan_filter_misi').select2();
            $('#renstra_tujuan_filter_tujuan').select2();

            $('#renstra_sasaran_filter_visi').select2();
            $('#renstra_sasaran_filter_misi').select2();
            $('#renstra_sasaran_filter_tujuan').select2();
            $('#renstra_sasaran_filter_sasaran').select2();

            $('#renstra_program_filter_visi').select2();
            $('#renstra_program_filter_misi').select2();
            $('#renstra_program_filter_tujuan').select2();
            $('#renstra_program_filter_sasaran').select2();
            $('#renstra_program_filter_program').select2();

            $('#renstra_kegiatan_filter_visi').select2();
            $('#renstra_kegiatan_filter_misi').select2();
            $('#renstra_kegiatan_filter_tujuan').select2();
            $('#renstra_kegiatan_filter_sasaran').select2();
            $('#renstra_kegiatan_filter_program').select2();
            $('#renstra_kegiatan_filter_kegiatan').select2();

            $('#renstra_kegiatan_kegiatan_id').select2();

            $('#sasaran_pd_program_rpjmd_program_rpjmd_id').select2({
                dropdownParent: $("#tambahSasaranPdProgramRpjmdModal")
            });

            $.ajax({
                url: "{{ route('opd.renstra.get-misi') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraMisiNavDiv').html(data.html);
                }
            });

            // new Tagify(document.querySelector('#indikator_kinerja_tujuan_pd_deskripsi'));
            // new Tagify(document.querySelector('#indikator_kinerja_sasaran_pd_deskripsi'));
            // new Tagify(document.querySelector('#indikator_kinerja_kegiatan_deskripsi'));
        });

        // Misi Start
        $('#renstra_misi_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-misi') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraMisiNavDiv').html(data.html);
                }
            });
        });
        $(document).on('change', '#onOffTaggingRenstraMisi',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-misi-tagging').show();
            } else {
                $('.renstra-misi-tagging').hide();
            }
        });
        // Misi End

        // Tujuan Start
        $('#renstra_tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraTujuan',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-tujuan-tagging').show();
            } else {
                $('.renstra-tujuan-tagging').hide();
            }
        });

        $(document).on('click', '.button-tambah-tujuan-pd', function(){
            var tujuan_id = $(this).attr('data-tujuan-id');
            $('#tambah_tujuan_pd_tujuan_id').val(tujuan_id);
            $('#tambahTujuanPDModal').modal('show');
        });

        $(document).on('click', '.edit-tujuan-pd', function(){
            var tujuan_pd_id = $(this).attr('data-tujuan-pd-id');
            var tujuan_id = $(this).attr('data-tujuan-id');
            $.ajax({
                url : "{{ url('/opd/renstra/tujuan-pd/edit') }}" + '/' + tujuan_pd_id,
                dataType: "json",
                success: function(data)
                {
                    $('#edit_tujuan_pd_tujuan_pd_id').val(tujuan_pd_id);
                    $('#edit_tujuan_pd_tujuan_id').val(tujuan_id);
                    $('#edit_tujuan_pd_kode').val(data.result.kode);
                    $('#edit_tujuan_pd_deskripsi').val(data.result.deskripsi);
                    $('#edit_tujuan_pd_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#editTujuanPDModal').modal('show');
                }
            });
        });

        $(document).on('click', '.hapus-tujuan-pd', function(){
            var tujuan_pd_id = $(this).attr('data-tujuan-pd-id');
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
                        url: "{{ route('opd.renstra.tujuan-pd.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tujuan_pd_id:tujuan_pd_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.tambah-tujuan-pd-indikator-kinerja', function(){
            var tujuan_pd_id = $(this).attr('data-tujuan-pd-id');
            $('#indikator_kinerja_tujuan_pd_tujuan_pd_id').val(tujuan_pd_id);
            $('#indikatorKinerjaTujuanPdModal').modal('show');
        });

        $(document).on('click', '.btn-edit-tujuan-indikator-kinerja', function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/tujuan-pd/indikator-kinerja/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#indikator_kinerja_tujuan_pd_id').val(id);
                    $('#edit_indikator_kinerja_tujuan_pd_deskripsi').val(data.result.deskripsi);
                    $('#edit_indikator_kinerja_tujuan_pd_satuan').val(data.result.satuan);
                    $('#edit_indikator_kinerja_tujuan_pd_kondisi_target_kinerja_awal').val(data.result.kondisi_target_kinerja_awal);
                    $("[name='edit_indikator_kinerja_tujuan_pd_status_indikator']").val(data.result.status_indikator).trigger('change');
                    $('#editIndikatorKinerjaTujuanPdModal').modal('show');
                }
            });
        });

        $(document).on('click', '.btn-hapus-tujuan-indikator-kinerja', function(){
            var tujuan_pd_indikator_kinerja_id = $(this).attr('data-tujuan-pd-indikator-kinerja-id');

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
                        url: "{{ route('opd.renstra.tujuan-pd.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tujuan_pd_indikator_kinerja_id:tujuan_pd_indikator_kinerja_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-tujuan-pd-target-satuan-rp-realisasi', function(){
            var tujuan_pd_indikator_kinerja_id = $(this).attr('data-tujuan-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.tujuan-pd-add-target.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).val();

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
                        url: "{{ route('opd.renstra.tujuan-pd.indikator-kinerja.target-satuan-realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            tujuan_pd_indikator_kinerja_id:tujuan_pd_indikator_kinerja_id,
                            target:target
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-tujuan-pd-edit-target-satuan-rp-realisasi', function(){
            var tujuan_pd_indikator_kinerja_id = $(this).attr('data-tujuan-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var tujuan_pd_target_satuan_rp_realisasi = $(this).attr('data-tujuan-pd-target-satuan-rp-realisasi');
            var target = $('.tujuan-pd-span-target.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).text();

            $('#tujuan_pd_target_satuan_rp_realisasi').val(tujuan_pd_target_satuan_rp_realisasi);
            $('#tujuan_pd_edit_target').val(target);

            $('#editTargetTujuanPdModal').modal('show');
        });
        // Tujuan End

        // Sasaran Start
        $('#renstra_sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraSasaran',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-sasaran-tagging').show();
            } else {
                $('.renstra-sasaran-tagging').hide();
            }
        });

        $(document).on('click', '.button-tambah-sasaran-pd', function(){
            var sasaran_id = $(this).attr('data-sasaran-id');
            $('#tambah_sasaran_pd_sasaran_id').val(sasaran_id);
            $('#tambahSasaranPDModal').modal('show');
        });

        $(document).on('click', '.edit-sasaran-pd', function(){
            var sasaran_pd_id = $(this).attr('data-sasaran-pd-id');
            var sasaran_id = $(this).attr('data-sasaran-id');
            $.ajax({
                url : "{{ url('/opd/renstra/sasaran-pd/edit') }}" + '/' + sasaran_pd_id,
                dataType: "json",
                success: function(data)
                {
                    $('#edit_sasaran_pd_sasaran_pd_id').val(sasaran_pd_id);
                    $('#edit_sasaran_pd_sasaran_id').val(sasaran_id);
                    $('#edit_sasaran_pd_kode').val(data.result.kode);
                    $('#edit_sasaran_pd_deskripsi').val(data.result.deskripsi);
                    $('#edit_sasaran_pd_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#editSasaranPDModal').modal('show');
                }
            });
        });

        $(document).on('click', '.hapus-sasaran-pd', function(){
            var sasaran_pd_id = $(this).attr('data-sasaran-pd-id');
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
                        url: "{{ route('opd.renstra.sasaran-pd.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sasaran_pd_id:sasaran_pd_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.tambah-sasaran-pd-indikator-kinerja', function(){
            var sasaran_pd_id = $(this).attr('data-sasaran-pd-id');
            $('#indikator_kinerja_sasaran_pd_sasaran_pd_id').val(sasaran_pd_id);
            $('#indikatorKinerjaSasaranPdModal').modal('show');
        });

        $(document).on('click', '.btn-edit-sasaran-indikator-kinerja', function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/sasaran-pd/indikator-kinerja/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#indikator_kinerja_sasaran_pd_id').val(id);
                    $('#edit_indikator_kinerja_sasaran_pd_deskripsi').val(data.result.deskripsi);
                    $('#edit_indikator_kinerja_sasaran_pd_satuan').val(data.result.satuan);
                    $('#edit_indikator_kinerja_sasaran_pd_kondisi_target_kinerja_awal').val(data.result.kondisi_target_kinerja_awal);
                    $("[name='edit_indikator_kinerja_sasaran_pd_status_indikator']").val(data.result.status_indikator).trigger('change');
                    $('#editIndikatorKinerjaSasaranPdModal').modal('show');
                }
            });
        });

        $(document).on('click', '.btn-hapus-sasaran-indikator-kinerja', function(){
            var sasaran_pd_indikator_kinerja_id = $(this).attr('data-sasaran-pd-indikator-kinerja-id');

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
                        url: "{{ route('opd.renstra.sasaran-pd.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sasaran_pd_indikator_kinerja_id:sasaran_pd_indikator_kinerja_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sasaran-pd-target-satuan-rp-realisasi', function(){
            var sasaran_pd_indikator_kinerja_id = $(this).attr('data-sasaran-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.sasaran-pd-add-target.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).val();

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
                        url: "{{ route('opd.renstra.sasaran-pd.indikator-kinerja.target-satuan-realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            sasaran_pd_indikator_kinerja_id:sasaran_pd_indikator_kinerja_id,
                            target:target
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sasaran-pd-edit-target-satuan-rp-realisasi', function(){
            var sasaran_pd_indikator_kinerja_id = $(this).attr('data-sasaran-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var sasaran_pd_target_satuan_rp_realisasi = $(this).attr('data-sasaran-pd-target-satuan-rp-realisasi');
            var target = $('.sasaran-pd-span-target.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).text();

            $('#sasaran_pd_target_satuan_rp_realisasi').val(sasaran_pd_target_satuan_rp_realisasi);
            $('#sasaran_pd_edit_target').val(target);

            $('#editTargetSasaranPdModal').modal('show');
        });

        $(document).on('click', '.tambah-sasaran-pd-program-rpjmd', function(){
            var sasaran_pd_id = $(this).attr('data-sasaran-pd-id');
            var sasaran_id = $(this).attr('data-sasaran-id');
            $('#sasaran_pd_program_rpjmd_sasaran_pd_id').val(sasaran_pd_id);
            $.ajax({
                url: "{{ route('opd.renstra.sasaran-pd.sasaran-pd-program-rpjmd.get-program-rpjmd') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    sasaran_pd_id : sasaran_pd_id,
                    sasaran_id : sasaran_id
                },
                success: function(data)
                {
                    $('#sasaran_pd_program_rpjmd_program_rpjmd_id').empty();
                    $.each(data, function(key, value){
                        $('#sasaran_pd_program_rpjmd_program_rpjmd_id').append(new Option(value.deskripsi, value.id));
                    });
                }
            });
            $('#tambahSasaranPdProgramRpjmdModal').modal('show');
        });

        $(document).on('click', '.btn-hapus-sasaran-pd-program-rpjmd', function(){
            var sasaran_pd_program_rpjmd_id = $(this).attr('data-sasaran-pd-program-rpjmd-id');
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
                        url: "{{ route('opd.renstra.sasaran-pd.sasaran-pd-program-rpjmd.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sasaran_pd_program_rpjmd_id:sasaran_pd_program_rpjmd_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });
        // Sasaran End

        // Program Start
        $('#renstra_program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraProgramNavDiv').html(data.html);
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

        $(document).on('click', '.button-program-edit-target-satuan-rp-realisasi', function(){
            var opd_program_indikator_kinerja_id = $(this).attr('data-opd-program-indikator-kinerja-id');
            var sasaran_id = $(this).attr('data-sasaran-id');
            var tahun = $(this).attr('data-tahun');
            var program_target_satuan_rp_realisasi = $(this).attr('data-program-target-satuan-rp-realisasi');
            var realisasi = $('.program-span-realisasi.'+tahun+'.data-opd-program-indikator-kinerja-'+opd_program_indikator_kinerja_id+'.data-sasaran-id-'+sasaran_id).text();
            var realisasi_rp = $('.program-span-realisasi-rp.'+tahun+'.data-opd-program-indikator-kinerja-'+opd_program_indikator_kinerja_id+'.data-sasaran-id-'+sasaran_id).attr('data-realisasi-rp');

            $('#program_target_satuan_rp_realisasi').val(program_target_satuan_rp_realisasi);
            $('#program_edit_realisasi').val(realisasi);
            $('#program_edit_realisasi_rp').val(realisasi_rp);

            $('#editTargetProgramModal').modal('show');
        });
        // Program End

        // Kegiatan Start
        $(document).on('click', '.tambah-kegiatan-indikator-kinerja', function(){
            var kegiatan_id = $(this).attr('data-kegiatan-id');
            $('#indikator_kinerja_kegiatan_kegiatan_id').val(kegiatan_id);
            $('#indikatorKinerjaKegiatanModal').modal('show');
        });

        $(document).on('click', '.btn-edit-kegiatan-indikator-kinerja', function(){
            var id = $(this).attr('data-id');
            $.ajax({
                url: "{{ url('/opd/renstra/kegiatan/indikator-kinerja/edit') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#indikator_kinerja_kegiatan_id').val(id);
                    $('#edit_indikator_kinerja_kegiatan_deskripsi').val(data.result.deskripsi);
                    $('#edit_indikator_kinerja_kegiatan_satuan').val(data.result.satuan);
                    $('#edit_indikator_kinerja_kegiatan_kondisi_target_kinerja_awal').val(data.result.kondisi_target_kinerja_awal);
                    $('#edit_indikator_kinerja_kegiatan_kondisi_target_anggaran_awal').val(data.result.kondisi_target_anggaran_awal);
                    $("[name='edit_indikator_kinerja_kegiatan_status_indikator']").val(data.result.status_indikator).trigger('change');
                    $('#editIndikatorKinerjaKegiatanModal').modal('show');
                }
            });
        });

        $(document).on('click', '.btn-hapus-kegiatan-indikator-kinerja', function(){
            var kegiatan_indikator_kinerja_id = $(this).attr('data-kegiatan-indikator-kinerja-id');

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
                        url: "{{ route('opd.renstra.kegiatan.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            kegiatan_indikator_kinerja_id:kegiatan_indikator_kinerja_id
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-kegiatan-target-satuan-rp-realisasi', function(){
            var kegiatan_indikator_kinerja_id = $(this).attr('data-kegiatan-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.kegiatan-add-target.'+tahun+'.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).val();
            var target_rp = $('.kegiatan-add-target-rp.'+tahun+'.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id).val();

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
                        url: "{{ route('opd.renstra.kegiatan.indikator-kinerja.target-satuan-realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            kegiatan_indikator_kinerja_id:kegiatan_indikator_kinerja_id,
                            target:target,
                            target_rp:target_rp
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
                                    window.location.href = "{{ route('opd.renstra.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-kegiatan-edit-target-satuan-rp-realisasi', function(){
            var kegiatan_indikator_kinerja_id = $(this).attr('data-kegiatan-indikator-kinerja-id');
            var sasaran_id = $(this).attr('data-sasaran-id');
            var tahun = $(this).attr('data-tahun');
            var kegiatan_target_satuan_rp_realisasi = $(this).attr('data-kegiatan-target-satuan-rp-realisasi');
            var target = $('.kegiatan-span-target.'+tahun+'.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id+'.data-sasaran-id-'+sasaran_id).text();
            var target_rp = $('.kegiatan-span-target-rp.'+tahun+'.data-kegiatan-indikator-kinerja-'+kegiatan_indikator_kinerja_id+'.data-sasaran-id-'+sasaran_id).attr('data-target-rp');

            $('#kegiatan_target_satuan_rp_realisasi').val(kegiatan_target_satuan_rp_realisasi);
            $('#kegiatan_edit_target').val(target);
            $('#kegiatan_edit_target_rp').val(target_rp);

            $('#editTargetKegiatanModal').modal('show');
        });
        // Kegiatan End

        // Filter Data Misi
        $('#renstra_misi_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_misi_filter_misi').empty();
                        $('#renstra_misi_filter_misi').prop('disabled', false);
                        $('#renstra_misi_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_misi_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_misi_filter_misi').prop('disabled', true);
                $("[name='renstra_misi_filter_misi']").val('').trigger('change');
            }
        });

        $('#renstra_misi_btn_filter').click(function(){
            var visi = $('#renstra_misi_filter_visi').val();
            var misi = $('#renstra_misi_filter_misi').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-misi') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi
                },
                success: function(data)
                {
                    $('#renstraMisiNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_misi_btn_reset').click(function(){
            $('#renstra_misi_filter_misi').prop('disabled', true);
            $("[name='renstra_misi_filter_visi']").val('').trigger('change');
            $("[name='renstra_misi_filter_misi']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-misi') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraMisiNavDiv').html(data.html);
                }
            });
        });

        // Filter Data Tujuan
        $('#renstra_tujuan_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_misi').empty();
                        $('#renstra_tujuan_filter_misi').prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                        $('#renstra_tujuan_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_misi').prop('disabled', true);
                $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                $("[name='renstra_tujuan_filter_misi']").val('').trigger('change');
                $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            }
        });

        $('#renstra_tujuan_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_tujuan').empty();
                        $('#renstra_tujuan_filter_tujuan').prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            }
        });

        $('#renstra_tujuan_btn_filter').click(function(){
            var visi = $('#renstra_tujuan_filter_visi').val();
            var misi = $('#renstra_tujuan_filter_misi').val();
            var tujuan = $('#renstra_tujuan_filter_tujuan').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_tujuan_btn_reset').click(function(){
            $('#renstra_tujuan_filter_misi').prop('disabled', true);
            $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
            $("[name='renstra_tujuan_filter_visi']").val('').trigger('change');
            $("[name='renstra_tujuan_filter_misi']").val('').trigger('change');
            $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        // Filter Data Sasaran
        $('#renstra_sasaran_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_misi').empty();
                        $('#renstra_sasaran_filter_misi').prop('disabled', false);
                        $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renstra_sasaran_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_misi').prop('disabled', true);
                $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_misi']").val('').trigger('change');
                $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_tujuan').empty();
                        $('#renstra_sasaran_filter_tujuan').prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renstra_sasaran_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_sasaran').empty();
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_btn_filter').click(function(){
            var visi = $('#renstra_sasaran_filter_visi').val();
            var misi = $('#renstra_sasaran_filter_misi').val();
            var tujuan = $('#renstra_sasaran_filter_tujuan').val();
            var sasaran = $('#renstra_sasaran_filter_sasaran').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-sasaran') }}",
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
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_sasaran_btn_reset').click(function(){
            $('#renstra_sasaran_filter_misi').prop('disabled', true);
            $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
            $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
            $("[name='renstra_sasaran_filter_visi']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_misi']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        // Filter Data Program
        $('#renstra_program_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_misi').empty();
                        $('#renstra_program_filter_misi').prop('disabled', false);
                        $('#renstra_program_filter_tujuan').prop('disabled', true);
                        $('#renstra_program_filter_sasaran').prop('disabled', true);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_misi').prop('disabled', true);
                $('#renstra_program_filter_tujuan').prop('disabled', true);
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_misi']").val('').trigger('change');
                $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_tujuan').empty();
                        $('#renstra_program_filter_tujuan').prop('disabled', false);
                        $('#renstra_program_filter_sasaran').prop('disabled', true);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_tujuan').prop('disabled', true);
                $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_sasaran').empty();
                        $('#renstra_program_filter_sasaran').prop('disabled', false);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_sasaran').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_program').empty();
                        $('#renstra_program_filter_program').prop('disabled', false);
                        $('#renstra_program_filter_program').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_program').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_btn_filter').click(function(){
            var visi = $('#renstra_program_filter_visi').val();
            var misi = $('#renstra_program_filter_misi').val();
            var tujuan = $('#renstra_program_filter_tujuan').val();
            var sasaran = $('#renstra_program_filter_sasaran').val();
            var program = $('#renstra_program_filter_program').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-program') }}",
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
                    $('#renstraProgramNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_program_btn_reset').click(function(){
            $('#renstra_program_filter_misi').prop('disabled', true);
            $('#renstra_program_filter_tujuan').prop('disabled', true);
            $('#renstra_program_filter_sasaran').prop('disabled', true);
            $('#renstra_program_filter_program').prop('disabled', true);
            $("[name='renstra_program_filter_visi']").val('').trigger('change');
            $("[name='renstra_program_filter_misi']").val('').trigger('change');
            $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
            $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
            $("[name='renstra_program_filter_program']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraProgramNavDiv').html(data.html);
                }
            });
        });

        // Renstra Kegiatan
        $('#renstra_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv').html(data.html);
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

        // Filter Data Kegiatan
        $('#renstra_kegiatan_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_misi').empty();
                        $('#renstra_kegiatan_filter_misi').prop('disabled', false);
                        $('#renstra_kegiatan_filter_tujuan').prop('disabled', true);
                        $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
                        $('#renstra_kegiatan_filter_program').prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renstra_kegiatan_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_misi').prop('disabled', true);
                $('#renstra_kegiatan_filter_tujuan').prop('disabled', true);
                $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
                $('#renstra_kegiatan_filter_program').prop('disabled', true);
                $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_misi']").val('').trigger('change');
                $("[name='renstra_kegiatan_filter_tujuan']").val('').trigger('change');
                $("[name='renstra_kegiatan_filter_sasaran']").val('').trigger('change');
                $("[name='renstra_kegiatan_filter_program']").val('').trigger('change');
                $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            }
        });

        $('#renstra_kegiatan_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_tujuan').empty();
                        $('#renstra_kegiatan_filter_tujuan').prop('disabled', false);
                        $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
                        $('#renstra_kegiatan_filter_program').prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renstra_kegiatan_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_tujuan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_tujuan']").val('').trigger('change');
                $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_sasaran']").val('').trigger('change');
                $('#renstra_kegiatan_filter_program').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_program']").val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            }
        });

        $('#renstra_kegiatan_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_sasaran').empty();
                        $('#renstra_kegiatan_filter_sasaran').prop('disabled', false);
                        $('#renstra_kegiatan_filter_program').prop('disabled', true);
                        $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renstra_kegiatan_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_sasaran']").val('').trigger('change');
                $('#renstra_kegiatan_filter_program').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_program']").val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            }
        });

        $('#renstra_kegiatan_filter_sasaran').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_program').empty();
                        $('#renstra_kegiatan_filter_program').prop('disabled', false);
                        $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                        $('#renstra_kegiatan_filter_program').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_program').append(new Option(value.kode +'. '+value.deskripsi, value.program_rpjmd_id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_program').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_program']").val('').trigger('change');
                $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            }
        });

        $('#renstra_kegiatan_filter_program').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-kegiatan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_kegiatan_filter_kegiatan').empty();
                        $('#renstra_kegiatan_filter_kegiatan').prop('disabled', false);
                        $('#renstra_kegiatan_filter_kegiatan').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_kegiatan_filter_kegiatan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
                $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            }
        });

        $('#renstra_kegiatan_btn_filter').click(function(){
            var visi = $('#renstra_kegiatan_filter_visi').val();
            var misi = $('#renstra_kegiatan_filter_misi').val();
            var tujuan = $('#renstra_kegiatan_filter_tujuan').val();
            var sasaran = $('#renstra_kegiatan_filter_sasaran').val();
            var program = $('#renstra_kegiatan_filter_program').val();
            var kegiatan = $('#renstra_kegiatan_filter_kegiatan').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-kegiatan') }}",
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
                    $('#renstraKegiatanNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_kegiatan_btn_reset').click(function(){
            $('#renstra_kegiatan_filter_misi').prop('disabled', true);
            $('#renstra_kegiatan_filter_tujuan').prop('disabled', true);
            $('#renstra_kegiatan_filter_sasaran').prop('disabled', true);
            $('#renstra_kegiatan_filter_program').prop('disabled', true);
            $('#renstra_kegiatan_filter_kegiatan').prop('disabled', true);
            $("[name='renstra_kegiatan_filter_visi']").val('').trigger('change');
            $("[name='renstra_kegiatan_filter_misi']").val('').trigger('change');
            $("[name='renstra_kegiatan_filter_tujuan']").val('').trigger('change');
            $("[name='renstra_kegiatan_filter_sasaran']").val('').trigger('change');
            $("[name='renstra_kegiatan_filter_program']").val('').trigger('change');
            $("[name='renstra_kegiatan_filter_kegiatan']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv').html(data.html);
                }
            });
        });
    </script>
@endsection
