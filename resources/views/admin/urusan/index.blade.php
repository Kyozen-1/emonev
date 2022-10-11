@extends('admin.layouts.app')
@section('title', 'Admin | Urusan')

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
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Urusan</h1>
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

        <div class="row mb-3">
            <div class="col-12" style="text-align: right">
                <button class="btn btn-outline-primary waves-effect waves-light mr-2" id="create" type="button" data-bs-toggle="modal" data-bs-target="#addEditModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
                <a class="btn btn-outline-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_urusan.xlsx') }}" title="Download Template Import Data"><i class="fas fa-file-excel"></i></a>
                <button class="btn btn-outline-info waves-effect waves-light" title="Import Data" id="btn_impor_template" type="button"><i class="fas fa-file-import"></i></button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="data-table-rows slim">
                            <div class="data-table-responsive-wrapper">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th width="15%">Kode</th>
                                            <th widht="50%">Urusan</th>
                                            <th width="15%">Tahun Perubahan</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo1" class="accordion-toggle">
                                                1.01
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo1" class="accordion-toggle">
                                                urusan pemerintah bidang pendidikan
                                            </td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo1" class="accordion-toggle">
                                                2019
                                            </td>
                                            <td>
                                                <button class="btn btn-icon btn-info waves-effect waves-light mr-1">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-icon btn-warning waves-effect waves-light">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" class="hiddenRow">
                                                <div class="accordian-body collapse" id="demo1">
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
                                                        <tr data-bs-toggle="collapse" data-bs-target="#demo10" class="accordion-toggle">
                                                            <td>
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
                                                        <tr>
                                                            <td colspan="12" class="hiddenRow">
                                                                <div class="accordian-body collapse" id="demo10">
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
                                                                        <tr data-toggle="collapse" class="accordion-toggle" data-target="#demo2">
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
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>

                                        {{-- <tr>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo2" class="accordion-toggle">Carlos</td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo2" class="accordion-toggle">Mathias</td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo2" class="accordion-toggle">Leme</td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo2" class="accordion-toggle">SP</td>
                                            <td data-bs-toggle="collapse" data-bs-target="#demo2" class="accordion-toggle">new</td>
                                            <td>
                                                <button class="btn btn-default btn-xs">
                                                    <i class="fas fa-eye text-dark"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr> --}}
                                            <td colspan="12" class="hiddenRow">
                                                <div class="accordian-body collapse" id="demo2">
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
                                                        <tr data-toggle="collapse" class="accordion-toggle" data-target="#demo2">
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
                                                        <tr>
                                                            <td colspan="12" class="hiddenRow">
                                                                <div class="accordian-body collapse" id="demo10">
                                                                    <table class="table table-striped">
                                                                    <thead>
                                                                        <tr class="info">
                                                                        <th>Job</th>
                                                                        </tr>
                                                                    </thead>
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
                </div>
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
@endsection
