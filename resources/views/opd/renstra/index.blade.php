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
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-pills responsive-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="renstra_tujuan_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_tujuan_pd" role="tab" aria-selected="true" type="button">Tujuan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renstra_sasaran_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_sasaran_pd" role="tab" aria-selected="false" type="button">Sasaran</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renstra_program_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_program" role="tab" aria-selected="false" type="button">Program</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renstra_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renstra_kegiatan" role="tab" aria-selected="false" type="button">Kegiatan</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="renstra_tujuan_pd" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_tujuan_filter_visi" id="renstra_tujuan_filter_visi" class="form-control">
                                        <option value="">--- Pilih Visi ---</option>
                                        <option value="aman">Aman</option>
                                        <option value="mandiri">Mandiri</option>
                                        <option value="sejahtera">Sejahtera</option>
                                        <option value="berahlak">Berahlak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Misi</label>
                                    <select name="renstra_tujuan_filter_misi" id="renstra_tujuan_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_tujuan_filter_tujuan" id="renstra_tujuan_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_tujuan_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_tujuan_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraTujuanNavDiv"></div>
                    </div>
                    <div class="tab-pane fade" id="renstra_sasaran_pd" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_sasaran_filter_visi" id="renstra_sasaran_filter_visi" class="form-control">
                                        <option value="">--- Pilih Visi ---</option>
                                        <option value="aman">Aman</option>
                                        <option value="mandiri">Mandiri</option>
                                        <option value="sejahtera">Sejahtera</option>
                                        <option value="berahlak">Berahlak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Misi</label>
                                    <select name="renstra_sasaran_filter_misi" id="renstra_sasaran_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_sasaran_filter_tujuan" id="renstra_sasaran_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_sasaran_filter_sasaran" id="renstra_sasaran_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_sasaran_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_sasaran_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraSasaranNavDiv"></div>
                    </div>
                    <div class="tab-pane fade" id="renstra_program" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_program_filter_visi" id="renstra_program_filter_visi" class="form-control">
                                        <option value="">--- Pilih Visi ---</option>
                                        <option value="aman">Aman</option>
                                        <option value="mandiri">Mandiri</option>
                                        <option value="sejahtera">Sejahtera</option>
                                        <option value="berahlak">Berahlak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Misi</label>
                                    <select name="renstra_program_filter_misi" id="renstra_program_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_program_filter_tujuan" id="renstra_program_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_program_filter_sasaran" id="renstra_program_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Program</label>
                                    <select name="renstra_program_filter_program" id="renstra_program_filter_program" class="form-control" disabled>
                                        <option value="">--- Pilih Program ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1" type="button" id="renstra_program_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_program_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraProgramNavDiv"></div>
                    </div>
                    <div class="tab-pane fade" id="renstra_kegiatan" role="tabpanel">
                        <div class="row mb-5">
                            <div class="col-12">
                                <h2 class="small-title">Filter Data</h2>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Visi</label>
                                    <select name="renstra_kegiatan_filter_visi" id="renstra_kegiatan_filter_visi" class="form-control">
                                        <option value="">--- Pilih Visi ---</option>
                                        <option value="aman">Aman</option>
                                        <option value="mandiri">Mandiri</option>
                                        <option value="sejahtera">Sejahtera</option>
                                        <option value="berahlak">Berahlak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Misi</label>
                                    <select name="renstra_kegiatan_filter_misi" id="renstra_kegiatan_filter_misi" class="form-control" disabled>
                                        <option value="">--- Pilih Misi ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Tujuan</label>
                                    <select name="renstra_kegiatan_filter_tujuan" id="renstra_kegiatan_filter_tujuan" class="form-control" disabled>
                                        <option value="">--- Pilih Tujuan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Sasaran</label>
                                    <select name="renstra_kegiatan_filter_sasaran" id="renstra_kegiatan_filter_sasaran" class="form-control" disabled>
                                        <option value="">--- Pilih Sasaran ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Program</label>
                                    <select name="renstra_kegiatan_filter_program" id="renstra_kegiatan_filter_program" class="form-control" disabled>
                                        <option value="">--- Pilih Program ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3">
                                    <label for="" class="form-label">Kegiatan</label>
                                    <select name="renstra_kegiatan_filter_kegiatan" id="renstra_kegiatan_filter_kegiatan" class="form-control" disabled>
                                        <option value="">--- Pilih Kegiatan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group position-relative mb-3 justify-content-center align-self-center" style="text-align: center">
                                    <button class="btn btn-primary waves-effect waves-light mr-1 mb-2" type="button" id="renstra_kegiatan_btn_filter">Filter Data</button>
                                    <button class="btn btn-secondary waves-effect waves-light" type="button" id="renstra_kegiatan_btn_reset">Reset</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="renstraKegiatanNavDiv"></div>
                    </div>
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
        $(document).ready(function(){
            $('#renstra_tujuan_filter_visi').select2();
            $('#renstra_tujuan_filter_misi').select2();
            $('#renstra_tujuan_filter_tujuan').select2();

            $('#renstra_sasaran_filter_visi').select2();
            $('#renstra_sasaran_filter_misi').select2();
            $('#renstra_sasaran_filter_tujuan').select2();
            $('#renstra_sasaran_filter_sasaran').select2();

            $('#renstra_program_filter_visi').select2();
            $('#renstra_program_filter_misi').select2();
            $('#renstra_program_filter_tujuan').select2();
            $('#renstra_program_filter_sasaran').select2();
            $('#renstra_program_filter_program').select2();

            $.ajax({
                url: "{{ route('opd.renstra.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraTujuan',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-tujuan-tagging').show();
            } else {
                $('.renstra-tujuan-tagging').hide();
            }
        });

        $('#renstra_sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraSasaran',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-sasaran-tagging').show();
            } else {
                $('.renstra-sasaran-tagging').hide();
            }
        });

        $('#renstra_program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraProgramNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraProgram',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-program-tagging').show();
            } else {
                $('.renstra-program-tagging').hide();
            }
        });

        // Filter Data Tujuan
        $('#renstra_tujuan_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_misi').empty();
                        $('#renstra_tujuan_filter_misi').prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                        $('#renstra_tujuan_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_misi').prop('disabled', true);
                $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                $("[name='renstra_tujuan_filter_misi']").val('').trigger('change');
                $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            }
        });

        $('#renstra_tujuan_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_tujuan_filter_tujuan').empty();
                        $('#renstra_tujuan_filter_tujuan').prop('disabled', false);
                        $('#renstra_tujuan_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_tujuan_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
                $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            }
        });

        $('#renstra_tujuan_btn_filter').click(function(){
            var visi = $('#renstra_tujuan_filter_visi').val();
            var misi = $('#renstra_tujuan_filter_misi').val();
            var tujuan = $('#renstra_tujuan_filter_tujuan').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_tujuan_btn_reset').click(function(){
            $('#renstra_tujuan_filter_misi').prop('disabled', true);
            $('#renstra_tujuan_filter_tujuan').prop('disabled', true);
            $("[name='renstra_tujuan_filter_visi']").val('').trigger('change');
            $("[name='renstra_tujuan_filter_misi']").val('').trigger('change');
            $("[name='renstra_tujuan_filter_tujuan']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-tujuan') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraTujuanNavDiv').html(data.html);
                }
            });
        });

        // Filter Data Sasaran
        $('#renstra_sasaran_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_misi').empty();
                        $('#renstra_sasaran_filter_misi').prop('disabled', false);
                        $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renstra_sasaran_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_misi').prop('disabled', true);
                $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_misi']").val('').trigger('change');
                $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_tujuan').empty();
                        $('#renstra_sasaran_filter_tujuan').prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                        $('#renstra_sasaran_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
                $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_sasaran_filter_sasaran').empty();
                        $('#renstra_sasaran_filter_sasaran').prop('disabled', false);
                        $('#renstra_sasaran_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_sasaran_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
                $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            }
        });

        $('#renstra_sasaran_btn_filter').click(function(){
            var visi = $('#renstra_sasaran_filter_visi').val();
            var misi = $('#renstra_sasaran_filter_misi').val();
            var tujuan = $('#renstra_sasaran_filter_tujuan').val();
            var sasaran = $('#renstra_sasaran_filter_sasaran').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran
                },
                success: function(data)
                {
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_sasaran_btn_reset').click(function(){
            $('#renstra_sasaran_filter_misi').prop('disabled', true);
            $('#renstra_sasaran_filter_tujuan').prop('disabled', true);
            $('#renstra_sasaran_filter_sasaran').prop('disabled', true);
            $("[name='renstra_sasaran_filter_visi']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_misi']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_tujuan']").val('').trigger('change');
            $("[name='renstra_sasaran_filter_sasaran']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-sasaran') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraSasaranNavDiv').html(data.html);
                }
            });
        });

        // Filter Data Program
        $('#renstra_program_filter_visi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-misi') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_misi').empty();
                        $('#renstra_program_filter_misi').prop('disabled', false);
                        $('#renstra_program_filter_tujuan').prop('disabled', true);
                        $('#renstra_program_filter_sasaran').prop('disabled', true);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_misi').append('<option value="">--- Pilih Misi ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_misi').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_misi').prop('disabled', true);
                $('#renstra_program_filter_tujuan').prop('disabled', true);
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_misi']").val('').trigger('change');
                $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_misi').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-tujuan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_tujuan').empty();
                        $('#renstra_program_filter_tujuan').prop('disabled', false);
                        $('#renstra_program_filter_sasaran').prop('disabled', true);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_tujuan').append('<option value="">--- Pilih Tujuan ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_tujuan').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_tujuan').prop('disabled', true);
                $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_tujuan').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-sasaran') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_sasaran').empty();
                        $('#renstra_program_filter_sasaran').prop('disabled', false);
                        $('#renstra_program_filter_program').prop('disabled', true);
                        $('#renstra_program_filter_sasaran').append('<option value="">--- Pilih Sasaran ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_sasaran').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_sasaran').prop('disabled', true);
                $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_filter_sasaran').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('opd.renstra.filter-get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#renstra_program_filter_program').empty();
                        $('#renstra_program_filter_program').prop('disabled', false);
                        $('#renstra_program_filter_program').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#renstra_program_filter_program').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                        });
                    }
                });
            } else {
                $('#renstra_program_filter_program').prop('disabled', true);
                $("[name='renstra_program_filter_program']").val('').trigger('change');
            }
        });

        $('#renstra_program_btn_filter').click(function(){
            var visi = $('#renstra_program_filter_visi').val();
            var misi = $('#renstra_program_filter_misi').val();
            var tujuan = $('#renstra_program_filter_tujuan').val();
            var sasaran = $('#renstra_program_filter_sasaran').val();
            var program = $('#renstra_program_filter_program').val();

            $.ajax({
                url: "{{ route('opd.renstra.filter.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    visi: visi,
                    misi: misi,
                    tujuan: tujuan,
                    sasaran: sasaran,
                    program: program
                },
                success: function(data)
                {
                    $('#renstraProgramNavDiv').html(data.html);
                }
            });
        });

        $('#renstra_program_btn_reset').click(function(){
            $('#renstra_program_filter_misi').prop('disabled', true);
            $('#renstra_program_filter_tujuan').prop('disabled', true);
            $('#renstra_program_filter_sasaran').prop('disabled', true);
            $('#renstra_program_filter_program').prop('disabled', true);
            $("[name='renstra_program_filter_visi']").val('').trigger('change');
            $("[name='renstra_program_filter_misi']").val('').trigger('change');
            $("[name='renstra_program_filter_tujuan']").val('').trigger('change');
            $("[name='renstra_program_filter_sasaran']").val('').trigger('change');
            $("[name='renstra_program_filter_program']").val('').trigger('change');
            $.ajax({
                url: "{{ route('opd.renstra.reset.get-program') }}",
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data)
                {
                    $('#renstraProgramNavDiv').html(data.html);
                }
            });
        });

        // Renstra Kegiatan
        $('#renstra_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renstra.get-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renstraKegiatanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('change', '#onOffTaggingRenstraKegiatan',function(){
            if($(this).prop('checked') == true)
            {
                $('.renstra-kegiatan-tagging').show();
            } else {
                $('.renstra-kegiatan-tagging').hide();
            }
        });

        $(document).on('click','.renstra_kegiatan_create',function(){
            $('#renstra_kegiatan_form')[0].reset();
            $("[name='renstra_kegiatan_kegiatan_id']").val('').trigger('change');
            $('#renstra_kegiatan_aksi_button').text('Save');
            $('#renstra_kegiatan_aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data Kegiatan');
            $('#renstra_kegiatan_aksi_button').val('Save');
            $('#renstra_kegiatan_aksi').val('Save');
            $('#kegiatan_renstra_form_result').html('');
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
                    $('#renstra_kegiatan_kegiatan_id').append('<option value="">--- Pilih Kegiatan ---</option>');
                    $.each(response, function(key, value){
                        $('#renstra_kegiatan_kegiatan_id').append(new Option(value.kode +'. '+value.deskripsi, value.id));
                    });
                }
            });

            $.ajax({
                url: "{{ route('admin.renstra.get-opd') }}",
                method: 'POST',
                data: {
                    "_token" : "{{ csrf_token() }}",
                    id: $(this).attr('data-program-rpjmd-id')
                },
                success: function(response)
                {
                    $('#renstra_kegiatan_opd_id').empty();
                    $('#renstra_kegiatan_opd_id').append('<option value="">--- Pilih OPD ---</option>');
                    $.each(response, function(key, value){
                        $('#renstra_kegiatan_opd_id').append(new Option(value.nama, value.id));
                    });
                }
            });
        });
    </script>
@endsection
