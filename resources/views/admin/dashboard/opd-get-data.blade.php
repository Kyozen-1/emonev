@extends('admin.layouts.app')
@section('title', 'Admin | Dashboard | '.$opd->nama)

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
                <h1 class="mb-0 pb-0 display-4" id="title">{{$opd->nama}}</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">{{$opd->nama}}</li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->
        <div class="card">
            <div class="card-body">
                <div class="border-0 p-0">
                    <ul class="nav nav-pills responsive-tabs" role="tablist">
                        @foreach ($tahuns as $tahun)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{$loop->first ? 'active' : ''}} navDetailOPD" data-bs-toggle="tab" data-bs-target="#detail_opd_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                                    {{$tahun}}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach ($tahuns as $tahun)
                            <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="detail_opd_{{$tahun}}" role="tabpanel">
                                <table class="table table-striped table-bordered text-center">
                                    <thead>
                                        <tr class="align-middle">
                                            <th scope="col" rowspan="3">No</th>
                                            <th scope="col" rowspan="3">TujuanPd/SasaranPd/Program/Kegiatan/SubKegiatan</th>
                                            <th scope="col" rowspan="3">Indikator</th>
                                            <th scope="col" colspan="2">Target</th>
                                            <th scope="col" colspan="8">Realisasi</th>
                                        </tr>
                                        <tr class="align-middle">
                                            <th scope="col" rowspan="2">K</th>
                                            <th scope="col" rowspan="2">RP</th>
                                            <th scope="col" colspan="2">I</th>
                                            <th scope="col" colspan="2">II</th>
                                            <th scope="col" colspan="2">III</th>
                                            <th scope="col" colspan="2">IV</th>
                                        </tr>
                                        <tr>
                                            <th scope="col">K</th>
                                            <th scope="col">RP</th>
                                            <th scope="col">K</th>
                                            <th scope="col">RP</th>
                                            <th scope="col">K</th>
                                            <th scope="col">RP</th>
                                            <th scope="col">K</th>
                                            <th scope="col">RP</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyDetailOpd{{$tahun}}"></tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/apexcharts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
    <script>
        var tahun_awal = "{{$tahun_awal}}";
        var opd_id = "{{$opd_id}}";
        $(document).ready(function(){
            var url = "{{ route('admin.dashboard.opd.get-data.tahun', ['opd_id'=>":opd_id", 'tahun' => ":tahun"]) }}";
            url = url.replace(":opd_id", opd_id);
            url = url.replace(":tahun", tahun_awal);
            $.ajax({
                url:url,
                method: 'GET',
                success: function(html)
                {
                    $("#tbodyDetailOpd"+tahun_awal).html(html);
                }
            });
        });

        $('.navDetailOPD').click(function(){
            var tahun = $(this).attr('data-tahun');
            var url = "{{ route('admin.dashboard.opd.get-data.tahun', ['opd_id'=>":opd_id", 'tahun' => ":tahun"]) }}";
            url = url.replace(":opd_id", opd_id);
            url = url.replace(":tahun", tahun);
            $.ajax({
                url: url,
                method: "GET",
                success: function(html)
                {
                    $("#tbodyDetailOpd"+tahun).html(html);
                }
            });
        });
    </script>
@endsection
