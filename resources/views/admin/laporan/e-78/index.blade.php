@extends('admin.layouts.app')
@section('title', 'Admin | Laporan | E 78')

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
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">e 79</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                        <li class="breadcrumb-item"><a href="#">e 79</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->
        <div class="card mb-5">
            <div class="card-body w-100">
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Sasaran</th>
                            <th rowspan="2">Program Prioritas</th>
                            <th rowspan="2">Indikator Kinerja</th>
                            <th rowspan="2">Data Capaian Pada Awal Tahun Perencanaan</th>
                            <th rowspan="2" colspan="2">Target Pada Akhir Tahun Perencanaan</th>
                            <th colspan="10">Target RPJMD Kabupaten/Kota Pada RKPD Kabupaten/Kota Tahun Ke-</th>
                            <th colspan="10">Capaian Target RPJMD Kabupaten/Kota Melalui Pelaksanaan RKPD Tahun Ke-</th>
                            <th colspan="10">Tingkat Capaian Target RPJMD Kabupaten/Kota Hasil Pelaksanaan RKPD Kabupaten/Kota Tahun Ke-(%)</th>
                            <th colspan="2" rowspan="2">Capaian Pada Akhir Tahun Perencanaan</th>
                            <th colspan="2" rowspan="2">Rasio Capaian Akhir</th>
                        </tr>
                        <tr>
                            <th colspan="2">1</th>
                            <th colspan="2">2</th>
                            <th colspan="2">3</th>
                            <th colspan="2">4</th>
                            <th colspan="2">5</th>
                            <th colspan="2">1</th>
                            <th colspan="2">2</th>
                            <th colspan="2">3</th>
                            <th colspan="2">4</th>
                            <th colspan="2">5</th>
                            <th colspan="2">1</th>
                            <th colspan="2">2</th>
                            <th colspan="2">3</th>
                            <th colspan="2">4</th>
                            <th colspan="2">5</th>
                        </tr>
                        <tr>
                            <th rowspan="2">(1)</th>
                            <th rowspan="2">(2)</th>
                            <th rowspan="2">(3)</th>
                            <th rowspan="2">(4)</th>
                            <th rowspan="2">(5)</th>
                            <th colspan="2">(6)</th>
                            <th colspan="2">(7)</th>
                            <th colspan="2">(8)</th>
                            <th colspan="2">(9)</th>
                            <th colspan="2">(10)</th>
                            <th colspan="2">(11)</th>
                            <th colspan="2">(12)</th>
                            <th colspan="2">(13)</th>
                            <th colspan="2">(14)</th>
                            <th colspan="2">(15)</th>
                            <th colspan="2">(16)</th>
                            <th colspan="2">(17)</th>
                            <th colspan="2">(18)</th>
                            <th colspan="2">(19)</th>
                            <th colspan="2">(20)</th>
                            <th colspan="2">(21)</th>
                            <th colspan="2">(22)</th>
                            <th colspan="2">(23)</th>
                        </tr>
                        <tr>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                            <th>K</th>
                            <th>Rp</th>
                        </tr>
                    </thead>
                </table>
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
