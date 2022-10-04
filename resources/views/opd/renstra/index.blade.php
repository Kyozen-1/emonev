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
        .select2-container{
            z-index:100000;
        }
    </style>
@endsection

@section('content')
@php
    use Carbon\Carbon;
    use App\Models\Visi;
    use App\Models\PivotPerubahanVisi;
    use App\Models\Misi;
    use App\Models\PivotPerubahanMisi;
    use App\Models\Tujuan;
    use App\Models\PivotPerubahanTujuan;
    use App\Models\Sasaran;
    use App\Models\PivotPerubahanSasaran;
    use App\Models\PivotSasaranIndikator;
    use App\Models\ProgramRpjmd;
    use App\Models\PivotPerubahanProgramRpjmd;
    use App\Models\Urusan;
    use App\Models\PivotPerubahanUrusan;
    use App\Models\Program;
    use App\Models\PivotPerubahanProgram;
    use App\Models\MasterOpd;
    use App\Models\TahunPeriode;
    use App\Models\TargetRpPertahunTujuan;
    use App\Models\TargetRpPertahunSasaran;
    use App\Models\TargetRpPertahunProgram;

    $get_periode = TahunPeriode::where('status', 'Aktif')->latest()->first();
    $tahun_awal = $get_periode->tahun_awal;
    $jarak_tahun = $get_periode->tahun_akhir - $tahun_awal;
    $tahuns = [];
    for ($i=0; $i < $jarak_tahun + 1; $i++) {
        $tahuns[] = $tahun_awal + $i;
    }
@endphp
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
            <div class="card-body">
                <div class="row">
                    <div class="col-6 justify-content-center align-self-center">
                        <label for="" class="form-label">Tambah Item Renstra</label>
                    </div>
                    <div class="col-6" style="text-align: right">
                        <button class="btn btn-icon btn-primary waves-effect waves-light tambah-itme-renstra" type="button" data-bs-toggle="modal" data-bs-target="#addEditItemRenstraModal"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addEditItemRenstraModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renstra.tambah-item-renstra') }}" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Misi</label>
                            <select name="misi_id" id="misi_id" class="form-control" required>
                                <option value="">--- Pilih Misi ---</option>
                                @foreach ($misis as $misi)
                                    <option value="{{$misi['id']}}">{{$misi['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Tujuan</label>
                            <select name="tujuan_id" id="tujuan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Tujuan ---</option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Sasaran</label>
                            <select name="sasaran_id" id="sasaran_id" class="form-controlf" disabled required>
                                <option value="">--- Pilih Sasaran --- </option>
                            </select>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-controlf" disabled required>
                                <option value="">--- Pilih Program --- </option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="aksi_button" id="aksi_button">Tambah</button>
                </div>
            </form>
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

    <script>
        $(document).ready(function(){
            $('#misi_id').select2();
            $('#tujuan_id').select2();
            $('#sasaran_id').select2();
            $('#program_id').select2();
        });

        $('#misi_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#tujuan_id').empty();
                        $('#tujuan_id').prop('disabled', false);
                        $('#tujuan_id').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#tujuan_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            }
        });

        $('#tujuan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#sasaran_id').empty();
                        $('#sasaran_id').prop('disabled', false);
                        $('#sasaran_id').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#sasaran_id').append(new Option(value.deskripsi, value.id));
                        });
                    }
                });
            }
        });

        $('#sasaran_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.get-program-rpjmd') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_id').empty();
                        $('#program_id').prop('disabled', false);
                        $('#program_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(){
                            $('#program_id').append(new Option(response.deskripsi, response.id));
                        });
                    }
                });
            }
        });
    </script>
@endsection
