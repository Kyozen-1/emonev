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
    @php
        use Carbon\Carbon;
        use App\Models\TahunPeriode;
        use App\Models\Visi;
        use App\Models\PivotPerubahanVisi;
        use App\Models\Misi;
        use App\Models\PivotPerubahanMisi;
        use App\Models\Tujuan;
        use App\Models\PivotPerubahanTujuan;
        use App\Models\Sasaran;
        use App\Models\PivotPerubahanSasaran;
        use App\Models\PivotSasaranIndikator;
        use App\Models\TargetRpPertahunSasaran;
        use App\Models\ProgramRpjmd;
        use App\Models\PivotOpdProgramRpjmd;
        use App\Models\Urusan;
        use App\Models\PivotPerubahanUrusan;
        use App\Models\MasterOpd;
        use App\Models\PivotSasaranIndikatorProgramRpjmd;
        use App\Models\Program;
        use App\Models\PivotPerubahanProgram;
        use App\Models\PivotProgramKegiatanRenstra;
        use App\Models\Kegiatan;
        use App\Models\PivotPerubahanKegiatan;
    @endphp
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
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#programNav" role="tab" aria-selected="false" type="button" id="program_tab_button">
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
                                </div>
                                {{-- Tujuan End --}}

                                {{-- Sasaran Start --}}
                                <div class="tab-pane fade" id="sasaranNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="sasaranLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                </div>
                                {{-- Sasaran End --}}
                                <div class="tab-pane fade" id="programNav" role="tabpanel">
                                    <div class="d-flex align-items-center mb-5" id="programLoading">
                                        <strong>Loading...</strong>
                                        <div class="spinner-border ms-auto text-primary" role="status" aria-hidden="true"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 justify-content-center align-self-center">
                                            <label for="" class="form-label">Tambah Program</label>
                                        </div>
                                        <div class="col-6" style="text-align: right">
                                            <button class="btn btn-primary waves-effect waves-light btn-icon" id="program_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditProgramModal" title="Tambah Data Program"><i class="fas fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div id="programNavDiv"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="renstraTab" role="tabpanel">
                        <div class="border-0 pb-0">
                            <ul class="nav nav-pills responsive-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#renstra_tujuan_pd" role="tab" aria-selected="true" type="button">Tujuan PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#renstra_sasaran_pd" role="tab" aria-selected="false" type="button">Sasaran PD</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#renstra_program" role="tab" aria-selected="false" type="button">Program</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#renstra_kegiatan" role="tab" aria-selected="false" type="button">Kegiatan</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                {{-- Renstra Tujuan Start --}}
                                <div class="tab-pane fade active show" id="renstra_tujuan_pd" role="tabpanel">
                                    <div class="data-table-rows slim">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="70%">Misi</th>
                                                        <th width="15%">Tahun Perubahan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($misis as $misi)
                                                        <tr>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['kode']}}
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['deskripsi']}}
                                                                <br>
                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['tahun_perubahan']}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="hiddenRow">
                                                                <div class="accordion-body collapse" id="renstra_misi{{$misi['id']}}">
                                                                    <table class="table table-striped table-condesed">
                                                                        <tbody>
                                                                            @php
                                                                                $renstra_get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                $renstra_tujuans = [];
                                                                                foreach ($renstra_get_tujuans as $renstra_get_tujuan) {
                                                                                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $renstra_get_tujuan->id)
                                                                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                    if($cek_perubahan_tujuan)
                                                                                    {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                            'kode' => $cek_perubahan_tujuan->kode,
                                                                                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    } else {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $renstra_get_tujuan->id,
                                                                                            'kode' => $renstra_get_tujuan->kode,
                                                                                            'deskripsi' => $renstra_get_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $renstra_get_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            @foreach ($renstra_tujuans as $renstra_tujuan)
                                                                                <tr>
                                                                                    <td width="15%">
                                                                                        {{$renstra_tujuan['kode']}}
                                                                                    </td>
                                                                                    <td width="70%">
                                                                                        {{$renstra_tujuan['deskripsi']}}
                                                                                        <br>
                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                    </td>
                                                                                    <td width="15%">
                                                                                        {{$renstra_tujuan['tahun_perubahan']}}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Tujuan End --}}
                                <div class="tab-pane fade" id="renstra_sasaran_pd" role="tabpanel">
                                    <div class="data-table-rows slim">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="70%">Misi</th>
                                                        <th width="15%">Tahun Perubahan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($misis as $misi)
                                                        <tr>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['kode']}}
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['deskripsi']}}
                                                                <br>
                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['tahun_perubahan']}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="hiddenRow">
                                                                <div class="accordion-body collapse" id="renstra_sasaran_misi{{$misi['id']}}">
                                                                    <table class="table table-striped table-condesed">
                                                                        <tbody>
                                                                            @php
                                                                                $renstra_get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                $renstra_tujuans = [];
                                                                                foreach ($renstra_get_tujuans as $renstra_get_tujuan) {
                                                                                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $renstra_get_tujuan->id)
                                                                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                    if($cek_perubahan_tujuan)
                                                                                    {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                            'kode' => $cek_perubahan_tujuan->kode,
                                                                                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    } else {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $renstra_get_tujuan->id,
                                                                                            'kode' => $renstra_get_tujuan->kode,
                                                                                            'deskripsi' => $renstra_get_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $renstra_get_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            @foreach ($renstra_tujuans as $renstra_tujuan)
                                                                                <tr>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['kode']}}
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="70%">
                                                                                        {{$renstra_tujuan['deskripsi']}}
                                                                                        <br>
                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['tahun_perubahan']}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="3" class="hiddenRow">
                                                                                        <div class="accordion-body collapse" id="renstra_tujuan{{$renstra_tujuan['id']}}">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>
                                                                                                    @php
                                                                                                        $get_renstra_sasarans = Sasaran::where('tujuan_id', $renstra_tujuan['id'])->get();
                                                                                                        $renstra_sasarans = [];
                                                                                                        foreach ($get_renstra_sasarans as $get_renstra_sasaran) {
                                                                                                            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_renstra_sasaran->id)
                                                                                                                                        ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                        ->latest()->first();
                                                                                                            if($cek_perubahan_sasaran)
                                                                                                            {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                    'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            } else {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $get_renstra_sasaran->id,
                                                                                                                    'kode' => $get_renstra_sasaran->kode,
                                                                                                                    'deskripsi' => $get_renstra_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $get_renstra_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            }
                                                                                                        }
                                                                                                    @endphp
                                                                                                    @foreach ($renstra_sasarans as $sasaran)
                                                                                                        <tr>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">{{$sasaran['kode']}}</td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="70%">
                                                                                                                {{$sasaran['deskripsi']}}
                                                                                                                <br>
                                                                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                            </td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">
                                                                                                                {{$sasaran['tahun_perubahan']}}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td class="hiddenRow" colspan="3">
                                                                                                                <div class="accordion-body collapse" id="renstra_sasaran{{$sasaran['id']}}">
                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th width="60%">Sasaran Indikator</th>
                                                                                                                                <th width="20%">Target</th>
                                                                                                                                <th width="20%">Satuan</th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                                            @php
                                                                                                                                $get_renstra_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                            @endphp
                                                                                                                            @foreach ($get_renstra_sasaran_indikators as $sasaran_indikator)
                                                                                                                                <tr>
                                                                                                                                    <td>
                                                                                                                                        {{$sasaran_indikator->indikator}}
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                                                        <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                    </td>
                                                                                                                                    <td>{{$sasaran_indikator->target}}</td>
                                                                                                                                    <td>{{$sasaran_indikator->satuan}}</td>
                                                                                                                                </tr>
                                                                                                                            @endforeach
                                                                                                                        </tbody>
                                                                                                                    </table>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Sasaran Start --}}
                                <div class="tab-pane fade" id="renstra_program" role="tabpanel">
                                    <div class="data-table-rows slim">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="70%">Misi</th>
                                                        <th width="15%">Tahun Perubahan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($misis as $misi)
                                                        <tr>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['kode']}}
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['deskripsi']}}
                                                                <br>
                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['tahun_perubahan']}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="hiddenRow">
                                                                <div class="accordion-body collapse" id="renstra_program_misi{{$misi['id']}}">
                                                                    <table class="table table-striped table-condesed">
                                                                        <tbody>
                                                                            @php
                                                                                $renstra_get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                $renstra_tujuans = [];
                                                                                foreach ($renstra_get_tujuans as $renstra_get_tujuan) {
                                                                                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $renstra_get_tujuan->id)
                                                                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                    if($cek_perubahan_tujuan)
                                                                                    {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                            'kode' => $cek_perubahan_tujuan->kode,
                                                                                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    } else {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $renstra_get_tujuan->id,
                                                                                            'kode' => $renstra_get_tujuan->kode,
                                                                                            'deskripsi' => $renstra_get_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $renstra_get_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            @foreach ($renstra_tujuans as $renstra_tujuan)
                                                                                <tr>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['kode']}}
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="70%">
                                                                                        {{$renstra_tujuan['deskripsi']}}
                                                                                        <br>
                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['tahun_perubahan']}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="3" class="hiddenRow">
                                                                                        <div class="accordion-body collapse" id="renstra_program_tujuan{{$renstra_tujuan['id']}}">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>
                                                                                                    @php
                                                                                                        $get_renstra_sasarans = Sasaran::where('tujuan_id', $renstra_tujuan['id'])->get();
                                                                                                        $renstra_sasarans = [];
                                                                                                        foreach ($get_renstra_sasarans as $get_renstra_sasaran) {
                                                                                                            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_renstra_sasaran->id)
                                                                                                                                        ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                        ->latest()->first();
                                                                                                            if($cek_perubahan_sasaran)
                                                                                                            {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                    'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            } else {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $get_renstra_sasaran->id,
                                                                                                                    'kode' => $get_renstra_sasaran->kode,
                                                                                                                    'deskripsi' => $get_renstra_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $get_renstra_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            }
                                                                                                        }
                                                                                                    @endphp
                                                                                                    @foreach ($renstra_sasarans as $sasaran)
                                                                                                        <tr>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">{{$sasaran['kode']}}</td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="70%">
                                                                                                                {{$sasaran['deskripsi']}}
                                                                                                                <br>
                                                                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                            </td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">
                                                                                                                {{$sasaran['tahun_perubahan']}}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td class="hiddenRow" colspan="3">
                                                                                                                <div class="accordion-body collapse" id="renstra_program_sasaran{{$sasaran['id']}}">
                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th width="60%">Sasaran Indikator</th>
                                                                                                                                <th width="20%">Target</th>
                                                                                                                                <th width="20%">Satuan</th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                                            @php
                                                                                                                                $get_renstra_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                            @endphp
                                                                                                                            @foreach ($get_renstra_sasaran_indikators as $sasaran_indikator)
                                                                                                                                <tr>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->indikator}}
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                                                        <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->target}}
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_program_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->satuan}}
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td class="hiddenRow" colspan="3">
                                                                                                                                        <div class="accordion-body collapse" id="renstra_program_sasaran_indikator{{$sasaran_indikator['id']}}">
                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="50%">Program RPJMD</th>
                                                                                                                                                        <th width="15%">Status Program</th>
                                                                                                                                                        <th width="15%">Pagu</th>
                                                                                                                                                        <th width="20%">OPD</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>
                                                                                                                                                    @php
                                                                                                                                                        $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->get();
                                                                                                                                                                                    $programs = [];
                                                                                                                                                                                    foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                                                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                                                                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                                                                        {
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                    @endphp
                                                                                                                                                    @foreach ($programs as $program)
                                                                                                                                                        <tr>
                                                                                                                                                            <td>
                                                                                                                                                                {{$program['deskripsi']}}
                                                                                                                                                                <br>
                                                                                                                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                                                                <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                                                                <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                                                                                <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                                                <span class="badge bg-success text-uppercase">Program RPJMD</span>
                                                                                                                                                            </td>
                                                                                                                                                            <td>
                                                                                                                                                                {{$program['status_program']}}
                                                                                                                                                            </td>
                                                                                                                                                            <td>{{$program['pagu']}}</td>
                                                                                                                                                            <td>
                                                                                                                                                                @php
                                                                                                                                                                    $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                @endphp
                                                                                                                                                                <ul>
                                                                                                                                                                    @foreach ($get_opds as $get_opd)
                                                                                                                                                                        <li>{{$get_opd->opd->nama}}</li>
                                                                                                                                                                    @endforeach
                                                                                                                                                                </ul>
                                                                                                                                                            </td>
                                                                                                                                                        </tr>
                                                                                                                                                    @endforeach
                                                                                                                                                </tbody>
                                                                                                                                            </table>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            @endforeach
                                                                                                                        </tbody>
                                                                                                                    </table>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Sasaran End --}}
                                {{-- Renstra Kegiatan Start --}}
                                <div class="tab-pane fade" id="renstra_kegiatan" role="tabpanel">
                                    <div class="data-table-rows slim">
                                        <div class="data-table-responsive-wrapper">
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="70%">Misi</th>
                                                        <th width="15%">Tahun Perubahan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($misis as $misi)
                                                        <tr>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['kode']}}
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['deskripsi']}}
                                                                <br>
                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                            </td>
                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_misi{{$misi['id']}}" class="accordion-toggle">
                                                                {{$misi['tahun_perubahan']}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="hiddenRow">
                                                                <div class="accordion-body collapse" id="renstra_kegiatan_misi{{$misi['id']}}">
                                                                    <table class="table table-striped table-condesed">
                                                                        <tbody>
                                                                            @php
                                                                                $renstra_get_tujuans = Tujuan::where('misi_id', $misi['id'])->get();
                                                                                $renstra_tujuans = [];
                                                                                foreach ($renstra_get_tujuans as $renstra_get_tujuan) {
                                                                                    $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $renstra_get_tujuan->id)
                                                                                                            ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                    if($cek_perubahan_tujuan)
                                                                                    {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $cek_perubahan_tujuan->tujuan_id,
                                                                                            'kode' => $cek_perubahan_tujuan->kode,
                                                                                            'deskripsi' => $cek_perubahan_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $cek_perubahan_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    } else {
                                                                                        $renstra_tujuans[] = [
                                                                                            'id' => $renstra_get_tujuan->id,
                                                                                            'kode' => $renstra_get_tujuan->kode,
                                                                                            'deskripsi' => $renstra_get_tujuan->deskripsi,
                                                                                            'tahun_perubahan' => $renstra_get_tujuan->tahun_perubahan
                                                                                        ];
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                            @foreach ($renstra_tujuans as $renstra_tujuan)
                                                                                <tr>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['kode']}}
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="70%">
                                                                                        {{$renstra_tujuan['deskripsi']}}
                                                                                        <br>
                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                    </td>
                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_tujuan{{$renstra_tujuan['id']}}" class="accordion-toggle" width="15%">
                                                                                        {{$renstra_tujuan['tahun_perubahan']}}
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td colspan="3" class="hiddenRow">
                                                                                        <div class="accordion-body collapse" id="renstra_kegiatan_tujuan{{$renstra_tujuan['id']}}">
                                                                                            <table class="table table-striped table-condesed">
                                                                                                <tbody>
                                                                                                    @php
                                                                                                        $get_renstra_sasarans = Sasaran::where('tujuan_id', $renstra_tujuan['id'])->get();
                                                                                                        $renstra_sasarans = [];
                                                                                                        foreach ($get_renstra_sasarans as $get_renstra_sasaran) {
                                                                                                            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $get_renstra_sasaran->id)
                                                                                                                                        ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                        ->latest()->first();
                                                                                                            if($cek_perubahan_sasaran)
                                                                                                            {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $cek_perubahan_sasaran->sasaran_id,
                                                                                                                    'kode' => $cek_perubahan_sasaran->kode,
                                                                                                                    'deskripsi' => $cek_perubahan_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $cek_perubahan_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            } else {
                                                                                                                $renstra_sasarans[] = [
                                                                                                                    'id' => $get_renstra_sasaran->id,
                                                                                                                    'kode' => $get_renstra_sasaran->kode,
                                                                                                                    'deskripsi' => $get_renstra_sasaran->deskripsi,
                                                                                                                    'tahun_perubahan' => $get_renstra_sasaran->tahun_perubahan
                                                                                                                ];
                                                                                                            }
                                                                                                        }
                                                                                                    @endphp
                                                                                                    @foreach ($renstra_sasarans as $sasaran)
                                                                                                        <tr>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">{{$sasaran['kode']}}</td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="70%">
                                                                                                                {{$sasaran['deskripsi']}}
                                                                                                                <br>
                                                                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                            </td>
                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran{{$sasaran['id']}}" class="accordion-toggle" width="15%">
                                                                                                                {{$sasaran['tahun_perubahan']}}
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <tr>
                                                                                                            <td class="hiddenRow" colspan="3">
                                                                                                                <div class="accordion-body collapse" id="renstra_kegiatan_sasaran{{$sasaran['id']}}">
                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th width="60%">Sasaran Indikator</th>
                                                                                                                                <th width="20%">Target</th>
                                                                                                                                <th width="20%">Satuan</th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                        <tbody>
                                                                                                                            @php
                                                                                                                                $get_renstra_sasaran_indikators = PivotSasaranIndikator::where('sasaran_id', $sasaran['id'])->get();
                                                                                                                            @endphp
                                                                                                                            @foreach ($get_renstra_sasaran_indikators as $sasaran_indikator)
                                                                                                                                <tr>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->indikator}}
                                                                                                                                        <br>
                                                                                                                                        <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                                        <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                                        <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                                                        <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->target}}
                                                                                                                                    </td>
                                                                                                                                    <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_sasaran_indikator{{$sasaran_indikator['id']}}" class="accordion-toggle">
                                                                                                                                        {{$sasaran_indikator->satuan}}
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                                <tr>
                                                                                                                                    <td class="hiddenRow" colspan="3">
                                                                                                                                        <div class="accordion-body collapse" id="renstra_kegiatan_sasaran_indikator{{$sasaran_indikator['id']}}">
                                                                                                                                            <table class="table table-striped table-condesed">
                                                                                                                                                <thead>
                                                                                                                                                    <tr>
                                                                                                                                                        <th width="45%">Program RPJMD</th>
                                                                                                                                                        <th width="15%">Status Program</th>
                                                                                                                                                        <th width="15%">Pagu</th>
                                                                                                                                                        <th width="15%">OPD</th>
                                                                                                                                                        <th width="10%">Aksi</th>
                                                                                                                                                    </tr>
                                                                                                                                                </thead>
                                                                                                                                                <tbody>
                                                                                                                                                    @php
                                                                                                                                                        $get_program_rpjmds = ProgramRpjmd::whereHas('pivot_sasaran_indikator_program_rpjmd', function($q) use ($sasaran_indikator){
                                                                                                                                                                                        $q->where('sasaran_indikator_id', $sasaran_indikator['id']);
                                                                                                                                                                                    })->get();
                                                                                                                                                                                    $programs = [];
                                                                                                                                                                                    foreach ($get_program_rpjmds as $get_program_rpjmd) {
                                                                                                                                                                                        $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $get_program_rpjmd->program_id)
                                                                                                                                                                                                                    ->orderBy('tahun_perubahan', 'desc')->latest()->first();
                                                                                                                                                                                        if($cek_perubahan_program)
                                                                                                                                                                                        {
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $cek_perubahan_program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                                'program_id' => $cek_perubahan_program->program_id
                                                                                                                                                                                            ];
                                                                                                                                                                                        } else {
                                                                                                                                                                                            $program = Program::find($get_program_rpjmd->program_id);
                                                                                                                                                                                            $programs[] = [
                                                                                                                                                                                                'id' => $get_program_rpjmd->id,
                                                                                                                                                                                                'deskripsi' => $program->deskripsi,
                                                                                                                                                                                                'status_program' => $get_program_rpjmd->status_program,
                                                                                                                                                                                                'pagu' => $get_program_rpjmd->pagu,
                                                                                                                                                                                                'program_id' => $program->id
                                                                                                                                                                                            ];
                                                                                                                                                                                        }
                                                                                                                                                                                    }
                                                                                                                                                    @endphp
                                                                                                                                                    @foreach ($programs as $program)
                                                                                                                                                        <tr>
                                                                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd{{$program['id']}}" class="accordion-toggle">
                                                                                                                                                                {{$program['deskripsi']}}
                                                                                                                                                                <br>
                                                                                                                                                                <span class="badge bg-warning text-uppercase">{{$misi['kode']}} Misi</span>
                                                                                                                                                                <span class="badge bg-secondary text-uppercase">{{$renstra_tujuan['kode']}} Tujuan</span>
                                                                                                                                                                <span class="badge bg-danger text-uppercase">{{$sasaran['kode']}} Sasaran</span>
                                                                                                                                                                <span class="badge bg-info text-uppercase">Sasaran Indikator</span>
                                                                                                                                                                <span class="badge bg-success text-uppercase">Program RPJMD</span>
                                                                                                                                                            </td>
                                                                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd{{$program['id']}}" class="accordion-toggle">
                                                                                                                                                                {{$program['status_program']}}
                                                                                                                                                            </td>
                                                                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd{{$program['id']}}" class="accordion-toggle">
                                                                                                                                                                {{$program['pagu']}}
                                                                                                                                                            </td>
                                                                                                                                                            <td data-bs-toggle="collapse" data-bs-target="#renstra_kegiatan_program_rpjmd{{$program['id']}}" class="accordion-toggle">
                                                                                                                                                                @php
                                                                                                                                                                    $get_opds = PivotOpdProgramRpjmd::where('program_rpjmd_id', $program['id'])->get();
                                                                                                                                                                @endphp
                                                                                                                                                                <ul>
                                                                                                                                                                    @foreach ($get_opds as $get_opd)
                                                                                                                                                                        <li>{{$get_opd->opd->nama}}</li>
                                                                                                                                                                    @endforeach
                                                                                                                                                                </ul>
                                                                                                                                                            </td>
                                                                                                                                                            <td>
                                                                                                                                                                <button class="btn btn-primary waves-effect waves-light mr-2 renstra_kegiatan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditRenstraKegiatanModal" title="Tambah Kegiatan" data-program-id="{{$program['program_id']}}" data-program-rpjmd-id="{{$program['id']}}"><i class="fas fa-plus"></i></button>
                                                                                                                                                            </td>
                                                                                                                                                        </tr>
                                                                                                                                                        <tr>
                                                                                                                                                            <td class="hiddenRow" colspan="5">
                                                                                                                                                                <div class="accordion-body collapse" id="renstra_kegiatan_program_rpjmd{{$program['id']}}">
                                                                                                                                                                    <table class="table table-striped table-condesed">
                                                                                                                                                                        <thead>
                                                                                                                                                                            <tr>
                                                                                                                                                                                <th width="85%">Kegiatan</th>
                                                                                                                                                                                <th width="15%">Tahun Perubahan</th>
                                                                                                                                                                            </tr>
                                                                                                                                                                        </thead>
                                                                                                                                                                        <tbody>
                                                                                                                                                                            @php
                                                                                                                                                                                $pivot_kegiatan_renstras = PivotProgramKegiatanRenstra::where('program_rpjmd_id', $program['id'])
                                                                                                                                                                                                            ->where('program_id', $program['program_id'])
                                                                                                                                                                                                            ->get();
                                                                                                                                                                                $kegiatans = [];
                                                                                                                                                                                foreach ($pivot_kegiatan_renstras as $kegiatan_renstra) {
                                                                                                                                                                                    $cek_perubahan_kegiatan = PivotPerubahanKegiatan::where('kegiatan_id', $kegiatan_renstra->kegiatan_id)
                                                                                                                                                                                                                ->orderBy('tahun_perubahan', 'desc')
                                                                                                                                                                                                                ->latest()->first();
                                                                                                                                                                                    if($cek_perubahan_kegiatan)
                                                                                                                                                                                    {
                                                                                                                                                                                        $kegiatans[] = [
                                                                                                                                                                                            'id' => $cek_perubahan_kegiatan->kegiatan_id,
                                                                                                                                                                                            'kode' => $cek_perubahan_kegiatan->kode,
                                                                                                                                                                                            'deskripsi' => $cek_perubahan_kegiatan->deskripsi,
                                                                                                                                                                                            'tahun_perubahan' => $cek_perubahan_kegiatan->tahun_perubahan
                                                                                                                                                                                        ];
                                                                                                                                                                                    } else {
                                                                                                                                                                                        $kegiatan = Kegiatan::find($kegiatan_renstra->kegiatan_id);
                                                                                                                                                                                        $kegiatans[] = [
                                                                                                                                                                                            'id' => $kegiatan->id,
                                                                                                                                                                                            'kode' => $kegiatan->kode,
                                                                                                                                                                                            'deskripsi' => $kegiatan->deskripsi,
                                                                                                                                                                                            'tahun_perubahan' => $kegiatan->tahun_perubahan
                                                                                                                                                                                        ];
                                                                                                                                                                                    }
                                                                                                                                                                                }
                                                                                                                                                                            @endphp
                                                                                                                                                                            @foreach ($kegiatans as $kegiatan)
                                                                                                                                                                            <tr>
                                                                                                                                                                                <td>{{$kegiatan['deskripsi']}}</td>
                                                                                                                                                                                <td>{{$kegiatan['tahun_perubahan']}}</td>
                                                                                                                                                                            </tr>
                                                                                                                                                                            @endforeach
                                                                                                                                                                        </tbody>
                                                                                                                                                                    </table>
                                                                                                                                                                </div>
                                                                                                                                                            </td>
                                                                                                                                                        </tr>
                                                                                                                                                    @endforeach
                                                                                                                                                </tbody>
                                                                                                                                            </table>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>
                                                                                                                            @endforeach
                                                                                                                        </tbody>
                                                                                                                    </table>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    @endforeach
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- Renstra Kegiatan End --}}
                            </div>
                        </div>
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

    {{-- Tujuan Start --}}
    <div class="modal fade" id="addEditTujuanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="tujuan_form_result"></span>
                    <form id="tujuan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tujuan_misi_id" id="tujuan_misi_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="tujuan_kode" id="tujuan_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="tujuan_deskripsi" id="tujuan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tujuan_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="tujuan_tahun_perubahan" id="tujuan_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="tujuan_aksi" id="tujuan_aksi" value="Save">
                    <input type="hidden" name="tujuan_hidden_id" id="tujuan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="tujuan_aksi_button" id="tujuan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div id="importTujuanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importTujuanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.tujuan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tujuan_impor_misi_id" id="tujuan_impor_misi_id">
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_tujuan" name="impor_tujuan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailTujuanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Detail Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Visi</label>
                                <textarea id="tujuan_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Misi</label>
                                <textarea id="tujuan_detail_misi" class="form-control" rows="5" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input id="tujuan_detail_kode" type="text" class="form-control" disabled/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea id="tujuan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Perubahan</label>
                                <input id="tujuan_detail_tahun_perubahan" type="text" class="form-control" disabled/>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Perubahan Tujuan</label>
                                <div id="div_pivot_perubahan_tujuan" class="scrollBarPagination"></div>
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
    {{-- Tujuan End --}}

    {{-- Sasaran Start --}}
    <div class="modal fade" id="addEditSasaranModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="sasaran_form_result"></span>
                    <form id="sasaran_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_tujuan_id" id="sasaran_tujuan_id">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input name="sasaran_kode" id="sasaran_kode" type="number" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="sasaran_deskripsi" id="sasaran_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="sasaran_tahun_perubahan" class="form-label">Tahun Perubahan</label>
                                <select name="sasaran_tahun_perubahan" id="sasaran_tahun_perubahan" class="form-control" required>
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
                    <input type="hidden" name="sasaran_aksi" id="sasaran_aksi" value="Save">
                    <input type="hidden" name="sasaran_hidden_id" id="sasaran_hidden_id">
                    <button type="submit" class="btn btn-primary" name="sasaran_aksi_button" id="sasaran_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal modal-right large scroll-out-negative fade" id="detailSasaranModal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <textarea id="sasaran_detail_visi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_misi_kode"></span></div>
                                <textarea id="sasaran_detail_misi" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_tujuan_kode"></span></div>
                                <textarea id="sasaran_detail_tujuan" class="form-control" rows="5" disabled></textarea>
                            </div>
                        </div>

                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <div class="input-group">
                                <div class="input-group-text"><span id="sasaran_detail_kode"></span></div>
                                <textarea id="sasaran_detail_deskripsi" class="form-control" rows="5" disabled></textarea>
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
                                    <tbody id="tbody_detail_sasaran_indikator">

                                    </tbody>
                                </table>
                            </div>
                            <!-- Table End -->
                        </div>
                        <hr>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Perubahan Sasaran</label>
                            <div id="div_pivot_perubahan_sasaran" class="scrollBarPagination"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="importSasaranModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importSasaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Import Data</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.sasaran.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_impor_tujuan_id" id="sasaran_impor_tujuan_id">
                        <div class="mb-3 position-relative form-group">
                            <input type="file" class="dropify" id="impor_sasaran" name="impor_sasaran" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                        </div>
                        <div class="mb-3 position-relative form-group">
                            <button class="btn btn-success waves-effect waves-light">Impor</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Sasaran End --}}

    {{-- Sasaran Indikator Start --}}
    <div class="modal fade" id="addEditSasaranIndikatorModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="sasaran_indikator_form_result"></span>
                    <form id="sasaran_indikator_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_indikator_sasaran_id" id="sasaran_indikator_sasaran_id">
                        <div class="mb-3">
                            <label class="form-label">Indikator</label>
                            <textarea name="sasaran_indikator_indikator" id="sasaran_indikator_indikator" rows="5" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Target</label>
                            <input type="number" name="sasaran_indikator_target" id="sasaran_indikator_target" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Satuan</label>
                            <input type="text" class="form-control" name="sasaran_indikator_satuan" id="sasaran_indikator_satuan" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="sasaran_indikator_aksi" id="sasaran_indikator_aksi" value="Save">
                    <input type="hidden" name="sasaran_indikator_hidden_id" id="sasaran_indikator_hidden_id">
                    <button type="submit" class="btn btn-primary" name="sasaran_indikator_aksi_button" id="sasaran_indikator_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal modal-right large scroll-out-negative fade" id="detailSasaranIndikatorModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable full">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group position-relative mb-3">
                                <label for="" class="form-label">Indikator</label>
                                <textarea id="sasaran_indikator_detail_indikator" rows="5" class="form-control" disabled></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row">
                                <div class="col-12">
                                    <label for="" class="form-label">Target</label>
                                    <input type="text" class="form-label" id="sasaran_indikator_detail_target" required>
                                </div>
                                <div class="col-12">
                                    <label for="" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="sasaran_indikator_detail_satuan">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="data-table-rows slim">
                        <!-- Table Start -->
                        <div class="data-table-responsive-wrapper">
                            <table id="visi_table" class="data-table w-100">
                                <thead>
                                    <tr>
                                        <th class="text-muted text-small text-uppercase" width="10%">No</th>
                                        <th class="text-muted text-small text-uppercase" width="55%">Target</th>
                                        <th class="text-muted text-small text-uppercase" width="15%">Satuan</th>
                                        <th class="text-muted text-small text-uppercase" width="20%">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- Table End -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- Sassaran Indikator End --}}

    {{-- Program RPJMD Start --}}
    <div class="modal fade" id="addEditProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="program_form_result"></span>
                    <form id="program_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <h2 class="small-title">Atur Program</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <select name="program_urusan_id" id="program_urusan_id" class="form-control" required>
                                <option value="">--- Pilih Urusan ---</option>
                                @foreach ($urusans as $urusan)
                                    <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Program</label>
                            <select name="program_program_id" id="program_program_id" class="form-control" disabled required>
                                <option value="">--- Pilih Program ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Status Program</label>
                            <select name="program_status_program" id="program_status_program" class="form-control" required>
                                <option value="">--- Pilih Status Program ---</option>
                                <option value="Program Prioritas">Program Prioritas</option>
                                <option value="Program Pendukung">Program Pendukung</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pagu</label>
                            <input type="number" name="program_pagu" id="program_pagu" class="form-control" required>
                        </div>
                        <h2 class="small-title">Atur Sasaran Indikator</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <select name="program_misi_id" id="program_misi_id" class="form-control" required>
                                <option value="">--- Pilih Misi ---</option>
                                @foreach ($misis as $misi)
                                    <option value="{{$misi['id']}}">{{$misi['kode']}}. {{$misi['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <select name="program_tujuan_id" id="program_tujuan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Tujuan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <select name="program_sasaran_id" id="program_sasaran_id" class="form-control" disabled required>
                                <option value="">--- Pilih Sasaran ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran Indikator</label>
                            <select name="program_sasaran_indikator_id[]" id="program_sasaran_indikator_id" class="form-control" multiple="multiple" disabled required>
                            </select>
                        </div>
                        <h2 class="small-title">Atur OPD Terkai</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pilih OPD</label>
                            <select name="program_opd_id[]" id="program_opd_id" class="form-control" multiple="multiple" required>
                                @foreach ($opds as $id => $nama)
                                    <option value="{{$id}}">{{$nama}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="program_aksi" id="program_aksi" value="Save">
                    <input type="hidden" name="program_hidden_id" id="program_hidden_id">
                    <button type="submit" class="btn btn-primary" name="program_aksi_button" id="program_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="editProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="program_edit_form_result"></span>
                    <form id="program_edit_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <h2 class="small-title">Atur Program</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <select name="program_edit_urusan_id" id="program_edit_urusan_id" class="form-control" required>
                                <option value="">--- Pilih Urusan ---</option>
                                @foreach ($urusans as $urusan)
                                    <option value="{{$urusan['id']}}">{{$urusan['kode']}}. {{$urusan['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Program</label>
                            <select name="program_edit_program_id" id="program_edit_program_id" class="form-control" disabled required>
                                <option value="">--- Pilih Program ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Status Program</label>
                            <select name="program_edit_status_program" id="program_edit_status_program" class="form-control" required>
                                <option value="">--- Pilih Status Program ---</option>
                                <option value="Program Prioritas">Program Prioritas</option>
                                <option value="Program Pendukung">Program Pendukung</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pagu</label>
                            <input type="number" name="program_edit_pagu" id="program_edit_pagu" class="form-control" required>
                        </div>
                        <h2 class="small-title">Atur Sasaran Indikator</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Misi</label>
                            <select name="program_edit_misi_id" id="program_edit_misi_id" class="form-control" required>
                                <option value="">--- Pilih Misi ---</option>
                                @foreach ($misis as $misi)
                                    <option value="{{$misi['id']}}">{{$misi['kode']}}. {{$misi['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <select name="program_edit_tujuan_id" id="program_edit_tujuan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Tujuan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <select name="program_edit_sasaran_id" id="program_edit_sasaran_id" class="form-control" disabled required>
                                <option value="">--- Pilih Sasaran ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Sasaran Indikator</label>
                            <select name="program_edit_sasaran_indikator_id" id="program_edit_sasaran_indikator_id" class="form-control" disabled required>
                            </select>
                        </div>
                        <h2 class="small-title">Atur OPD Terkait</h2>
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Pilih OPD</label>
                            <select name="program_edit_opd_id" id="program_edit_opd_id" class="form-control" required>
                                @foreach ($opds as $id => $nama)
                                    <option value="{{$id}}">{{$nama}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="program_edit_aksi" id="program_edit_aksi" value="Save">
                    <input type="hidden" name="program_edit_hidden_id" id="program_edit_hidden_id">
                    <button type="submit" class="btn btn-primary" name="program_edit_aksi_button" id="program_edit_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div> --}}
    {{-- Program RPJMD End --}}

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
                        <input type="hidden" name="renstra_kegiatan_program_id" id="renstra_kegiatan_program_id">
                        <input type="hidden" name="renstra_kegiatan_program_rpjmd_id" id="renstra_kegiatan_program_rpjmd_id">
                        <div class="form-group position-relative mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <select name="renstra_kegiatan_kegiatan_id" id="renstra_kegiatan_kegiatan_id" class="form-control" required>
                                <option value="">--- Pilih Kegiatan ---</option>
                            </select>
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
        // Visi Start
        $(document).ready(function(){
            $('#program_urusan_id').select2();
            $('#program_program_id').select2();
            $('#program_misi_id').select2();
            $('#program_tujuan_id').select2();
            $('#program_sasaran_id').select2();
            $('#program_sasaran_indikator_id').select2();
            $('#program_opd_id').select2();
            $('#renstra_kegiatan_kegiatan_id').select2();
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
        $('#tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-tujuan') }}",
                dataType: "json",
                beforeSend: function()
                {
                    $('#tujuanLoading').show();
                },
                success: function(data)
                {
                    $('#tujuanLoading').hide();
                    $('#tujuanNav').html(data.html);
                }
            });
        });

        $(document).on('click','.tujuan_create',function(){
            $('#tujuan_misi_id').val($(this).attr('data-misi-id'));
            $('#tujuan_form')[0].reset();
            $('#tujuan_aksi_button').text('Save');
            $('#tujuan_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Tujuan');
            $('#tujuan_aksi_button').val('Save');
            $('#tujuan_aksi').val('Save');
            $('#tujuan_form_result').html('');
        });

        $('#tujuan_form').on('submit', function(e){
            e.preventDefault();
            if($('#tujuan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#tujuan_aksi_button').text('Menyimpan...');
                        $('#tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditTujuanModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Tujuan',
                                showConfirmButton: true
                            });
                            $('#tujuanNav').html(data.success);
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }

            if($('#tujuan_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.tujuan.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#tujuan_aksi_button').text('Menyimpan...');
                        $('#tujuan_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#tujuan_aksi_button').prop('disabled', false);
                            $('#tujuan_form')[0].reset();
                            $('#tujuan_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditTujuanModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Tujuan',
                                showConfirmButton: true
                            });
                            $('#tujuanNav').html(data.success);
                        }

                        $('#tujuan_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click','.tujuan_btn_impor_template',function(){
            $('#tujuan_impor_misi_id').val($(this).attr('data-misi-id'));
            $('#importTujuanModal').modal('show');
        });

        $(document).on('click', '.detail-tujuan', function(){
            var id = $(this).attr('data-tujuan-id');
            $.ajax({
                url: "{{ url('/admin/tujuan/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_tujuan').remove();
                    $('#div_pivot_perubahan_tujuan').append('<div id="pivot_perubahan_tujuan"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#tujuan_detail_visi').val(data.result.visi);
                    $('#tujuan_detail_misi').val(data.result.misi);
                    $('#tujuan_detail_kode').val(data.result.kode);
                    $('#tujuan_detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_tujuan').append(data.result.pivot_perubahan_tujuan);
                    $('#tujuan_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#detailTujuanModal').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-tujuan', function(){
            var id = $(this).attr('data-tujuan-id');
            $('#tujuan_misi_id').val($(this).attr('data-misi-id'));
            $('#tujuan_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/tujuan/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#tujuan_kode').val(data.result.kode);
                    $('#tujuan_deskripsi').val(data.result.deskripsi);
                    $("[name='tujuan_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                    $('#tujuan_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#tujuan_aksi_button').text('Edit');
                    $('#tujuan_aksi_button').prop('disabled', false);
                    $('#tujuan_aksi_button').val('Edit');
                    $('#tujuan_aksi').val('Edit');
                    $('#addEditTujuanModal').modal('show');
                }
            });
        });
        // Tujuan End

        // Sasaran Start
        $('#sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-sasaran') }}",
                dataType: "json",
                beforeSend: function()
                {
                    $('#sasaranLoading').show();
                },
                success: function(data)
                {
                    $('#sasaranLoading').hide();
                    $('#sasaranNav').html(data.html);
                }
            });
        });

        $(document).on('click','.sasaran_create',function(){
            $('#sasaran_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#sasaran_form')[0].reset();
            $('#sasaran_aksi_button').text('Save');
            $('#sasaran_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Sasaran');
            $('#sasaran_aksi_button').val('Save');
            $('#sasaran_aksi').val('Save');
            $('#sasaran_form_result').html('');
        });

        $('#sasaran_form').on('submit', function(e){
            e.preventDefault();
            if($('#sasaran_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_aksi_button').text('Menyimpan...');
                        $('#sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_aksi_button').prop('disabled', false);
                            $('#sasaran_form')[0].reset();
                            $('#sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditSasaranModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Sasaran',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
                        }

                        $('#sasaran_form_result').html(html);
                    }
                });
            }

            if($('#sasaran_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_aksi_button').text('Menyimpan...');
                        $('#sasaran_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_aksi_button').prop('disabled', false);
                            $('#sasaran_form')[0].reset();
                            $('#sasaran_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditSasaranModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Sasaran',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
                        }

                        $('#sasaran_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.detail-sasaran', function(){
            var id = $(this).attr('data-sasaran-id');
            $.ajax({
                url: "{{ url('/admin/sasaran/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_sasaran').remove();
                    $('#div_pivot_perubahan_sasaran').append('<div id="pivot_perubahan_sasaran"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#sasaran_detail_visi').val(data.result.visi);
                    $('#sasaran_detail_misi').val(data.result.misi);
                    $('#sasaran_detail_tujuan').val(data.result.tujuan);
                    $('#sasaran_detail_kode').text(data.result.kode);
                    $('#sasaran_detail_misi_kode').text(data.result.kode_misi);
                    $('#sasaran_detail_tujuan_kode').text(data.result.kode_tujuan);
                    $('#sasaran_detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_sasaran').append(data.result.pivot_perubahan_sasaran);
                    $('#sasaran_detail_tahun_perubahan').val(data.result.tahun_perubahan);
                    $('#tbody_detail_sasaran_indikator').html(data.result.sasaran_indikator);
                    $('#detailSasaranModal').modal('show');
                }
            });
        });

        $(document).on('click', '.edit-sasaran', function(){
            var id = $(this).attr('data-sasaran-id');
            $('#sasaran_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#sasaran_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/sasaran/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#sasaran_kode').val(data.result.kode);
                    $('#sasaran_deskripsi').val(data.result.deskripsi);
                    $("[name='sasaran_tahun_perubahan']").val(data.result.tahun_perubahan).trigger('change');
                    $('#sasaran_hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#sasaran_aksi_button').text('Edit');
                    $('#sasaran_aksi_button').prop('disabled', false);
                    $('#sasaran_aksi_button').val('Edit');
                    $('#sasaran_aksi').val('Edit');
                    $('#addEditSasaranModal').modal('show');
                }
            });
        });

        $(document).on('click','.sasaran_btn_impor_template',function(){
            $('#sasaran_impor_tujuan_id').val($(this).attr('data-tujuan-id'));
            $('#importSasaranModal').modal('show');
        });
        // Sasaran End

        // Sasaran Indikator Start
        $(document).on('click','.sasaran_indikator_create',function(){
            $('#sasaran_indikator_sasaran_id').val($(this).attr('data-sasaran-id'));
            $('#sasaran_indikator_form')[0].reset();
            $('#sasaran_indikator_aksi_button').text('Save');
            $('#sasaran_indikator_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Sasaran Indikator');
            $('#sasaran_indikator_aksi_button').val('Save');
            $('#sasaran_indikator_aksi').val('Save');
            $('#sasaran_indikator_form_result').html('');
        });

        $('#sasaran_indikator_form').on('submit', function(e){
            e.preventDefault();
            if($('#sasaran_indikator_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.indikator.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_indikator_aksi_button').text('Menyimpan...');
                        $('#sasaran_indikator_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_indikator_aksi_button').prop('disabled', false);
                            $('#sasaran_indikator_form')[0].reset();
                            $('#sasaran_indikator_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditSasaranIndikatorModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Sasaran Indikator',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
                        }

                        $('#sasaran_indikator_form_result').html(html);
                    }
                });
            }

            if($('#sasaran_indikator_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.sasaran.indikator.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#sasaran_indikator_aksi_button').text('Menyimpan...');
                        $('#sasaran_indikator_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#sasaran_indikator_aksi_button').prop('disabled', false);
                            $('#sasaran_indikator_form')[0].reset();
                            $('#sasaran_indikator_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditSasaranIndikatorModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Sasaran Indikator',
                                showConfirmButton: true
                            });
                            $('#sasaranNav').html(data.success);
                        }

                        $('#sasaran_indikator_form_result').html(html);
                    }
                });
            }
        });

        $(document).on('click', '.edit-sasaran-indikator', function(){
            var id = $(this).attr('data-sasaran-indikator-id');
            $('#sasaran_indikator_sasaran_id').val($(this).attr('data-sasaran-id'));
            $('#sasaran_indikator_form_result').html('');
            $.ajax({
                url: "{{ url('/admin/sasaran/indikator/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#sasaran_indikator_indikator').val(data.result.indikator);
                    $('#sasaran_indikator_target').val(data.result.target);
                    $('#sasaran_indikator_satuan').val(data.result.satuan);
                    $('#sasaran_indikator_hidden_id').val(id);
                    $('.modal-title').text('Edit Data Sasaran Indikator');
                    $('#sasaran_indikator_aksi_button').text('Edit');
                    $('#sasaran_indikator_aksi_button').prop('disabled', false);
                    $('#sasaran_indikator_aksi_button').val('Edit');
                    $('#sasaran_indikator_aksi').val('Edit');
                    $('#addEditSasaranIndikatorModal').modal('show');
                }
            });
        });
        // Sasaran Indikator End

        // Program Start
        var program_edit_program_id = 0;
        var program_edit_tujuan_id = 0;
        var program_edit_sasaran_id = 0;
        var program_edit_sasaran_indikator_id = 0;

        $('#program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('admin.perencanaan.get-program') }}",
                dataType: "json",
                beforeSend: function()
                {
                    $('#programLoading').show();
                },
                success: function(data)
                {
                    $('#programLoading').remove();
                    $('#programNavDiv').html(data.html);
                }
            });
        });

        $('#program_urusan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_program_id').empty();
                        $('#program_program_id').prop('disabled', false);
                        $('#program_program_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#program_program_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_program_id').prop('disabled', true);
            }
        });

        $('#program_misi_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_tujuan_id').empty();
                        $('#program_tujuan_id').prop('disabled', false);
                        $('#program_sasaran_id').prop('disabled', true);
                        $('#program_sasaran_indikator_id').prop('disabled', true);
                        $('#program_tujuan_id').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#program_tujuan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_tujuan_id').prop('disabled', true);
                $('#program_sasaran_id').prop('disabled', true);
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('#program_tujuan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_sasaran_id').empty();
                        $('#program_sasaran_id').prop('disabled', false);
                        $('#program_sasaran_indikator_id').prop('disabled', true);
                        $('#program_sasaran_id').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#program_sasaran_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#program_sasaran_id').prop('disabled', true);
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('#program_sasaran_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.get-sasaran-indikator') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_sasaran_indikator_id').empty();
                        $('#program_sasaran_indikator_id').prop('disabled', false);
                        $('#program_sasaran_indikator_id').append('<option value="">--- Pilih Sasaran Indikator ---</option>');
                        $.each(response, function(key, value){
                            $('#program_sasaran_indikator_id').append(new Option(value.indikator, value.id));
                        });
                    }
                });
            } else {
                $('#program_sasaran_indikator_id').prop('disabled', true);
            }
        });

        $('#program_create').click(function(){
            program_edit_program_id = 0;
            program_edit_tujuan_id = 0;
            program_edit_sasaran_id = 0;
            program_edit_sasaran_indikator_id = 0;
            $('#program_form')[0].reset();
            $('#program_aksi_button').text('Save');
            $('#program_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Program');
            $('#program_aksi_button').val('Save');
            $('#program_aksi').val('Save');
            $('#program_form_result').html('');
            $("[name='program_urusan_id']").val('').trigger('change');
            $("[name='program_program_id']").val('').trigger('change');
            $("[name='program_misi_id']").val('').trigger('change');
            $("[name='program_tujuan_id']").val('').trigger('change');
            $("[name='program_sasaran_id']").val('').trigger('change');
            $("[name='program_sasaran_indikator_id[]']").val('').trigger('change');
            $("[name='program_opd_id[]']").val('').trigger('change');
        });

        $('#program_form').on('submit', function(e){
            e.preventDefault();
            if($('#program_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#program_aksi_button').text('Menyimpan...');
                        $('#program_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#program_aksi_button').prop('disabled', false);
                            $('#program_form')[0].reset();
                            $('#program_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditProgramModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Menambahkan Program RPJMD',
                                showConfirmButton: true
                            });
                            $('#programNavDiv').html(data.success);
                        }

                        $('#program_form_result').html(html);
                    }
                });
            }

            if($('#program_aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.program-rpjmd.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#program_aksi_button').text('Menyimpan...');
                        $('#program_aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            $('#program_aksi_button').prop('disabled', false);
                            $('#program_form')[0].reset();
                            $('#program_aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            $('#addEditProgramModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Merubah Program RPJMD',
                                showConfirmButton: true
                            });
                            $('#programNavDiv').html(data.success);
                        }

                        $('#program_form_result').html(html);
                    }
                });
            }
        });

        // $('#program_edit_urusan_id').on('change', function(){
        //     if($(this).val() != '')
        //     {
        //         $.ajax({
        //             url: "{{ route('admin.program-rpjmd.get-program') }}",
        //             method: 'POST',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //                 id:$(this).val()
        //             },
        //             success: function(response){
        //                 $('#program_edit_program_id').empty();
        //                 $('#program_edit_program_id').prop('disabled', false);
        //                 $('#program_edit_program_id').append('<option value="">--- Pilih Program ---</option>');
        //                 $.each(response, function(key, value){
        //                     $('#program_edit_program_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
        //                 });
        //             }
        //         });
        //     } else {
        //         $('#program_edit_program_id').prop('disabled', true);
        //     }
        // });

        // $('#program_edit_misi_id').on('change', function(){
        //     if($(this).val() != '')
        //     {
        //         $.ajax({
        //             url: "{{ route('admin.program-rpjmd.get-tujuan') }}",
        //             method: 'POST',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //                 id:$(this).val()
        //             },
        //             success: function(response){
        //                 $('#program_edit_tujuan_id').empty();
        //                 $('#program_edit_tujuan_id').prop('disabled', false);
        //                 $('#program_edit_sasaran_id').prop('disabled', true);
        //                 $('#program_edit_sasaran_indikator_id').prop('disabled', true);
        //                 $('#program_edit_tujuan_id').append('<option value="">--- Pilih Tujuan ---</option>');
        //                 $.each(response, function(key, value){
        //                     $('#program_edit_tujuan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
        //                 });
        //             }
        //         });
        //     } else {
        //         $('#program_edit_tujuan_id').prop('disabled', true);
        //         $('#program_edit_sasaran_id').prop('disabled', true);
        //         $('#program_edit_sasaran_indikator_id').prop('disabled', true);
        //     }
        // });

        // $('#program_edit_tujuan_id').on('change', function(){
        //     if($(this).val() != '')
        //     {
        //         $.ajax({
        //             url: "{{ route('admin.program-rpjmd.get-sasaran') }}",
        //             method: 'POST',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //                 id:$(this).val()
        //             },
        //             success: function(response){
        //                 $('#program_edit_sasaran_id').empty();
        //                 $('#program_edit_sasaran_id').prop('disabled', false);
        //                 $('#program_edit_sasaran_indikator_id').prop('disabled', true);
        //                 $('#program_edit_sasaran_id').append('<option value="">--- Pilih Sasaran ---</option>');
        //                 $.each(response, function(key, value){
        //                     $('#program_edit_sasaran_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
        //                 });
        //             }
        //         });
        //     } else {
        //         $('#program_edit_sasaran_id').prop('disabled', true);
        //         $('#program_edit_sasaran_indikator_id').prop('disabled', true);
        //     }
        // });

        // $('#program_edit_sasaran_id').on('change', function(){
        //     if($(this).val() != '')
        //     {
        //         $.ajax({
        //             url: "{{ route('admin.program-rpjmd.get-sasaran-indikator') }}",
        //             method: 'POST',
        //             data: {
        //                 "_token": "{{ csrf_token() }}",
        //                 id:$(this).val()
        //             },
        //             success: function(response){
        //                 $('#program_edit_sasaran_indikator_id').empty();
        //                 $('#program_edit_sasaran_indikator_id').prop('disabled', false);
        //                 $('#program_edit_sasaran_indikator_id').append('<option value="">--- Pilih Sasaran Indikator ---</option>');
        //                 $.each(response, function(key, value){
        //                     $('#program_edit_sasaran_indikator_id').append(new Option(value.indikator, value.id));
        //                 });
        //             }
        //         });
        //     } else {
        //         $('#program_edit_sasaran_indikator_id').prop('disabled', true);
        //     }
        // });

        // $(document).on('click', '.edit-sasaran-indikator', function(){
        //     var sasaran_indikator_id = $(this).attr('data-sasaran-indikator-id');
        //     var id = $(this).attr('data-id');
        //     $('#program_edit_form_result').html('');
        //     $.ajax({
        //         url: "{{ url('/admin/program-rpjmd/edit') }}"+'/'+id+'/'+sasaran_indikator_id,
        //         dataType: "json",
        //         success: function(data)
        //         {
        //             $("[name='program_edit_urusan_id']").val(data.result.urusan_id).trigger('change');
        //             program_edit_program_id = data.result.program_id;
        //             $("[name='program_edit_status_program']").val(data.result.status_program).trigger('change');
        //             $('#program_edit_pagu').val(data.result.pagu);
        //             $("[name='program_edit_misi_id']").val(data.result.misi_id).trigger('change');
        //             program_edit_tujuan_id = data.result.tujuan_id;
        //             program_edit_sasaran_id = data.result.sasaran_id;
        //             program_edit_sasaran_indikator_id = data.result.sasaran_indikator_id;
        //             $("[name='program_edit_opd_id']").val(data.result.opd_id).trigger('change');
        //             $('#program_edit_hidden_id').val(id);
        //             $('.modal-title').text('Edit Data Program RPJMD');
        //             $('#program_edit_aksi_button').text('Edit');
        //             $('#program_edit_aksi_button').prop('disabled', false);
        //             $('#program_edit_aksi_button').val('Edit');
        //             $('#program_edit_aksi').val('Edit');
        //             $('#editProgramModal').modal('show');
        //         }
        //     });
        // });
        // Program End

        // Kegiatan Renstra
        $('.renstra_kegiatan_create').click(function(){
            $('#renstra_kegiatan_form')[0].reset();
            $("[name='renstra_kegiatan_kegiatan_id']").val('').trigger('change');
            $('#renstra_kegiatan_aksi_button').text('Save');
            $('#renstra_kegiatan_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Program');
            $('#renstra_kegiatan_aksi_button').val('Save');
            $('#renstra_kegiatan_aksi').val('Save');
            $('#kegiatan_renstra_form_result').html('');
            $('#renstra_kegiatan_program_id').val($(this).attr('data-program-id'));
            $('#renstra_kegiatan_program_rpjmd_id').val($(this).attr('data-program-rpjmd-id'));
            $.ajax({
                url: "{{ route('admin.renstra.get-kegiatan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:$(this).attr('data-program-id')
                },
                success: function(response){
                    $('#renstra_kegiatan_kegiatan_id').empty();
                    $('#renstra_kegiatan_kegiatan_id').prop('disabled', false);
                    $('#renstra_kegiatan_kegiatan_id').append('<option value="">--- Pilih Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#renstra_kegiatan_kegiatan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });
        });

        $('#renstra_kegiatan_form').on('submit', function(e){
            e.preventDefault();
            if($('#renstra_kegiatan_aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.renstra.store') }}",
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
    </script>
@endsection
