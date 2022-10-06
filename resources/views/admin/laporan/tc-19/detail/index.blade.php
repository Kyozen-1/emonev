@extends('admin.layouts.app')
@section('title', 'Admin | Laporan | TC 19 |'.$tahun)

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
            <h1 class="mb-0 pb-0 display-4" id="title">TC 19</h1>
            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                <ul class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.laporan.tc-19.index') }}">TC 19</a></li>
                    <li class="breadcrumb-item"><a href="#">{{$tahun}}</a></li>
                </ul>
            </nav>
        </div>
        <!-- Title End -->
        </div>
    </div>
    <!-- Title and Top Buttons End -->
    <div class="card mb-5">
        <div class="card-body">
            <!-- Table Start -->
            <div class="data-table-responsive-wrapper">
                <div class="text-center">
                    <h1>Tabel T-C.19</h1>
                    <h1>Evaluasi Hasil Pelaksanaan Perencanaan Daerah sampai dengan Tahun {{$tahun}} Berjalan</h1>
                    <h1>{{ Auth::user()->kabupaten->nama }}</h1>
                </div>
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th colspan="4">Kode</th>
                            <th>Urusan / Bidang Urusan Pemerintah Daerah dan Program / Kegiatan</th>
                            <th>Inidikator Kinerja Program (outcome) / Kegiatan (output)</th>
                            <th colspan="2">Capaian Kinerja RPJMD pada Tahun Akhir Periode RPJMD</th>
                            <th colspan="2">Realisasi Capaian Kinerja RKPD s/d Tahun Lalu (n-2)</th>
                            <th colspan="2">Target Kinerja dan Anggaran RKPD Tahun Berjalan yang dievaluasi (Tahun n-1)</th>
                            <th colspan="2">Realisasi Capaian Kinerja dan Anggaran RKPD yang dievaluasi (tahun n-1)</th>
                            <th colspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran RKPD (%)</th>
                            <th colspan="2">Realisasi Kinerja dan Anggaran RKPD s/d Tahun n-1</th>
                            <th colspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran RPJMD s/d Tahun n-1 (%)</th>
                            <th>Perangkat Daerah Penanggung Jawab</th>
                            <th>Ket.</th>
                        </tr>
                        <tr>
                            <th rowspan="2">1</th>
                            <th colspan="4" rowspan="2">2</th>
                            <th rowspan="2">3</th>
                            <th rowspan="2">4</th>
                            <th colspan="2">5</th>
                            <th colspan="2">6</th>
                            <th colspan="2">7</th>
                            <th colspan="2">8</th>
                            <th colspan="2">9 = 8/7 x 100%</th>
                            <th colspan="2">10 = 6 + 8</th>
                            <th colspan="2">11 = 10/5 x 100%</th>
                            <th rowspan="2">12</th>
                            <th rowspan="2">13</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        {!! $html !!}
                    </tbody>
                </table>
            </div>
            <!-- Table End -->
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
