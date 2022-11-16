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
                            <input id="indikator_kinerja_tujuan_pd_deskripsi" name="indikator_kinerja_tujuan_pd_deskripsi"/>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
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
                        <div class="form-group position-relative">
                            <label for="tujuan_pd_edit_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="tujuan_pd_edit_satuan" name="tujuan_pd_edit_satuan" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="tujuan_pd_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="tujuan_pd_edit_realisasi" name="tujuan_pd_edit_realisasi" required>
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
                            <input id="indikator_kinerja_sasaran_pd_deskripsi" name="indikator_kinerja_sasaran_pd_deskripsi"/>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
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
                        <div class="form-group position-relative">
                            <label for="sasaran_pd_edit_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="sasaran_pd_edit_satuan" name="sasaran_pd_edit_satuan" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="sasaran_pd_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="sasaran_pd_edit_realisasi" name="sasaran_pd_edit_realisasi" required>
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
                        <input type="hidden" name="renstra_kegiatan_opd_id" value="{{Auth::user()->opd->opd_id}}">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <select name="renstra_kegiatan_kegiatan_id" id="renstra_kegiatan_kegiatan_id" class="form-control" required>
                                <option value="">--- Pilih Kegiatan ---</option>
                            </select>
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

            $.ajax({
                url: "{{ route('opd.renstra.get-misi') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraMisiNavDiv').html(data.html);
                }
            });

            new Tagify(document.querySelector('#indikator_kinerja_tujuan_pd_deskripsi'));
            new Tagify(document.querySelector('#indikator_kinerja_sasaran_pd_deskripsi'));
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
            var satuan = $('.tujuan-pd-add-satuan.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).val();
            var realisasi = $('.tujuan-pd-add-realisasi.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).val();

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
                            target:target,
                            satuan:satuan,
                            realisasi:realisasi
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
            var satuan = $('.tujuan-pd-span-satuan.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).text();
            var realisasi = $('.tujuan-pd-span-realisasi.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id).text();

            $('#tujuan_pd_target_satuan_rp_realisasi').val(tujuan_pd_target_satuan_rp_realisasi);
            $('#tujuan_pd_edit_target').val(target);
            $('#tujuan_pd_edit_satuan').val(satuan);
            $('#tujuan_pd_edit_realisasi').val(realisasi);

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
            var satuan = $('.sasaran-pd-add-satuan.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).val();
            var realisasi = $('.sasaran-pd-add-realisasi.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).val();

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
                            target:target,
                            satuan:satuan,
                            realisasi:realisasi
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
            var satuan = $('.sasaran-pd-span-satuan.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).text();
            var realisasi = $('.sasaran-pd-span-realisasi.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id).text();

            $('#sasaran_pd_target_satuan_rp_realisasi').val(sasaran_pd_target_satuan_rp_realisasi);
            $('#sasaran_pd_edit_target').val(target);
            $('#sasaran_pd_edit_satuan').val(satuan);
            $('#sasaran_pd_edit_realisasi').val(realisasi);

            $('#editTargetSasaranPdModal').modal('show');
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
        // Program End

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
                url: "{{ route('opd.renstra.option.kegiatan') }}",
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
            // $('#renstra_kegiatan_kegiatan_id').siblings('.select2').children('.selection').children('.select2-selection').css('z-index', '10000');
        });

        $('#renstra_kegiatan_form').on('submit', function(e){
            e.preventDefault();
            if($('#renstra_kegiatan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.kegiatan.store') }}",
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
                            $('#addEditRenstraKegiatanModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Kegiatan',
                                showConfirmButton: true
                            });
                            $('#renstra_kegiatan').html(data.success);
                        }

                        $('#renstra_kegiatan_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-renstra-kegiatan', function(){
            var id = $(this).attr('data-renstra-kegiatan-id');
            $.ajax({
                url: "{{ url('/opd/renstra/kegiatan/detail') }}" + '/' +id,
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
                        url: "{{ route('opd.renstra.kegiatan.detail.target-rp-pertahun') }}",
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
