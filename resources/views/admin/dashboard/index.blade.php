@extends('admin.layouts.app')
@section('title', 'Admin | Dashboard')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" />
@endsection

@section('content')
<div class="container">
    <!-- Title and Top Buttons Start -->
    <div class="page-title-container">
        <div class="row">
        <!-- Title Start -->
        <div class="col-12 col-md-7">
            <h1 class="mb-0 pb-0 display-4" id="title">Dashboard</h1>
            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                <ul class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                </ul>
            </nav>
        </div>
        <!-- Title End -->
        </div>
    </div>
    <!-- Title and Top Buttons End -->

    <!-- Content Start -->
    <ul class="nav nav-tabs nav-tabs-title nav-tabs-line-title responsive-tabs" id="lineTitleTabsContainer" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#dashboardVisiMisiTab" role="tab" aria-selected="true">Dashboard Visi Misi</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#dashboardPendapatanPembiayaanBelanjaTab" role="tab" aria-selected="false">Dashboard Pendapatan, Pembiayaan, dan Belanja</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active show" id="dashboardVisiMisiTab" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="card-title mb-3">Visi: "Terwujudnya Kabupaten Madiun Aman, Mandiri, Sejahtera dan Berakhlak"</h5>
                                </div>
                                <div class="col-2" style="text-align: right">
                                    <div class="form-group position-relative">
                                        <select name="tahun" id="tahun">
                                            <option value="">Pilih Tahun</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="card">
                                        <div class="card-body border-1">
                                            <h2 class="small-title">Total Ketercapaian Misi dan Anggaran</h2>
                                            <h3 class="card-title mb-3">Baik (71.01)</h3>
                                            <div class="row">
                                                <div class="col">
                                                    <h2 class="small-title">Misi</h2>
                                                    <div id="grafik_misi"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <h2 class="small-title">Anggaran</h2>
                                                    <div id="grafik_anggaran"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div id="grafik_misi_anggaran"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-title mb-3">1.1 Mewujudkan Pemerintahan Yang Baik (Good Government) <span class="badge bg-primary">+70.70% <i data-acorn-icon="trend-up"></i> Dari 100%</span></h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title mb-3">Tujuan: <span class="badge bg-primary">1.1.1 Terwujudnya Pemerintahan yang baik</span></h5>
                                            <h5 class="card-title mb-3">Sasaran: <span class="badge"><select name="" id="" class="form-control bg-primary text-white">
                                                <option value="">1.1.1.2 Meningkatnya pengelolaan keuangan dan pengawasan penyelenggaraan pemerintah daerah</option>
                                                <option value="1">1.1.1.2 Meningkatnya pengelolaan keuangan dan pengawasan penyelenggaraan pemerintah daerah</option>
                                            </select></span></h5>
                                            <hr class="text-primary" style="height: 2px">
                                            <div class="row mb-3">
                                                <div class="col-12 col-md-8">
                                                    <h5 class="card-title mb-3">Indikator Kinerja Tujuan dan Sasaran</h5>
                                                    <small class="text-muted">Cat: Pilih salah satu indikator</small>
                                                    <p class="text-secondary mt-3"><i data-acorn-icon="check-circle"></i> Opini BPK</p>
                                                    <p class="text-primary"><i data-acorn-icon="check-circle"></i> Meningkatnya pengelolaan keuangan dan pengawasan penyelenggaran pemerintahan daerah</p>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3">Capaian Kinerja RPJMD</h5>
                                                            <ul class="nav nav-pills responsive-tabs mb-3" role="tablist">
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#first3" role="tab" aria-selected="true" type="button">
                                                                    2019
                                                                    </button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#second3" role="tab" aria-selected="false" type="button">2020</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#third3" role="tab" aria-selected="false" type="button">2021</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fourth3" role="tab" aria-selected="false" type="button">2022</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fifth3" role="tab" aria-selected="false" type="button">2023</button>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane fade active show" id="first3" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_sasaran_indikator_2019"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_sasaran_indikator_2019"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="second3" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_sasaran_indikator_2020"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_sasaran_indikator_2020"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="third3" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_sasaran_indikator_2021"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_sasaran_indikator_2021"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="fourth3" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_sasaran_indikator_2022"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_sasaran_indikator_2022"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="fifth3" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_sasaran_indikator_2023"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_sasaran_indikator_2023"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-md-8">
                                                    <h5 class="card-title mb-3">Indikator Kinerja Program</h5>
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Pilih Program:</label>
                                                        <h5 class="card-title mb-3"><span class="badge"><select name="" id="" class="form-control bg-primary text-white">
                                                            <option value="">1.1.1.9 Program Optimalisasi Pengelolaan Pajak Dae...</option>
                                                            <option value="1">1.1.1.9 Program Optimalisasi Pengelolaan Pajak Dae...</option>
                                                        </select></span></h5>
                                                    </div>
                                                    <h2 class="small-title">Perangkat Daerah Penanggung Jawab: <span class="badge bg-primary">Dinas Pekerjaan Umum dan Tata Ruang</span></h2>
                                                    <h5 class="card-title mb-3">Riwayat Pencarian</h5>
                                                    <p class="text-primary mt-3"><i data-acorn-icon="check-circle"></i>1.2.1.1 Program Peningkatan Kualitas Pembangunan..</p>
                                                    <p class="text-secondary"><i data-acorn-icon="check-circle"></i>1.2.1.2 Program Pembangunan dan Pemeliharan Ja..</p>
                                                    <p class="text-secondary"><i data-acorn-icon="check-circle"></i>1.2.1.3 Program Pembangunan dan Pemeliharan Salu..</p>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3">Capaian Kinerja RPJMD</h5>
                                                            <ul class="nav nav-pills responsive-tabs mb-3" role="tablist">
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#first4" role="tab" aria-selected="true" type="button">
                                                                    2019
                                                                    </button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#second4" role="tab" aria-selected="false" type="button">2020</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#third4" role="tab" aria-selected="false" type="button">2021</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fourth4" role="tab" aria-selected="false" type="button">2022</button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fifth5" role="tab" aria-selected="false" type="button">2023</button>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content">
                                                                <div class="tab-pane fade active show" id="first4" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_program_2019"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_program_2019"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="second4" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_program_2020"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_program_2020"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="third4" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_program_2021"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_program_2021"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="fourth4" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_program_2022"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_program_2022"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="fifth5" role="tabpanel">
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Kinerja</h5>
                                                                            <div id="grafik_kinerja_program_2023"></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <h5 class="card-title">Anggaran</h5>
                                                                            <div id="grafik_anggaran_program_2023"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="dashboardPendapatanPembiayaanBelanjaTab" role="tabpanel">
            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="h-100 row g-0 card-body align-items-center">
                            <div class="col-auto">
                                <div class="bg-gradient-light sw-6 sh-6 rounded-md d-flex justify-content-center align-items-center">
                                    <i data-acorn-icon="loaf" class="text-white"></i>
                                </div>
                            </div>
                            <div class="col sh-6 ps-3 d-flex flex-column justify-content-center">
                                <div class="heading mb-0 d-flex align-items-center lh-1-25">PENDAPATAN DAERAH</div>
                                <div class="row g-0">
                                    <div class="col-auto">
                                        <div class="cta-2 text-primary">Rp. 54,3 M</div>
                                    </div>
                                    <div class="col text-success d-flex align-items-center ps-3">
                                        <i data-acorn-icon="arrow-top" class="me-1" data-acorn-size="13"></i>
                                        <span class="text-medium">+18.4% Dari Bulan Kemarin</span>
                                    </div>
                                </div>
                                <div class="heading mb-0 d-flex align-items-center lh-1-25"><a href="#" class="text-decoration-none">Selengkapnya</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="h-100 row g-0 card-body align-items-center">
                            <div class="col-auto">
                                <div class="bg-gradient-light sw-6 sh-6 rounded-md d-flex justify-content-center align-items-center">
                                    <i data-acorn-icon="loaf" class="text-white"></i>
                                </div>
                            </div>
                            <div class="col sh-6 ps-3 d-flex flex-column justify-content-center">
                                <div class="heading mb-0 d-flex align-items-center lh-1-25">PEMBIAYAAN DAERAH</div>
                                <div class="row g-0">
                                    <div class="col-auto">
                                        <div class="cta-2 text-primary">Rp. 54,3 M</div>
                                    </div>
                                    <div class="col text-success d-flex align-items-center ps-3">
                                        <i data-acorn-icon="arrow-top" class="me-1" data-acorn-size="13"></i>
                                        <span class="text-medium">+18.4% Dari Bulan Kemarin</span>
                                    </div>
                                </div>
                                <div class="heading mb-0 d-flex align-items-center lh-1-25"><a href="#" class="text-decoration-none">Selengkapnya</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card">
                        <div class="h-100 row g-0 card-body align-items-center">
                            <div class="col-auto">
                                <div class="bg-gradient-light sw-6 sh-6 rounded-md d-flex justify-content-center align-items-center">
                                    <i data-acorn-icon="loaf" class="text-white"></i>
                                </div>
                            </div>
                            <div class="col sh-6 ps-3 d-flex flex-column justify-content-center">
                                <div class="heading mb-0 d-flex align-items-center lh-1-25">BELANJA DAERAH</div>
                                <div class="row g-0">
                                    <div class="col-auto">
                                        <div class="cta-2 text-primary">Rp. 54,3 M</div>
                                    </div>
                                    <div class="col text-danger d-flex align-items-center ps-3">
                                        <i data-acorn-icon="trend-down" class="me-1" data-acorn-size="13"></i>
                                        <span class="text-medium">-2% Dari Bulan Kemarin</span>
                                    </div>
                                </div>
                                <div class="heading mb-0 d-flex align-items-center lh-1-25"><a href="#" class="text-decoration-none">Selengkapnya</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <ul class="nav nav-tabs nav-tabs-line card-header-tabs responsive-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#first" role="tab" type="button" aria-selected="true">Pendapatan Daerah</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#second" role="tab" type="button" aria-selected="false">Pembiayaan Daerah</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#third" role="tab" type="button" aria-selected="false">Belanja Daerah</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="first" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-8">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <h5 class="card-title mb-3">Tren Pendapatan</h5>
                                            </div>
                                            <div class="col d-flex input-daterange">
                                                <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly /> - <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                                            </div>
                                        </div>
                                        <div id="grafik_tren_pendapatan"></div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <h5 class="card-title mb-3">Komposisi Sumber Pendapatan</h5>
                                        <div id="grafik_komposisi_sumber_pendapatan"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="card-title mb-3">Rencana vs Realisasi Pendapatan Tahun Berjalan</h5>
                                        <div id="grafik_rencana_realisasi_pendapatan"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="second" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-8">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <h5 class="card-title mb-3">Tren Pendapatan</h5>
                                            </div>
                                            <div class="col d-flex input-daterange">
                                                <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly /> - <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                                            </div>
                                        </div>
                                        <div id="grafik_tren_pendapatan_pembiayaan_daerah"></div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <h5 class="card-title mb-3">Komposisi Sumber Pendapatan</h5>
                                        <div id="grafik_komposisi_sumber_pendapatan_pembiayaan_daerah"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="card-title mb-3">Rencana vs Realisasi Pendapatan Tahun Berjalan</h5>
                                        <div id="grafik_rencana_realisasi_pendapatan_pembiayaan_daerah"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="third" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-12 col-md-8">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <h5 class="card-title mb-3">Tren Pendapatan</h5>
                                            </div>
                                            <div class="col d-flex input-daterange">
                                                <input type="text" name="from_date" id="from_date" class="form-control" placeholder="From Date" readonly /> - <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date" readonly />
                                            </div>
                                        </div>
                                        <div id="grafik_tren_pendapatan_belanja_daerah"></div>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <h5 class="card-title mb-3">Komposisi Sumber Pendapatan</h5>
                                        <div id="grafik_komposisi_sumber_pendapatan_belanja_daerah"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="card-title mb-3">Rencana vs Realisasi Pendapatan Tahun Berjalan</h5>
                                        <div id="grafik_rencana_realisasi_pendapatan_belanja_daerah"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->
</div>
@endsection

@section('js')
    <script src="{{ asset('js/apexcharts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    <script>
        $(document).ready(function(){
            $('.input-daterange').datepicker({
                todayBtn:'linked',
                format:'yyyy-mm-dd',
                autoclose:true
            });
            var grafik_misi = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_misi = new ApexCharts(document.querySelector("#grafik_misi"), grafik_misi);
            chart_grafik_misi.render();

            var grafik_anggaran = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran = new ApexCharts(document.querySelector("#grafik_anggaran"), grafik_anggaran);
            chart_grafik_anggaran.render();

            var grafik_misi_anggaran = {
                series: [{
                    name: 'Net Profit',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Revenue',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                }],
                chart: {
                    type: 'bar',
                    height: 700,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                        return "$ " + val + " thousands"
                        }
                    },
                    style: {
                        fontSize: '1rem',
                    },
                }
            };

            var chart_grafik_misi_anggaran = new ApexCharts(document.querySelector("#grafik_misi_anggaran"), grafik_misi_anggaran);
            chart_grafik_misi_anggaran.render();

            var grafik_kinerja_sasaran_indikator_2019 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_sasaran_indikator_2019 = new ApexCharts(document.querySelector("#grafik_kinerja_sasaran_indikator_2019"), grafik_kinerja_sasaran_indikator_2019);
            chart_grafik_kinerja_sasaran_indikator_2019.render();

            var grafik_anggaran_sasaran_indikator_2019 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_sasaran_indikator_2019 = new ApexCharts(document.querySelector("#grafik_anggaran_sasaran_indikator_2019"), grafik_anggaran_sasaran_indikator_2019);
            chart_grafik_anggaran_sasaran_indikator_2019.render();

            var grafik_kinerja_sasaran_indikator_2020 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_sasaran_indikator_2020 = new ApexCharts(document.querySelector("#grafik_kinerja_sasaran_indikator_2020"), grafik_kinerja_sasaran_indikator_2020);
            chart_grafik_kinerja_sasaran_indikator_2020.render();

            var grafik_anggaran_sasaran_indikator_2020 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_sasaran_indikator_2020 = new ApexCharts(document.querySelector("#grafik_anggaran_sasaran_indikator_2020"), grafik_anggaran_sasaran_indikator_2020);
            chart_grafik_anggaran_sasaran_indikator_2020.render();

            var grafik_kinerja_sasaran_indikator_2021 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_sasaran_indikator_2021 = new ApexCharts(document.querySelector("#grafik_kinerja_sasaran_indikator_2021"), grafik_kinerja_sasaran_indikator_2021);
            chart_grafik_kinerja_sasaran_indikator_2021.render();

            var grafik_anggaran_sasaran_indikator_2021 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_sasaran_indikator_2021 = new ApexCharts(document.querySelector("#grafik_anggaran_sasaran_indikator_2021"), grafik_anggaran_sasaran_indikator_2021);
            chart_grafik_anggaran_sasaran_indikator_2021.render();

            var grafik_kinerja_sasaran_indikator_2022 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_sasaran_indikator_2022 = new ApexCharts(document.querySelector("#grafik_kinerja_sasaran_indikator_2022"), grafik_kinerja_sasaran_indikator_2022);
            chart_grafik_kinerja_sasaran_indikator_2022.render();

            var grafik_anggaran_sasaran_indikator_2022 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_sasaran_indikator_2022 = new ApexCharts(document.querySelector("#grafik_anggaran_sasaran_indikator_2022"), grafik_anggaran_sasaran_indikator_2022);
            chart_grafik_anggaran_sasaran_indikator_2022.render();

            var grafik_kinerja_sasaran_indikator_2023 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_sasaran_indikator_2023 = new ApexCharts(document.querySelector("#grafik_kinerja_sasaran_indikator_2023"), grafik_kinerja_sasaran_indikator_2023);
            chart_grafik_kinerja_sasaran_indikator_2023.render();

            var grafik_anggaran_sasaran_indikator_2023 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_sasaran_indikator_2023 = new ApexCharts(document.querySelector("#grafik_anggaran_sasaran_indikator_2023"), grafik_anggaran_sasaran_indikator_2023);
            chart_grafik_anggaran_sasaran_indikator_2023.render();

            var grafik_kinerja_program_2019 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_program_2019 = new ApexCharts(document.querySelector("#grafik_kinerja_program_2019"), grafik_kinerja_program_2019);
            chart_grafik_kinerja_program_2019.render();

            var grafik_anggaran_program_2019 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_program_2019 = new ApexCharts(document.querySelector("#grafik_anggaran_program_2019"), grafik_anggaran_program_2019);
            chart_grafik_anggaran_program_2019.render();

            var grafik_kinerja_program_2020 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_program_2020 = new ApexCharts(document.querySelector("#grafik_kinerja_program_2020"), grafik_kinerja_program_2020);
            chart_grafik_kinerja_program_2020.render();

            var grafik_anggaran_program_2020 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_program_2020 = new ApexCharts(document.querySelector("#grafik_anggaran_program_2020"), grafik_anggaran_program_2020);
            chart_grafik_anggaran_program_2020.render();

            var grafik_kinerja_program_2021 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_program_2021 = new ApexCharts(document.querySelector("#grafik_kinerja_program_2021"), grafik_kinerja_program_2021);
            chart_grafik_kinerja_program_2021.render();

            var grafik_anggaran_program_2021 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_program_2021 = new ApexCharts(document.querySelector("#grafik_anggaran_program_2021"), grafik_anggaran_program_2021);
            chart_grafik_anggaran_program_2021.render();

            var grafik_kinerja_program_2022 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_program_2022 = new ApexCharts(document.querySelector("#grafik_kinerja_program_2022"), grafik_kinerja_program_2022);
            chart_grafik_kinerja_program_2022.render();

            var grafik_anggaran_program_2022 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_program_2022 = new ApexCharts(document.querySelector("#grafik_anggaran_program_2022"), grafik_anggaran_program_2022);
            chart_grafik_anggaran_program_2022.render();

            var grafik_kinerja_program_2023 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_kinerja_program_2023 = new ApexCharts(document.querySelector("#grafik_kinerja_program_2023"), grafik_kinerja_program_2023);
            chart_grafik_kinerja_program_2023.render();

            var grafik_anggaran_program_2023 = {
                series: [44, 55, 41, 17, 15],
                chart: {
                    type: 'donut',
                    toolbar: {
                        show: false
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 320
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_anggaran_program_2023 = new ApexCharts(document.querySelector("#grafik_anggaran_program_2023"), grafik_anggaran_program_2023);
            chart_grafik_anggaran_program_2023.render();

            var grafik_tren_pendapatan = {
                series: [{
                    name: "STOCK ABC",
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                yaxis: {
                    opposite: true
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart_grafik_tren_pendapatan = new ApexCharts(document.querySelector("#grafik_tren_pendapatan"), grafik_tren_pendapatan);
            chart_grafik_tren_pendapatan.render();

            var grafik_komposisi_sumber_pendapatan = {
                series: [44, 55, 13, 43, 22],
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_komposisi_sumber_pendapatan = new ApexCharts(document.querySelector("#grafik_komposisi_sumber_pendapatan"), grafik_komposisi_sumber_pendapatan);
            chart_grafik_komposisi_sumber_pendapatan.render();

            var grafik_rencana_realisasi_pendapatan = {
                series: [{
                    name: 'Net Profit',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Revenue',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                }],
                chart: {
                    type: 'bar',
                    height: 500,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                        return "$ " + val + " thousands"
                        }
                    },
                    style: {
                        fontSize: '1rem',
                    },
                }
            };

            var chart_grafik_rencana_realisasi_pendapatan = new ApexCharts(document.querySelector("#grafik_rencana_realisasi_pendapatan"), grafik_rencana_realisasi_pendapatan);
            chart_grafik_rencana_realisasi_pendapatan.render();

            var grafik_tren_pendapatan_pembiayaan_daerah = {
                series: [{
                    name: "STOCK ABC",
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                yaxis: {
                    opposite: true
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart_grafik_tren_pendapatan_pembiayaan_daerah = new ApexCharts(document.querySelector("#grafik_tren_pendapatan_pembiayaan_daerah"), grafik_tren_pendapatan_pembiayaan_daerah);
            chart_grafik_tren_pendapatan_pembiayaan_daerah.render();

            var grafik_komposisi_sumber_pendapatan_pembiayaan_daerah = {
                series: [44, 55, 13, 43, 22],
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_komposisi_sumber_pendapatan_pembiayaan_daerah = new ApexCharts(document.querySelector("#grafik_komposisi_sumber_pendapatan_pembiayaan_daerah"), grafik_komposisi_sumber_pendapatan_pembiayaan_daerah);
            chart_grafik_komposisi_sumber_pendapatan_pembiayaan_daerah.render();

            var grafik_rencana_realisasi_pendapatan_pembiayaan_daerah = {
                series: [{
                    name: 'Net Profit',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Revenue',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                }],
                chart: {
                    type: 'bar',
                    height: 500,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                        return "$ " + val + " thousands"
                        }
                    },
                    style: {
                        fontSize: '1rem',
                    },
                }
            };

            var chart_grafik_rencana_realisasi_pendapatan_pembiayaan_daerah = new ApexCharts(document.querySelector("#grafik_rencana_realisasi_pendapatan_pembiayaan_daerah"), grafik_rencana_realisasi_pendapatan_pembiayaan_daerah);
            chart_grafik_rencana_realisasi_pendapatan_pembiayaan_daerah.render();

            var grafik_tren_pendapatan_belanja_daerah = {
                series: [{
                    name: "STOCK ABC",
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'straight'
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                yaxis: {
                    opposite: true
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    style: {
                        fontSize: '2rem',
                    },
                },
                legend: {
                    horizontalAlign: 'left'
                }
            };

            var chart_grafik_tren_pendapatan_belanja_daerah = new ApexCharts(document.querySelector("#grafik_tren_pendapatan_belanja_daerah"), grafik_tren_pendapatan_belanja_daerah);
            chart_grafik_tren_pendapatan_belanja_daerah.render();

            var grafik_komposisi_sumber_pendapatan_belanja_daerah = {
                series: [44, 55, 13, 43, 22],
                chart: {
                    width: 380,
                    type: 'pie',
                },
                labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            var chart_grafik_komposisi_sumber_pendapatan_belanja_daerah = new ApexCharts(document.querySelector("#grafik_komposisi_sumber_pendapatan_belanja_daerah"), grafik_komposisi_sumber_pendapatan_belanja_daerah);
            chart_grafik_komposisi_sumber_pendapatan_belanja_daerah.render();

            var grafik_rencana_realisasi_pendapatan_belanja_daerah = {
                series: [{
                    name: 'Net Profit',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Revenue',
                    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
                }],
                chart: {
                    type: 'bar',
                    height: 500,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                        return "$ " + val + " thousands"
                        }
                    },
                    style: {
                        fontSize: '1rem',
                    },
                }
            };

            var chart_grafik_rencana_realisasi_pendapatan_belanja_daerah = new ApexCharts(document.querySelector("#grafik_rencana_realisasi_pendapatan_belanja_daerah"), grafik_rencana_realisasi_pendapatan_belanja_daerah);
            chart_grafik_rencana_realisasi_pendapatan_belanja_daerah.render();
        });
    </script>
@endsection
