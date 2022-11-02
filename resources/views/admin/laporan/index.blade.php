@extends('admin.layouts.app')
@section('title', 'Admin | Laporan')

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
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Laporan</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <ul class="nav nav-tabs nav-tabs-title nav-tabs-line-title responsive-tabs" id="lineTitleTabsContainer" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#tc_14" role="tab" aria-selected="true">TC. 14</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tc_19" role="tab" aria-selected="false">TC. 19</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_79" role="tab" aria-selected="false">E. 79</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_78" role="tab" aria-selected="false">E. 78</a>
            </li>
        </ul>

        <div class="card mb-5">
            <div class="card-body">
                <div class="tab-content">
                    {{-- TC 14 Start --}}
                    <div class="tab-pane fade active show" id="tc_14" role="tabpanel">
                        <div class="text-center mb-3">
                            <h1>Tabel T-C.14</h1>
                            <h1>Program Pembangunan Daerah yang disertai Pagu Indikatif</h1>
                            <h1>{{ Auth::user()->kabupaten->nama }}</h1>
                        </div>
                        <div class="text-left">
                            {{-- <label for="" class="control-label">(*) = Program Prioritas, (**) = Program Pendukung</label> --}}
                        </div>
                        <!-- Table Start -->
                        <div class="data-table-responsive-wrapper">
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="3" colspan="3">Kode</th>
                                        <th rowspan="3" >Misi/Tujuan/Sasaran/ Program Pembangunan Daerah</th>
                                        <th rowspan="3" >Indikator Kinerja (tujuan/impact/ outcome)</th>
                                        <th rowspan="3" >Kondisi Kinerja Awal RPJMD (Tahun 0)</th>
                                        <th colspan="12">Capaian Kinerja Program dan Kerangka Pendanaan</th>
                                        <th rowspan="3">Perangkat Daerah Penanggung Jawab</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Tahun-1</th>
                                        <th colspan="2">Tahun-2</th>
                                        <th colspan="2">Tahun-3</th>
                                        <th colspan="2">Tahun-4</th>
                                        <th colspan="2">Tahun-5</th>
                                        <th colspan="2">Kondisi Kinerja pada akhir periode RPJMD</th>
                                    </tr>
                                    <tr>
                                        <th>target</th>
                                        <th>Rp</th>
                                        <th>target</th>
                                        <th>Rp</th>
                                        <th>target</th>
                                        <th>Rp</th>
                                        <th>target</th>
                                        <th>Rp</th>
                                        <th>target</th>
                                        <th>Rp</th>
                                        <th>target</th>
                                        <th>Rp</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3">(1)</th>
                                        <th>(2)</th>
                                        <th>(3)</th>
                                        <th>(4)</th>
                                        <th>(5)</th>
                                        <th>(6)</th>
                                        <th>(7)</th>
                                        <th>(8)</th>
                                        <th>(9)</th>
                                        <th>(10)</th>
                                        <th>(11)</th>
                                        <th>(12)</th>
                                        <th>(13)</th>
                                        <th>(14)</th>
                                        <th>(15)</th>
                                        <th>(16)</th>
                                        <th>(17)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {!!$tc_14!!}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- TC 14 End --}}

                    {{-- TC 19 Start --}}
                    <div class="tab-pane fade" id="tc_19" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navTc19" data-bs-toggle="tab" data-bs-target="#tc_19_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="tc_19_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
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
                                                        <th>Indikator Kinerja Program (outcome) / Kegiatan (output)</th>
                                                        <th colspan="2">Capaian Kinerja RPJMD pada Tahun {{$tahun}} Akhir Periode RPJMD</th>
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
                                                <tbody class="tbodyTc19" style="text-align: left">
                                                    {!!$tc_19!!}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- TC 19 End --}}

                    {{-- E 79 Start --}}
                    <div class="tab-pane fade" id="e_79" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navE79" data-bs-toggle="tab" data-bs-target="#e_79_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="e_79_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <table class="table table-striped table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">No</th>
                                                    <th rowspan="2">Sasaran</th>
                                                    <th colspan="4" rowspan="2">Kode</th>
                                                    <th rowspan="2">Urusan / Bidang Urusan Pemerintah Daerah dan Program / Kegiatan</th>
                                                    <th rowspan="2">Indikator Kinerja Program (outcome) / Kegiatan (output)</th>
                                                    <th colspan="2" rowspan="2">Capaian Kinerja RPJMD pada Tahun {{$tahun}} (Akhir Periode RPJMD)</th>
                                                    <th colspan="2" rowspan="2">Realisasi Capaian Kinerja RPJMD Kabupaten/kota sampai dengan RKPD Kabupaten/kota Tahun Lalu (n-2)</th>
                                                    <th colspan="2" rowspan="2">Target Kinerja dan Anggaran RKPD Kabupaten/kota Tahun Berjalan (Tahun n-1) yang dievaluasi</th>
                                                    <th colspan="8" rowspan="1">Realisasi Kinerja Pada Triwulan</th>
                                                    <th colspan="2" rowspan="2">Realisasi Capaian Kinerja dan Anggaran RKPD Kabupaten/kota yang Dievaluasi</th>
                                                    <th colspan="2" rowspan="2">Realisasi Kinerja dan Anggaran RPJMD Kabupaten/kota s/d Tahun  {{$tahun}}(Akhir Tahun Pelaksanaan RKPD tahun {{$tahun}})</th>
                                                    <th colspan="2" rowspan="2">Tingkat Capaian Kinerja dan Realisasi Anggaran RPJMD Kabupaten/kota s/d Tahun {{$tahun}} (%)</th>
                                                    <th rowspan="2">Perangkat Daerah Penanggung Jawab</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" rowspan="1">I</th>
                                                    <th colspan="2" rowspan="1">II</th>
                                                    <th colspan="2" rowspan="1">III</th>
                                                    <th colspan="2" rowspan="1">IV</th>
                                                </tr>
                                                <tr>
                                                    <th rowspan="2">1</th>
                                                    <th rowspan="2">2</th>
                                                    <th rowspan="2" colspan="4">3</th>
                                                    <th rowspan="2">4</th>
                                                    <th rowspan="2" colspan="1">5</th>
                                                    <th colspan="2" rowspan="1">6</th>
                                                    <th colspan="2">7</th>
                                                    <th colspan="2">8</th>
                                                    <th colspan="2" rowspan="1">9</th>
                                                    <th colspan="2" rowspan="1">10</th>
                                                    <th colspan="2" rowspan="1">11</th>
                                                    <th colspan="2" rowspan="1">12</th>
                                                    <th colspan="2" rowspan="1">13</th>
                                                    <th colspan="2" rowspan="1">14</th>
                                                    <th colspan="2" rowspan="1">15</th>
                                                    <th rowspan="1">16</th>
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
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tbodyE79" style="text-align: left">
                                                {!!$e_79!!}
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 79 End --}}

                    {{-- E 78 Start --}}
                    <div class="tab-pane fade" id="e_78" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navE78" data-bs-toggle="tab" data-bs-target="#e_78_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="e_78_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <table class="table table-striped table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">No</th>
                                                    <th rowspan="2">Sasaran</th>
                                                    <th rowspan="2">Program Prioritas</th>
                                                    <th rowspan="2">Indikator Kinerja</th>
                                                    <th rowspan="2">Data Capaian Pada Awal Tahun Perencanaan</th>
                                                    <th rowspan="2" colspan="2">Target Pada Akhir Tahun Perencanaan</th>
                                                    <th colspan="10">Target RPJMD Kabupaten/Kota Pada RKPD Kabupaten/Kota Tahun {{$tahun}}</th>
                                                    <th colspan="10">Capaian Target RPJMD Kabupaten/Kota Melalui Pelaksanaan RKPD Tahun {{$tahun}}</th>
                                                    <th colspan="10">Tingkat Capaian Target RPJMD Kabupaten/Kota Hasil Pelaksanaan RKPD Kabupaten/Kota Tahun {{$tahun}}(%)</th>
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
                                            <tbody class="tbodyE78" style="text-align: left">
                                                {!!$e_78!!}
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 78 End --}}
                </div>
            </div>
        </div>
    </div>
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
    $('.navTc19').click(function(){
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ route('admin.laporan.tc-19') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun
            },
            success: function(data){
                $('.tbodyTc19').html(data.tc_19);
            }
        });
    });

    $('.navE79').click(function(){
        var tahun = $(this).attr('data-tahun');
        $.ajax({
            url: "{{ route('admin.laporan.e-79') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun
            },
            success: function(data){
                $('.tbodyE79').html(data.e_79);
            }
        });
    });
</script>
@endsection
