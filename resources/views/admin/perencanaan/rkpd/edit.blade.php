@extends('admin.layouts.app')
@section('title', 'Admin | Perencanaan | RKPD | '.$opd->nama)

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
        .box {
            float: left;
            height: 20px;
            width: 20px;
            margin-bottom: 15px;
            border: 1px solid black;
            clear: both;
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
                    <h1 class="mb-0 pb-0 display-4" id="title">Perencanaan</h1>
                    <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                        <ul class="breadcrumb pt-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.perencanaan.index') }}">Perencanaan</a></li>
                            <li class="breadcrumb-item"><a href="#">RKPD</a></li>
                            <li class="breadcrumb-item"><a href="#">Atur</a></li>
                            <li class="breadcrumb-item"><a href="#">{{$opd->nama}}</a></li>
                        </ul>
                    </nav>
                </div>
                <!-- Title End -->
            </div>
        </div>
        <!-- Title and Top Buttons End -->

        <div class="card mb-5">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6" style="text-align: left">
                        <h2 class="small-title">Tambah URUSAN untuk OPD {{$opd->nama}}</h2>
                    </div>
                    <div class="col-6" style="text-align: right">
                        <button class="btn btn-outline-primary waves-effect waves-light mr-2 rkpd_tahun_pembangunan_urusan_create" type="button" data-bs-toggle="modal" data-bs-target="#addTahunPembangunanUrusanModal" title="Tambah Urusan"><i class="fas fa-plus"></i></button>
                        <a href="{{ route('admin.perencanaan.index') }}" class="btn btn-danger btn-icon waves-effect waves-light">Kembali</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div id="divRkpd">{!! $html !!}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Urusan Start --}}
    <div class="modal fade" id="addTahunPembangunanUrusanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Urusan OPD {{$opd->nama}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        {{-- action="{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.urusan.store') }}" --}}
                        @csrf
                        <input type="hidden" name="rkpd_tahun_pembangunan_urusan_tahun" id="rkpd_tahun_pembangunan_urusan_tahun" value="{{$tahun}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_urusan_opd_id" id="rkpd_tahun_pembangunan_urusan_opd_id" value="{{$opd->id}}">
                        <div class="form-group position-relative mb-3">
                            <label for="rkpd_tahun_pembangunan_urusan_urusan_id" class="form-label">Urusan</label>
                            <select name="rkpd_tahun_pembangunan_urusan_urusan_id[]" id="rkpd_tahun_pembangunan_urusan_urusan_id" class="form-control" multiple>
                                @foreach ($urusans as $urusan)
                                    <option value="{{$urusan['id']}}">{{$urusan['deskripsi']}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_tahun_pembangunan_urusan_aksi" id="rkpd_tahun_pembangunan_urusan_aksi" value="Save">
                    <button type="button" class="btn btn-primary" name="rkpd_tahun_pembangunan_urusan_aksi_button" id="rkpd_tahun_pembangunan_urusan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Modal Urusan End --}}

    {{-- Modal Program Start --}}
    <div class="modal fade" id="addTahunPembangunanProgramModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Program OPD {{$opd->nama}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        {{-- action="{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.program.store') }}" --}}
                        @csrf
                        <input type="hidden" name="rkpd_tahun_pembangunan_program_tahun" id="rkpd_tahun_pembangunan_program_tahun" value="{{$tahun}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_program_opd_id" id="rkpd_tahun_pembangunan_program_opd_id" value="{{$opd->id}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_program_urusan_id" id="rkpd_tahun_pembangunan_program_urusan_id">
                        <div class="form-group position-relative mb-3">
                            <label for="rkpd_tahun_pembangunan_program_program_id" class="form-label">Program</label>
                            <select name="rkpd_tahun_pembangunan_program_program_id[]" id="rkpd_tahun_pembangunan_program_program_id" class="form-control" multiple>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_tahun_pembangunan_program_aksi" id="rkpd_tahun_pembangunan_program_aksi" value="Save">
                    <button type="button" class="btn btn-primary" name="rkpd_tahun_pembangunan_program_aksi_button" id="rkpd_tahun_pembangunan_program_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Modal Program End --}}

    {{-- Modal Kegiatan Start --}}
    <div class="modal fade" id="addTahunPembangunanKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Kegiatan OPD {{$opd->nama}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.kegiatan.store') }}" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="rkpd_tahun_pembangunan_kegiatan_tahun" value="{{$tahun}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_kegiatan_opd_id" value="{{$opd->id}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_kegiatan_urusan_id" id="rkpd_tahun_pembangunan_kegiatan_urusan_id">
                        <input type="hidden" name="rkpd_tahun_pembangunan_kegiatan_program_id" id="rkpd_tahun_pembangunan_kegiatan_program_id">
                        <div class="form-group position-relative mb-3">
                            <label for="rkpd_tahun_pembangunan_kegiatan_kegiatan_id" class="form-label">Kegiatan</label>
                            <select name="rkpd_tahun_pembangunan_kegiatan_kegiatan_id[]" id="rkpd_tahun_pembangunan_kegiatan_kegiatan_id" class="form-control" multiple>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_tahun_pembangunan_kegiatan_aksi" id="rkpd_tahun_pembangunan_kegiatan_aksi" value="Save">
                    <button type="submit" class="btn btn-primary" name="rkpd_tahun_pembangunan_kegiatan_aksi_button" id="rkpd_tahun_pembangunan_kegiatan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Modal Kegiatan End --}}

    {{-- Modal Sub Kegiatan Start --}}
    <div class="modal fade" id="addTahunPembangunanSubKegiatanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur Sub Kegiatan OPD {{$opd->nama}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.sub-kegiatan.store') }}" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_tahun" value="{{$tahun}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_opd_id" value="{{$opd->id}}">
                        <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_urusan_id" id="rkpd_tahun_pembangunan_sub_kegiatan_urusan_id">
                        <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_program_id" id="rkpd_tahun_pembangunan_sub_kegiatan_program_id">
                        <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_kegiatan_id" id="rkpd_tahun_pembangunan_sub_kegiatan_kegiatan_id">
                        <div class="form-group position-relative mb-3">
                            <label for="rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id" class="form-label">Sub Kegiatan</label>
                            <select name="rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id[]" id="rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id" class="form-control" multiple>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_tahun_pembangunan_sub_kegiatan_aksi" id="rkpd_tahun_pembangunan_sub_kegiatan_aksi" value="Save">
                    <button type="submit" class="btn btn-primary" name="rkpd_tahun_pembangunan_sub_kegiatan_aksi_button" id="rkpd_tahun_pembangunan_sub_kegiatan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    {{-- Modal Sub Kegiatan End --}}
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
            $('#rkpd_tahun_pembangunan_urusan_urusan_id').select2({
                dropdownParent: $("#addTahunPembangunanUrusanModal")
            });

            $('#rkpd_tahun_pembangunan_program_program_id').select2({
                dropdownParent: $("#addTahunPembangunanProgramModal")
            });

            $('#rkpd_tahun_pembangunan_kegiatan_kegiatan_id').select2({
                dropdownParent: $("#addTahunPembangunanKegiatanModal")
            });

            $('#rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id').select2({
                dropdownParent: $('#addTahunPembangunanSubKegiatanModal')
            });
        });

        $(document).on('click', '.btn-open-rkpd-tahun-pembangunan-program', function(){
            var value = $(this).val();
            var tahun = $(this).attr('data-tahun');
            var urusan_id = $(this).attr('data-urusan-id');
            var opd_id = $(this).attr('data-opd-id');
            $('.btn-open-rkpd-tahun-pembangunan-program.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun).empty();
            if(value == 'close')
            {
                $('.btn-open-rkpd-tahun-pembangunan-program.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun).val('open');
                $('.btn-open-rkpd-tahun-pembangunan-program.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-rkpd-tahun-pembangunan-program.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun).val('close');
                $('.btn-open-rkpd-tahun-pembangunan-program.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $(document).on('click', '.button-add-rkpd-tahun-pembangunan-program', function(){
            var urusan_id = $(this).attr('data-urusan-id');
            var opd_id = $(this).attr('data-opd-id');
            var tahun = $(this).attr('data-tahun');

            $.ajax({
                url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/program') }}" + '/' + opd_id + '/' + tahun + '/' + urusan_id,
                dataType: "json",
                success: function(data)
                {
                    $('#rkpd_tahun_pembangunan_program_program_id').empty();
                    $.each(data, function(key, value){
                        $('#rkpd_tahun_pembangunan_program_program_id').append(new Option(value.deskripsi, value.id));
                    });
                }
            });

            $('#rkpd_tahun_pembangunan_program_urusan_id').val(urusan_id);
            $('#addTahunPembangunanProgramModal').modal('show');
        });

        $(document).on('click', '.btn-open-rkpd-tahun-pembangunan-kegiatan', function(){
            var value = $(this).val();
            var tahun = $(this).attr('data-tahun');
            var urusan_id = $(this).attr('data-urusan-id');
            var opd_id = $(this).attr('data-opd-id');
            var program_id = $(this).attr('data-program-id');
            $('.btn-open-rkpd-tahun-pembangunan-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id).empty();
            if(value == 'close')
            {
                $('.btn-open-rkpd-tahun-pembangunan-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id).val('open');
                $('.btn-open-rkpd-tahun-pembangunan-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-rkpd-tahun-pembangunan-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id).val('close');
                $('.btn-open-rkpd-tahun-pembangunan-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $(document).on('click', '.button-add-rkpd-tahun-pembangunan-kegiatan', function(){
            var urusan_id = $(this).attr('data-urusan-id');
            var program_id = $(this).attr('data-program-id');
            var opd_id = $(this).attr('data-opd-id');
            var tahun = $(this).attr('data-tahun');

            $.ajax({
                url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/kegiatan') }}" + '/' + opd_id + '/' + tahun + '/' + urusan_id + '/' + program_id,
                dataType: "json",
                success: function(data)
                {
                    $('#rkpd_tahun_pembangunan_kegiatan_kegiatan_id').empty();
                    $.each(data, function(key, value){
                        $('#rkpd_tahun_pembangunan_kegiatan_kegiatan_id').append(new Option(value.deskripsi, value.id));
                    });
                }
            });

            $('#rkpd_tahun_pembangunan_kegiatan_urusan_id').val(urusan_id);
            $('#rkpd_tahun_pembangunan_kegiatan_program_id').val(program_id);
            $('#addTahunPembangunanKegiatanModal').modal('show');
        });

        $(document).on('click', '.btn-open-rkpd-tahun-pembangunan-sub-kegiatan', function(){
            var value = $(this).val();
            var tahun = $(this).attr('data-tahun');
            var urusan_id = $(this).attr('data-urusan-id');
            var opd_id = $(this).attr('data-opd-id');
            var program_id = $(this).attr('data-program-id');
            var kegiatan_id = $(this).attr('data-kegiatan-id');
            $('.btn-open-rkpd-tahun-pembangunan-sub-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id+'.data-kegiatan-'+kegiatan_id).empty();
            if(value == 'close')
            {
                $('.btn-open-rkpd-tahun-pembangunan-sub-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id+'.data-kegiatan-'+kegiatan_id).val('open');
                $('.btn-open-rkpd-tahun-pembangunan-sub-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id+'.data-kegiatan-'+kegiatan_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-rkpd-tahun-pembangunan-sub-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id+'.data-kegiatan-'+kegiatan_id).val('close');
                $('.btn-open-rkpd-tahun-pembangunan-sub-kegiatan.data-urusan-'+urusan_id+'.data-opd-'+opd_id+'.data-tahun-'+tahun+'.data-program-'+program_id+'.data-kegiatan-'+kegiatan_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $(document).on('click', '.button-add-rkpd-tahun-pembangunan-sub-kegiatan', function(){
            var urusan_id = $(this).attr('data-urusan-id');
            var program_id = $(this).attr('data-program-id');
            var kegiatan_id = $(this).attr('data-kegiatan-id');
            var opd_id = $(this).attr('data-opd-id');
            var tahun = $(this).attr('data-tahun');

            $.ajax({
                url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/sub-kegiatan') }}" + '/' + opd_id + '/' + tahun + '/' + urusan_id + '/' + program_id  + '/' + kegiatan_id,
                dataType: "json",
                success: function(data)
                {
                    $('#rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id').empty();
                    $.each(data, function(key, value){
                        $('#rkpd_tahun_pembangunan_sub_kegiatan_sub_kegiatan_id').append(new Option(value.deskripsi, value.id));
                    });
                }
            });

            $('#rkpd_tahun_pembangunan_sub_kegiatan_urusan_id').val(urusan_id);
            $('#rkpd_tahun_pembangunan_sub_kegiatan_program_id').val(program_id);
            $('#rkpd_tahun_pembangunan_sub_kegiatan_kegiatan_id').val(kegiatan_id);
            $('#addTahunPembangunanSubKegiatanModal').modal('show');
        });

        $('#rkpd_tahun_pembangunan_urusan_aksi_button').click(function(){
            var rkpd_tahun_pembangunan_urusan_tahun = $('#rkpd_tahun_pembangunan_urusan_tahun').val();
            var rkpd_tahun_pembangunan_urusan_opd_id = $('#rkpd_tahun_pembangunan_urusan_opd_id').val();
            var rkpd_tahun_pembangunan_urusan_urusan_id = $('#rkpd_tahun_pembangunan_urusan_urusan_id').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.urusan.store') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    rkpd_tahun_pembangunan_urusan_tahun:rkpd_tahun_pembangunan_urusan_tahun,
                    rkpd_tahun_pembangunan_urusan_opd_id:rkpd_tahun_pembangunan_urusan_opd_id,
                    rkpd_tahun_pembangunan_urusan_urusan_id:rkpd_tahun_pembangunan_urusan_urusan_id
                },
                beforeSend: function()
                {
                    return new swal({
                        title: "Checking...",
                        text: "Harap Menunggu",
                        imageUrl: "{{ asset('/images/preloader.gif') }}",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#divRkpd').html(data.html);
                        $("[name='rkpd_tahun_pembangunan_urusan_urusan_id[]']").val('').trigger('change');
                        $('#addTahunPembangunanUrusanModal').modal('hide');
                    }
                }
            });
        });

        $('#rkpd_tahun_pembangunan_program_aksi_button').click(function(){
            var rkpd_tahun_pembangunan_program_tahun = $('#rkpd_tahun_pembangunan_program_tahun').val();
            var rkpd_tahun_pembangunan_program_opd_id = $('#rkpd_tahun_pembangunan_program_opd_id').val();
            var rkpd_tahun_pembangunan_program_urusan_id = $('#rkpd_tahun_pembangunan_program_urusan_id').val();
            var rkpd_tahun_pembangunan_program_program_id = $('#rkpd_tahun_pembangunan_program_program_id').val();

            $.ajax({
                url: "{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.program.store') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    rkpd_tahun_pembangunan_program_tahun:rkpd_tahun_pembangunan_program_tahun,
                    rkpd_tahun_pembangunan_program_opd_id:rkpd_tahun_pembangunan_program_opd_id,
                    rkpd_tahun_pembangunan_program_urusan_id:rkpd_tahun_pembangunan_program_urusan_id,
                    rkpd_tahun_pembangunan_program_program_id:rkpd_tahun_pembangunan_program_program_id
                },
                beforeSend: function()
                {
                    return new swal({
                        title: "Checking...",
                        text: "Harap Menunggu",
                        imageUrl: "{{ asset('/images/preloader.gif') }}",
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                },
                success: function(data)
                {
                    var html = '';
                    if(data.errors)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: data.errors,
                            showConfirmButton: true
                        });
                    }
                    if(data.success)
                    {
                        Swal.fire({
                            icon: 'success',
                            title: data.success,
                            showConfirmButton: true
                        });
                        $('#divRkpd').html(data.html);
                        $("[name='rkpd_tahun_pembangunan_program_program_id[]']").val('').trigger('change');
                        $('#addTahunPembangunanProgramModal').modal('hide');
                    }
                }
            });
        });
    </script>
@endsection
