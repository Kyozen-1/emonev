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
                <a class="nav-link active" data-bs-toggle="tab" id="nav_tc_14" href="#tc_14" role="tab" aria-selected="true">TC. 14</a>
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
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" role="tab" aria-selected="false">|</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tc_23" role="tab" aria-selected="false">TC. 23</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tc_24" role="tab" aria-selected="false">TC. 24</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#tc_27" role="tab" aria-selected="false">TC. 27</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_80" role="tab" aria-selected="false">E 80</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_81" role="tab" aria-selected="false">E 81</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#e_81_perubahan" role="tab" aria-selected="false">E 81 Perubahan</a>
            </li>
        </ul>

        <div class="card mb-5">
            <div class="card-body">
                <div class="tab-content">
                    {{-- TC 14 Start --}}
                    <div class="tab-pane fade active show" id="tc_14" role="tabpanel">
                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button
                                class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                <i data-acorn-icon="download" data-acorn-size="15"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                <a class="dropdown-item" href="{{ route('admin.laporan.tc-14.ekspor.pdf') }}">PDF</a>
                                <a class="dropdown-item" href="{{ route('admin.laporan.tc-14.ekspor.excel') }}">Excel</a>
                            </div>
                        </div>
                        <div class="text-center mb-3">
                            <h1>Tabel T-C.14</h1>
                            <h1>Program Pembangunan Daerah yang disertai Pagu Indikatif</h1>
                            <h1>{{ Auth::user()->kabupaten_id?Auth::user()->kabupaten->nama : '' }}</h1>
                        </div>
                        <div class="text-left">
                            {{-- <label for="" class="control-label">(*) = Program Prioritas, (**) = Program Pendukung</label> --}}
                        </div>
                        <!-- Table Start -->
                        <div class="table-responsive">
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
                                <tbody id="tbody14"></tbody>
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
                                        <div class="d-flex justify-content-between">
                                            <div></div>
                                            <button
                                                class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-haspopup="true"
                                            >
                                                <i data-acorn-icon="download" data-acorn-size="15"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                                <a class="dropdown-item" href="{{ route('admin.laporan.tc-19.ekspor.pdf', ['tahun'=>$tahun]) }}">PDF</a>
                                                <a class="dropdown-item" href="{{ route('admin.laporan.tc-19.ekspor.excel', ['tahun'=>$tahun]) }}">Excel</a>
                                            </div>
                                        </div>
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="table-responsive">
                                            <div class="text-center">
                                                <h1>Tabel T-C.19</h1>
                                                <h1>Evaluasi Hasil Pelaksanaan Perencanaan Daerah sampai dengan Tahun {{$tahun}} Berjalan</h1>
                                                <h1>{{ Auth::user()->kabupaten_id?Auth::user()->kabupaten->nama : '' }}</h1>
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
                                                <tbody id="tbodyTc19{{$tahun}}" style="text-align: left"></tbody>
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
                                        <div class="d-flex justify-content-between">
                                            <div></div>
                                            <button
                                                class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-haspopup="true"
                                            >
                                                <i data-acorn-icon="download" data-acorn-size="15"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                                <a class="dropdown-item" href="{{ route('admin.laporan.e-79.ekspor.pdf', ['tahun' => $tahun]) }}">PDF</a>
                                                <a class="dropdown-item" href="{{ route('admin.laporan.e-79.ekspor.excel', ['tahun' => $tahun]) }}">Excel</a>
                                            </div>
                                        </div>
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="table-responsive">
                                            <div class="text-center">
                                                <h1>Tabel E.79</h1>
                                                <h3>Evaluasi terhadap Hasil RKPD</h3>
                                                <h1>{{ Auth::user()->kabupaten_id?Auth::user()->kabupaten->nama : '' }}</h1>
                                                <h3>Tahun {{ $tahun }}</h3>
                                            </div>
                                            <table class="table table-striped table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">No</th>
                                                        <th rowspan="2">Sasaran</th>
                                                        <th colspan="4" rowspan="2">Kode</th>
                                                        <th rowspan="2">Urusan / Bidang Urusan Pemerintah Daerah dan Program / Kegiatan</th>
                                                        <th rowspan="2">Indikator Kinerja Program (outcome) / Kegiatan (output)</th>
                                                        <th colspan="2" rowspan="2">Capaian Kinerja RPJMD pada Tahun (Akhir Periode RPJMD)</th>
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
                                                <tbody id="tbodyE79{{$tahun}}" style="text-align: left"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 79 End --}}

                    {{-- E 78 Start --}}
                    <div class="tab-pane fade" id="e_78" role="tabpanel">
                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button
                                class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                <i data-acorn-icon="download" data-acorn-size="15"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                <a class="dropdown-item" href="{{ route('admin.laporan.e-78.ekspor.pdf') }}">PDF</a>
                                <a class="dropdown-item" href="{{ route('admin.laporan.e-78.ekspor.excel') }}">Excel</a>
                            </div>
                        </div>
                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                        <div class="table-responsive">
                            <div class="text-center">
                                <h1>Tabel E.78</h1>
                                <h3>Evaluasi Terhadap Hasil RPJMD</h3>
                                <h1>{{ Auth::user()->kabupaten_id?Auth::user()->kabupaten->nama : '' }}</h1>
                            </div>
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Sasaran</th>
                                        <th rowspan="2">Program Prioritas</th>
                                        <th rowspan="2">Indikator Kinerja</th>
                                        <th rowspan="2">Data Capaian Pada Awal Tahun Perencanaan</th>
                                        <th rowspan="2" colspan="2">Target Pada Akhir Tahun Perencanaan</th>
                                        <th colspan="10">Target RPJMD Kabupaten/Kota Pada RKPD Kabupaten/Kota Tahun </th>
                                        <th colspan="10">Capaian Target RPJMD Kabupaten/Kota Melalui Pelaksanaan RKPD Tahun </th>
                                        <th colspan="10">Tingkat Capaian Target RPJMD Kabupaten/Kota Hasil Pelaksanaan RKPD Kabupaten/Kota Tahun (%)</th>
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
                                <tbody id="tbodyE78" style="text-align: left"></tbody>
                            </table>
                        </div>
                    </div>
                    {{-- E 78 End --}}

                    {{-- TC 23 Start --}}
                    <div class="tab-pane fade" id="tc_23" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-6">
                                <h2 class="small-title">Filter By Opd</h2>
                                <select name="tc_23_opd_id" id="tc_23_opd_id" class="form-control">
                                    @foreach ($opds as $id => $nama)
                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6" style="text-align: right">
                                <h2 class="small-title">Ekspor Data</h2>
                                <div class="d-flex justify-content-between">
                                    <div></div>
                                    <button
                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                    >
                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                        <a class="dropdown-item tc_23_ekspor_pdf">PDF</a>
                                        <a class="dropdown-item tc_23_ekspor_excel">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="text-center">
                                <h1>Tabel T-C.23</h1>
                                <h3>Pencapaian Kinerja Pelayanan Perangkat Daerah -</h3>
                                <h3>Kabupaten Madiun</h3>
                            </div>
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Indikator Kinerja sesuai Tugas dan Fungsi Perangkat Daerah</th>
                                        <th rowspan="2">Target NSPK</th>
                                        <th rowspan="2">Target IKK</th>
                                        <th rowspan="2">Target Indikator Lainnya</th>
                                        <th colspan="5">Target Renstra Perangkat Daerah Tahun Ke- </th>
                                        <th colspan="5">Realisasi Capaian Tahun Ke-</th>
                                        <th colspan="5">Rasio Capaian Pada Tahun Ke-</th>
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
                                <tbody id="tbodyTc23"></tbody>
                            </table>
                        </div>
                    </div>
                    {{-- TC 23 End --}}

                    {{-- TC 24 Start --}}
                    <div class="tab-pane fade" id="tc_24" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-6">
                                <h2 class="small-title">Filter By Opd</h2>
                                <select name="tc_24_opd_id" id="tc_24_opd_id" class="form-control">
                                    @foreach ($opds as $id => $nama)
                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6" style="text-align: right">
                                <h2 class="small-title">Ekspor Data</h2>
                                <div class="d-flex justify-content-between">
                                    <div></div>
                                    <button
                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                    >
                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                        <a class="dropdown-item tc_24_ekspor_pdf">PDF</a>
                                        <a class="dropdown-item tc_24_ekspor_excel">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="data-table-responsive-wrapper">
                            <div class="text-center">
                                <h1>Tabel T-C.24</h1>
                                <h3>Anggaran dan Realisasi Pendanaan Pelayanan Perangkat Daerah -</h3>
                                <h3>Kabupaten Madiun</h3>
                            </div>
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Uraian ***)</th>
                                        <th colspan="5">Anggaran Pada Tahun Ke-</th>
                                        <th colspan="5">Realisasi Anggaran Pada Tahun Ke-</th>
                                        <th colspan="5">Rasio antara Realisasi dan Anggaran Tahun ke-</th>
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
                                <tbody id="tbodyTc24"></tbody>
                            </table>
                        </div>
                    </div>
                    {{-- TC 24 End --}}

                    {{-- TC 27 Start --}}
                    <div class="tab-pane fade" id="tc_27" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-6">
                                <h2 class="small-title">Filter By Opd</h2>
                                <select name="tc_27_opd_id" id="tc_27_opd_id" class="form-control">
                                    @foreach ($opds as $id => $nama)
                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6" style="text-align: right">
                                <h2 class="small-title">Ekspor Data</h2>
                                <div class="d-flex justify-content-between">
                                    <div></div>
                                    <button
                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                    >
                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                        <a class="dropdown-item tc_27_ekspor_pdf">PDF</a>
                                        <a class="dropdown-item tc_27_ekspor_excel">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="text-center">
                                <h1>Tabel T-C.27</h1>
                                <h3>Rencana Program, Kegiatan, dan Pendanaan Perangkat Daerah -</h3>
                                <h3>Kabupaten</h3>
                            </div>
                            <table class="table table-striped table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="3">Tujuan</th>
                                        <th rowspan="3">Sasaran</th>
                                        <th rowspan="3">Kode</th>
                                        <th rowspan="3">Program dan Kegiatan</th>
                                        <th rowspan="3">Indikator Kinerja Tujuan, Sasaran, Program (outcome) dan Kegiatan (output)</th>
                                        <th rowspan="3">Data Capaian Pada Tahun Awal Perencanaan</th>
                                        <th colspan="12">Target Kinerja Program dan Kerangka Pendanaan</th>
                                        <th rowspan="3">Unit Kerja Perangkat Daerah Penanggung-jawab </th>
                                        <th rowspan="3">Lokasi</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Tahun - 1</th>
                                        <th colspan="2">Tahun - 2</th>
                                        <th colspan="2">Tahun - 3</th>
                                        <th colspan="2">Tahun - 4</th>
                                        <th colspan="2">Tahun - 5</th>
                                        <th colspan="2">Kondisi Kinerja pada akhir periode Renstra Perangkat Daerah</th>
                                    </tr>
                                    <tr>
                                        <th>Target</th>
                                        <th>Rp</th>
                                        <th>Target</th>
                                        <th>Rp</th>
                                        <th>Target</th>
                                        <th>Rp</th>
                                        <th>Target</th>
                                        <th>Rp</th>
                                        <th>Target</th>
                                        <th>Rp</th>
                                        <th>Target</th>
                                        <th>Rp</th>
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
                                    </tr>
                                </thead>
                                <tbody id="tbodyTc27"></tbody>
                            </table>
                        </div>
                    </div>
                    {{-- TC 27 End --}}

                    {{-- E 80 Start --}}
                    <div class="tab-pane fade" id="e_80" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-6">
                                <h2 class="small-title">Filter By Opd</h2>
                                <select name="e_80_opd_id" id="e_80_opd_id" class="form-control">
                                    @foreach ($opds as $id => $nama)
                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6" style="text-align: right">
                                <h2 class="small-title">Ekspor Data</h2>
                                <div class="d-flex justify-content-between">
                                    <div></div>
                                    <button
                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                        type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                    >
                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                        <a class="dropdown-item e_80_ekspor_pdf">PDF</a>
                                        <a class="dropdown-item e_80_ekspor_excel">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="text-center">
                                <h1>Tabel E.80</h1>
                                <h3>Evaluasi Terhadap Hasil Renstra Perangkat Daerah Lingkup </h3>
                                <h3>Renstra Perangkat Daerah</h3>
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
                                <tbody id="tbodyE80" style="text-align: left"></tbody>
                            </table>
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
                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <h2 class="small-title">Filter By Opd</h2>
                                                <select id="e_81_opd_id_{{$tahun}}" class="form-control e_81_opd_id" data-tahun="{{$tahun}}">
                                                    @foreach ($opds as $id => $nama)
                                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6" style="text-align: right">
                                                <h2 class="small-title">Ekspor Data</h2>
                                                <div class="d-flex justify-content-between">
                                                    <div></div>
                                                    <button
                                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                                        type="button"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        aria-haspopup="true"
                                                    >
                                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                                        <a class="dropdown-item e_81_ekspor_pdf" data-tahun="{{$tahun}}">PDF</a>
                                                        <a class="dropdown-item e_81_ekspor_excel" data-tahun="{{$tahun}}">Excel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="table-responsive">
                                            <div class="text-center">
                                                <h1>Tabel E.81</h1>
                                                <h3>Evaluasi Terhadap Hasil Renja Perangkat Daerah Lingkup </h3>
                                                <h3>Renstra Perangkat Daerah</h3>
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
                                                <tbody id="tbodyE81{{$tahun}}" style="text-align: left"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 81 End --}}

                    {{-- E 81 Perubahan Start --}}
                    <div class="tab-pane fade" id="e_81_perubahan" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                @foreach ($tahuns as $tahun)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{$loop->first ? 'active' : ''}} navE81Perubahan" data-bs-toggle="tab" data-bs-target="#e_81_perubahan_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                            {{$tahun}}
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                @foreach ($tahuns as $tahun)
                                    <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="e_81_perubahan_{{$tahun}}" role="tabpanel">
                                        <div class="row mb-5">
                                            <div class="col-6">
                                                <h2 class="small-title">Filter By Opd</h2>
                                                <select id="e_81_perubahan_opd_id_{{$tahun}}" class="form-control e_81_perubahan_opd_id" data-tahun="{{$tahun}}">
                                                    @foreach ($opds as $id => $nama)
                                                        <option value="{{$id}}" @if($id == 16) selected @endif>{{$nama}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6" style="text-align: right">
                                                <h2 class="small-title">Ekspor Data</h2>
                                                <div class="d-flex justify-content-between">
                                                    <div></div>
                                                    <button
                                                        class="btn btn-icon btn-icon-only btn-sm btn-background-alternate mt-n2 shadow"
                                                        type="button"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        aria-haspopup="true"
                                                    >
                                                        <i data-acorn-icon="download" data-acorn-size="15"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end shadow">
                                                        <a class="dropdown-item e_81_perubahan_ekspor_pdf" data-tahun="{{$tahun}}">PDF</a>
                                                        <a class="dropdown-item e_81_perubahan_ekspor_excel" data-tahun="{{$tahun}}">Excel</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <h5 class="card-title">{{$tahun}}</h5> --}}
                                        <div class="table-responsive">
                                            <div class="text-center">
                                                <h1>Tabel E.81 Perubahan</h1>
                                                <h3>Evaluasi Terhadap Hasil Renja Perangkat Daerah Lingkup </h3>
                                                <h3>Renstra Perangkat Daerah</h3>
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
                                                <tbody id="tbodyE81Perubahan{{$tahun}}" style="text-align: left"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    {{-- E 81 Perubahan End --}}
                </div>
            </div>
        </div>
    </div>

    <div id="editTindakLanjutTriwulanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTindakLanjutTriwulanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditTindakLanjutTriwulan" action="{{ route('opd.laporan.e-81.edit.tindak-lanjut-triwulan') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_tindak_lanjut_triwulan_tahun" id="edit_tindak_lanjut_triwulan_tahun">
                        <input type="hidden" name="edit_tindak_lanjut_triwulan_opd_id" id="edit_tindak_lanjut_triwulan_opd_id">
                        <input type="hidden" name="edit_tindak_lanjut_triwulan_tahun_periode_id" id="edit_tindak_lanjut_triwulan_tahun_periode_id">
                        <div class="form-group position-relative mb-3">
                            <label for="edit_tindak_lanjut_triwulan_text" class="form-label">Tindak lanjut yang diperlukan dalam Triwulan berikutnya:</label>
                            <textarea name="edit_tindak_lanjut_triwulan_text" id="edit_tindak_lanjut_triwulan_text" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTindakLanjutRenjaModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTindakLanjutRenjaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditTindakLanjutRenja" action="{{ route('opd.laporan.e-81.edit.tindak-lanjut-renja') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="edit_tindak_lanjut_renja_tahun" id="edit_tindak_lanjut_renja_tahun">
                        <input type="hidden" name="edit_tindak_lanjut_renja_opd_id" id="edit_tindak_lanjut_renja_opd_id">
                        <input type="hidden" name="edit_tindak_lanjut_renja_tahun_periode_id" id="edit_tindak_lanjut_renja_tahun_periode_id">
                        <div class="form-group position-relative mb-3">
                            <label for="edit_tindak_lanjut_renja_text" class="form-label">Tindak lanjut yang diperlukan dalam Renja Perangkat Daerah Kabupaten Madiun Berikutnya:</label>
                            <textarea name="edit_tindak_lanjut_renja_text" id="edit_tindak_lanjut_renja_text" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan Perubahan</button>
                        </div>
                    </form>
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
    var tahun_awal = "{{ $tahun_awal }}";
    $(document).ready(function(){
        $('#tc_23_opd_id').select2();
        $('#tc_24_opd_id').select2();
        $('#tc_27_opd_id').select2();
        $('.e_81_opd_id').select2();
        $('.e_81_perubahan_opd_id').select2();
        $.ajax({
            url: "{{ route('admin.laporan.tc-14') }}",
            dataType:"json",
            success: function(data)
            {
                $('#tbody14').html(data.tc_14);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.tc-19.get-data') }}",
            dataType:"json",
            success: function(data)
            {
                $('#tbodyTc19'+tahun_awal).html(data.tc_19);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.e-79.get-data') }}",
            dataType: "json",
            success: function(data)
            {
                $('#tbodyE79'+tahun_awal).html(data.e_79);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.e-78') }}",
            dataType: "json",
            success: function(data)
            {
                $('#tbodyE78').html(data.e_78);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.tc-23') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16
            },
            success: function(data){
                $('#tbodyTc23').html(data.tc_23);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.tc-24') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16
            },
            success: function(data){
                $('#tbodyTc24').html(data.tc_24);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.tc-27') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16
            },
            success: function(data){
                $('#tbodyTc27').html(data.tc_27);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.e-80') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16
            },
            success: function(data){
                $('#tbodyE80').html(data.e_80);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.e-81') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16,
                tahun: tahun_awal
            },
            success: function(data){
                $('#tbodyE81'+tahun_awal).html(data.e_81);
            }
        });

        $.ajax({
            url: "{{ route('admin.laporan.e-81-perubahan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:16,
                tahun: tahun_awal
            },
            success: function(data){
                $('#tbodyE81Perubahan'+tahun_awal).html(data.e_81_perubahan);
            }
        });
    });

    $('#tc_23_opd_id').change(function(){
        var val = $(this).val();
        $.ajax({
            url: "{{ route('admin.laporan.tc-23') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:val
            },
            success: function(data){
                $('#tbodyTc23').html(data.tc_23);
            }
        });
    });

    $('.tc_23_ekspor_pdf').click(function(){
        var opd_id = $('#tc_23_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-23/ekspor/pdf') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('.tc_23_ekspor_excel').click(function(){
        var opd_id = $('#tc_23_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-23/ekspor/excel') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('#tc_24_opd_id').change(function(){
        var val = $(this).val();
        $.ajax({
            url: "{{ route('admin.laporan.tc-24') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:val
            },
            success: function(data){
                $('#tbodyTc24').html(data.tc_24);
            }
        });
    });

    $('.tc_24_ekspor_pdf').click(function(){
        var opd_id = $('#tc_24_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-24/ekspor/pdf') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('.tc_24_ekspor_excel').click(function(){
        var opd_id = $('#tc_24_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-24/ekspor/excel') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('#tc_27_opd_id').change(function(){
        var val = $(this).val();
        $.ajax({
            url: "{{ route('admin.laporan.tc-27') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:val
            },
            success: function(data){
                $('#tbodyTc27').html(data.tc_27);
            }
        });
    });

    $('.tc_27_ekspor_pdf').click(function(){
        var opd_id = $('#tc_27_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-27/ekspor/pdf') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('.tc_27_ekspor_excel').click(function(){
        var opd_id = $('#tc_27_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/tc-27/ekspor/excel') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('#e_80_opd_id').change(function(){
        var val = $(this).val();
        $.ajax({
            url: "{{ route('admin.laporan.e-80') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:val
            },
            success: function(data){
                $('#tbodyE80').html(data.e_80);
            }
        });
    });

    $('.e_80_ekspor_pdf').click(function(){
        var opd_id = $('#e_80_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-80/ekspor/pdf') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('.e_80_ekspor_excel').click(function(){
        var opd_id = $('#e_80_opd_id').val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-80/ekspor/excel') }}"+'/'+opd_id;
        document.body.appendChild(a);
        a.click();
    });

    $('#nav_tc_14').click(function(){
        $.ajax({
            url: "{{ route('admin.laporan.tc-14') }}",
            dataType:"json",
            success: function(data)
            {
                $('#tbody14').html(data.tc_14);
            }
        });
    });

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
                $('#tbodyTc19'+tahun).html(data.tc_19);
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
                $('#tbodyE79'+tahun).html(data.e_79);
            }
        });
    });

    $('.navE81').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_opd_id_'+tahun).val();
        $.ajax({
            url: "{{ route('admin.laporan.e-81') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun,
                opd_id: opd_id
            },
            success: function(data){
                $('#tbodyE81'+tahun).html(data.e_81);
            }
        });
    });

    $('.e_81_opd_id').change(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_opd_id_'+tahun).val();
        $.ajax({
            url: "{{ route('admin.laporan.e-81') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:opd_id,
                tahun: tahun
            },
            success: function(data){
                $('#tbodyE81'+tahun).html(data.e_81);
            }
        });
    });

    $('.e_81_ekspor_pdf').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_opd_id_'+tahun).val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-81/ekspor/pdf') }}"+'/'+opd_id+'/'+tahun;
        document.body.appendChild(a);
        a.click();
    });

    $('.e_81_ekspor_excel').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_opd_id_'+tahun).val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-81/ekspor/excel') }}"+'/'+opd_id+'/'+tahun;
        document.body.appendChild(a);
        a.click();
    });

    $('.navE81Perubahan').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_perubahan_opd_id_'+tahun).val();
        $.ajax({
            url: "{{ route('admin.laporan.e-81-perubahan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                tahun:tahun,
                opd_id: opd_id
            },
            success: function(data){
                $('#tbodyE81Perubahan'+tahun).html(data.e_81_perubahan);
            }
        });
    });

    $('.e_81_perubahan_opd_id').change(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_perubahan_opd_id_'+tahun).val();
        $.ajax({
            url: "{{ route('admin.laporan.e-81-perubahan') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                opd_id:opd_id,
                tahun: tahun
            },
            success: function(data){
                $('#tbodyE81Perubahan'+tahun).html(data.e_81_perubahan);
            }
        });
    });

    $('.e_81_perubahan_ekspor_pdf').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_perubahan_opd_id_'+tahun).val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-81/perubahan/ekspor/pdf') }}"+'/'+opd_id+'/'+tahun;
        document.body.appendChild(a);
        a.click();
    });

    $('.e_81_perubahan_ekspor_excel').click(function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $('#e_81_perubahan_opd_id_'+tahun).val();
        var a = document.createElement('a');
        a.href = "{{ url('/admin/laporan/e-81/perubahan/ekspor/excel') }}"+'/'+opd_id+'/'+tahun;
        document.body.appendChild(a);
        a.click();
    });

    $(document).on('click', '.edit-tindak-lanjut-triwulan', function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $(this).attr('data-opd-id');
        var tahun_periode_id = $(this).attr('data-tahun-periode-id');

        $('#edit_tindak_lanjut_triwulan_tahun').val(tahun);
        $('#edit_tindak_lanjut_triwulan_opd_id').val(opd_id);
        $('#edit_tindak_lanjut_triwulan_tahun_periode_id').val(tahun_periode_id);

        $('#editTindakLanjutTriwulanModal').modal('show');
    });

    $(document).on('click', '.edit-tindak-lanjut-renja', function(){
        var tahun = $(this).attr('data-tahun');
        var opd_id = $(this).attr('data-opd-id');
        var tahun_periode_id = $(this).attr('data-tahun-periode-id');

        $('#edit_tindak_lanjut_renja_tahun').val(tahun);
        $('#edit_tindak_lanjut_renja_opd_id').val(opd_id);
        $('#edit_tindak_lanjut_renja_tahun_periode_id').val(tahun_periode_id);

        $('#editTindakLanjutRenjaModal').modal('show');
    });
</script>
@endsection
