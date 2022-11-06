@extends('opd.layouts.app')
@section('title', 'OPD | Laporan')

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
                        <li class="breadcrumb-item"><a href="{{ route('opd.dashboard.index') }}">Dashboard</a></li>
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
                <a class="nav-link" data-bs-toggle="tab" href="#tc_23" role="tab" aria-selected="false">TC. 23</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tc_24" role="tab" aria-selected="false">TC. 24</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_80" role="tab" aria-selected="false">E 80</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_81" role="tab" aria-selected="false">E 81</a>
            </li>
        </ul>

        <div class="card mb-5">
            <div class="card-body">
                <div class="tab-content">
                    {{-- TC 23 Start --}}
                    <div class="tab-pane fade active show" id="tc_23" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navTc23" data-bs-toggle="tab" data-bs-target="#tc_23_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="tc_23_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="data-table-responsive-wrapper">
                                            <div class="text-center">
                                                <h1>Tabel T-C.23</h1>
                                                <h3>Pencapaian Kinerja Pelayanan Perangkat Daerah {{Auth::user()->opd->nama}}</h3>
                                                <h3>{{ Auth::user()->opd->kabupaten->nama }}</h3>
                                                <h3>Tahun {{ $tahun }}</h3>
                                            </div>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">No</th>
                                                        <th rowspan="2">Indikator Kinerja sesuai Tugas dan Fungsi Perangkat Daerah</th>
                                                        <th rowspan="2">Target NSPK</th>
                                                        <th rowspan="2">Target IKK</th>
                                                        <th rowspan="2">Target Indikator Lainnya</th>
                                                        <th colspan="5">Target Renstra Perangkat Daerah Tahun Ke-{{$tahun}} </th>
                                                        <th colspan="5">Realisasi Capaian Tahun Ke-{{$tahun}}</th>
                                                        <th colspan="5">Rasio Capaian Pada Tahun Ke-{{$tahun}}</th>
                                                    </tr>
                                                    <tr>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                    </tr>
                                                    <tr>
                                                        <th>(1)</th>
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
                                                        <th>(18)</th>
                                                        <th>(19)</th>
                                                        <th>(20)</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- TC 23 End --}}

                    {{-- TC 24 Start --}}
                    <div class="tab-pane fade" id="tc_24" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navTc24" data-bs-toggle="tab" data-bs-target="#tc_24_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="tc_24_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="data-table-responsive-wrapper">
                                            <div class="text-center">
                                                <h1>Tabel T-C.24</h1>
                                                <h3>Anggaran dan Realisasi Pendanaan Pelayanan Perangkat Daerah {{Auth::user()->opd->nama}}</h3>
                                                <h3>{{ Auth::user()->opd->kabupaten->nama }}</h3>
                                                <h3>Tahun {{ $tahun }}</h3>
                                            </div>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">Uraian ***)</th>
                                                        <th colspan="5">Anggaran Pada Tahun Ke- {{$tahun}}</th>
                                                        <th colspan="5">Realisasi Anggaran Pada Tahun Ke- {{$tahun}}</th>
                                                        <th colspan="5">Rasio antara Realisasi dan Anggaran Tahun ke- {{$tahun}}</th>
                                                        <th colspan="2">Rata - Rata Pertumbuhan</th>
                                                    </tr>
                                                    <tr>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>1</th>
                                                        <th>2</th>
                                                        <th>3</th>
                                                        <th>4</th>
                                                        <th>5</th>
                                                        <th>Anggaran</th>
                                                        <th>Realisasi</th>
                                                    </tr>
                                                    <tr>
                                                        <th>(1)</th>
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
                                                        <th>(18)</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- TC 24 End --}}

                    {{-- E 80 Start --}}
                    <div class="tab-pane fade" id="e_80" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navE80" data-bs-toggle="tab" data-bs-target="#e_80_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="e_80_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="data-table-responsive-wrapper">
                                            <div class="text-center">
                                                <h1>Tabel E.80</h1>
                                                <h3>Evaluasi Terhadap Hasil Renstra Perangkat Daerah Lingkup {{ Auth::user()->opd->kabupaten->nama }} </h3>
                                                <h3>Renstra Perangkat Daerah {{Auth::user()->opd->nama}} {{ Auth::user()->opd->kabupaten->nama }}</h3>
                                                <h3>Periode Pelaksanaan {{ $tahun }}</h3>
                                            </div>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">No</th>
                                                        <th rowspan="2">Sasaran</th>
                                                        <th rowspan="2">Program/Kegiatan</th>
                                                        <th rowspan="2">Indikator Kinerja</th>
                                                        <th rowspan="2">Data Capaian Pada Awal Tahun Perencanaan</th>
                                                        <th rowspan="2" colspan="2">Target Capaian pada Akhir Tahun Perencanaan</th>
                                                        <th colspan="10">Target Renstra Perangkat Daerah kabupaten/kota Tahun ke- </th>
                                                        <th colspan="10">Realisasi Capaian Tahun ke- </th>
                                                        <th colspan="10">Rasio Capaian pada Tahun ke- </th>
                                                        <th rowspan="2">Unit Penanggung Jawab </th>
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
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 80 End --}}

                    {{-- E 81 Start --}}
                    <div class="tab-pane fade" id="e_81" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navE81" data-bs-toggle="tab" data-bs-target="#e_81_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="e_81_{{$tahun}}" role="tabpanel">
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="data-table-responsive-wrapper">
                                            <div class="text-center">
                                                <h1>Tabel E.81</h1>
                                                <h3>Evaluasi Terhadap Hasil Renja Perangkat Daerah Lingkup {{ Auth::user()->opd->kabupaten->nama }} </h3>
                                                <h3>Renstra Perangkat Daerah {{Auth::user()->opd->nama}} {{ Auth::user()->opd->kabupaten->nama }}</h3>
                                                <h3>Periode Pelaksanaan {{ $tahun }}</h3>
                                            </div>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">No</th>
                                                        <th rowspan="2">Sasaran</th>
                                                        <th rowspan="2">Program / Kegiatan</th>
                                                        <th rowspan="2">Indikator Kinerja Program (outcome)/ Kegiatan (output)</th>
                                                        <th rowspan="2" colspan="2">Target Renstra Perangkat Daerah pada Tahun {{$tahun}} (Akhir Periode Renstra Perangkat Daerah) </th>
                                                        <th rowspan="2" colspan="2">Realisasi Capaian Kinerja Renstra Perangkat Daerah sampai dengan Renja Perangkat Daerah Tahun Lalu (n-2)</th>
                                                        <th rowspan="2" colspan="2">Target Kinerja dan Anggaran Renja Perangkat Daerah Tahun berjalan (Tahun n-1) yang dievaluasi</th>
                                                        <th colspan="8">Realisasi Kinerja Pada Triwulan</th>
                                                        <th rowspan="2" colspan="2">Realisasi Capaian Kinerja dan Anggaran Renja Perangkat Daerah yang dievaluasi </th>
                                                        <th rowspan="2" colspan="2">Realisasi Kinerja dan Anggaran Renstra Perangkat Daerah s/d tahun {{$tahun}} (Akhir Tahun Pelaksanaan Renja Perangkat Daerah Tahun {{$tahun}}) </th>
                                                        <th rowspan="2" colspan="2">Tingkat Capaian Kinerja Dan Realisasi Anggaran Renstra Perangkat Daerah s/d tahun {{$tahun}} (%)</th>
                                                        <th rowspan="2">Unit Perangkat Daerah Penanggung Jawab</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2">I</th>
                                                        <th colspan="2">II</th>
                                                        <th colspan="2">III</th>
                                                        <th colspan="2">IV</th>
                                                    </tr>
                                                    <tr>
                                                        <th rowspan="2">1</th>
                                                        <th rowspan="2">2</th>
                                                        <th rowspan="2">3</th>
                                                        <th rowspan="2">4</th>
                                                        <th colspan="2">5</th>
                                                        <th colspan="2">6</th>
                                                        <th colspan="2">7</th>
                                                        <th colspan="2">8</th>
                                                        <th colspan="2">9</th>
                                                        <th colspan="2">10</th>
                                                        <th colspan="2">11</th>
                                                        <th colspan="2">12</th>
                                                        <th colspan="2">13 = 6 + 12</th>
                                                        <th colspan="2">14=13/5 x100%</th>
                                                        <th rowspan="2">15</th>
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
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 81 End --}}
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
@endsection