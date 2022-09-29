@extends('admin.layouts.app')
@section('title', 'Admin | Sub Kegiatan')

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

        @media (min-width: 374px) {
            .scrollBarPagination {
                height:200px;
                overflow-y: scroll;
            }
        }
        @media (min-width: 992px) {
            .scrollBarPagination {
                height:700px;
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
            <h1 class="mb-0 pb-0 display-4" id="title">Sub Kegiatan</h1>
            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                <ul class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Nomenklatur</a></li>
                    <li class="breadcrumb-item"><a href="#">Sub Kegiatan</a></li>
                </ul>
            </nav>
        </div>
        <!-- Title End -->
        </div>
    </div>
    <!-- Title and Top Buttons End -->

    <div class="row mb-3">
        <div class="col-12" style="text-align: right">
            <button class="btn btn-outline-primary waves-effect waves-light mr-2" id="create" type="button" data-bs-toggle="modal" data-bs-target="#addEditModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
            <a class="btn btn-outline-success waves-effect waves-light mr-2" href="{{ asset('template/template_impor_sub_kegiatan.xlsx') }}" title="Download Template Import Data"><i class="fas fa-file-excel"></i></a>
            <button class="btn btn-outline-info waves-effect waves-light" title="Import Data" id="btn_impor_template" type="button"><i class="fas fa-file-import"></i></button>
        </div>
    </div>

    <div class="data-table-rows slim">
        <!-- Table Start -->
        <div class="data-table-responsive-wrapper">
            <table id="sub_kegiatan_table" class="data-table w-100">
                <thead>
                    <tr>
                        <th class="text-muted text-small text-uppercase">No</th>
                        <th class="text-muted text-small text-uppercase">Kode Urusan</th>
                        <th class="text-muted text-small text-uppercase">Kode Program</th>
                        <th class="text-muted text-small text-uppercase">Kode Kegiatan</th>
                        <th class="text-muted text-small text-uppercase">Kode</th>
                        <th class="text-muted text-small text-uppercase">Deskripsi</th>
                        <th class="text-muted text-small text-uppercase">Indikator</th>
                        <th class="text-muted text-small text-uppercase">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Table End -->
    </div>
</div>

<div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form id="sub_kegiatan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <select name="urusan_id" id="urusan_id" class="form-control" required>
                                <option value="">--- Pilih Urusan ---</option>
                                @foreach ($urusan as $id => $deskripsi)
                                    <option value="{{$id}}">{{$deskripsi}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Program</label>
                            <select name="program_id" id="program_id" class="form-control" disabled required>
                                <option value="">--- Pilih Program ---</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <select name="kegiatan_id" id="kegiatan_id" class="form-control" disabled required>
                                <option value="">--- Pilih Kegiatan ---</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input name="kode" id="kode" type="text" class="form-control" placeholder="01" required/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                <input type="hidden" name="aksi" id="aksi" value="Save">
                <input type="hidden" name="hidden_id" id="hidden_id">
                <button type="submit" class="btn btn-primary" name="aksi_button" id="aksi_button">Add</button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detail Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <textarea id="detail_urusan" class="form-control" rows="5" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Program</label>
                            <textarea id="detail_program" class="form-control" rows="5" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Kegiatan</label>
                            <textarea id="detail_kegiatan" class="form-control" rows="5" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode</label>
                            <input id="detail_kode" type="text" class="form-control" disabled/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Deskripsi</label>
                            <textarea name="detail_deskripsi" id="detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Sub Kegiatan Indikator</label>
                            <div id="div_pivot_sub_kegiatan_indikator" class="scrollBarPagination"></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="mb-3">
                            <label for="" class="form-label">Perubahan Sub Kegiatan</label>
                            <div id="div_pivot_perubahan_sub_kegiatan" class="scrollBarPagination"></div>
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

<div id="importModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="detail-title">Import Data</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.sub-kegiatan.impor') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 position-relative form-group">
                        <input type="file" class="dropify" id="impor_sub_kegiatan" name="impor_sub_kegiatan" data-height="150" data-allowed-file-extensions="xlsx" data-show-errors="true" required>
                    </div>
                    <div class="mb-3 position-relative form-group">
                        <button class="btn btn-success waves-effect waves-light">Impor</button>
                    </div>
                </form>
            </div>
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
        var program_id = 0;
        var kegiatan_id = 0;
        $(document).ready(function(){
            $('.dropify').dropify();
            $('.dropify-wrapper').css('line-height', '3rem');
            $('#urusan_id').select2();
            $('#program_id').select2();
            $('#kegiatan_id').select2();

            var dataTables = $('#sub_kegiatan_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.sub-kegiatan.index') }}",
                },
                columns:[
                    {
                        data: 'DT_RowIndex'
                    },
                    {
                        data: 'urusan',
                        name: 'urusan'
                    },
                    {
                        data: 'program',
                        name: 'program'
                    },
                    {
                        data: 'kegiatan_id',
                        name: 'kegiatan_id'
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi'
                    },
                    {
                        data: 'indikator',
                        name: 'indikator'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false
                    },
                ]
            });
        });
        $(document).on('click', '.detail', function(){
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ url('/admin/sub-kegiatan/detail') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#pivot_perubahan_sub_kegiatan').remove();
                    $('#pivot_sub_kegiatan_indikator').remove();
                    $('#div_pivot_sub_kegiatan_indikator').append('<div id="pivot_sub_kegiatan_indikator"></div>');
                    $('#div_pivot_perubahan_sub_kegiatan').append('<div id="pivot_perubahan_sub_kegiatan"></div>');
                    $('#detail-title').text('Detail Data');
                    $('#detail_urusan').val(data.result.urusan);
                    $('#detail_program').val(data.result.program);
                    $('#detail_kegiatan').val(data.result.kegiatan);
                    $('#detail_kode').val(data.result.kode);
                    $('#detail_deskripsi').val(data.result.deskripsi);
                    $('#pivot_perubahan_sub_kegiatan').append(data.result.pivot_perubahan_sub_kegiatan);
                    $('#pivot_sub_kegiatan_indikator').append(data.result.pivot_sub_kegiatan_indikator);
                    $('#detailModal').modal('show');
                }
            });
        });

        $('#urusan_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.sub-kegiatan.get-program') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#program_id').empty();
                        $('#program_id').prop('disabled', false);
                        $('#program_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#program_id').append(new Option(value.deskripsi, value.id));
                        });
                        if(program_id != 0)
                        {
                            $("[name='program_id']").val(program_id).trigger('change');
                        }
                    }
                });
            }
        });

        $('#program_id').on('change', function(){
            if($(this).val() != '')
            {
                $.ajax({
                    url: "{{ route('admin.sub-kegiatan.get-kegiatan') }}",
                    method: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id:$(this).val()
                    },
                    success: function(response){
                        $('#kegiatan_id').empty();
                        $('#kegiatan_id').prop('disabled', false);
                        $('#kegiatan_id').append('<option value="">--- Pilih Program ---</option>');
                        $.each(response, function(key, value){
                            $('#kegiatan_id').append(new Option(value.deskripsi, value.id));
                        });
                        if(kegiatan_id != 0)
                        {
                            $("[name='kegiatan_id']").val(kegiatan_id).trigger('change');
                        }
                    }
                });
            }
        });

        $('#create').click(function(){
            program_id = 0;
            kegiatan_id = 0;
            $('#sub_kegiatan_form')[0].reset();
            $("[name='urusan_id']").val('').trigger('change');
            $("[name='program_id']").val('').trigger('change');
            $("[name='kegiatan_id']").val('').trigger('change');
            $('#aksi_button').text('Save');
            $('#aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data');
            $('#aksi_button').val('Save');
            $('#aksi').val('Save');
            $('#form_result').html('');
        });
        $('#sub_kegiatan_form').on('submit', function(e){
            e.preventDefault();
            if($('#aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.sub-kegiatan.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function()
                    {
                        $('#aksi_button').text('Menyimpan...');
                        $('#aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            program_id = 0;
                            kegiatan_id = 0;
                            $('#aksi_button').prop('disabled', false);
                            $('#sub_kegiatan_form')[0].reset();
                            $("[name='urusan_id']").val('').trigger('change');
                            $("[name='program_id']").val('').trigger('change');
                            $("[name='kegiatan_id']").val('').trigger('change');
                            $('#aksi_button').text('Save');
                            $('#sub_kegiatan_table').DataTable().ajax.reload();
                        }
                        if(data.success)
                        {
                            html = '<div class="alert alert-success">'+data.success+'</div>';
                            program_id = 0;
                            kegiatan_id = 0;
                            $('#aksi_button').prop('disabled', false);
                            $('#sub_kegiatan_form')[0].reset();
                            $("[name='urusan_id']").val('').trigger('change');
                            $("[name='program_id']").val('').trigger('change');
                            $("[name='kegiatan_id']").val('').trigger('change');
                            $('#aksi_button').text('Save');
                            $('#sub_kegiatan_table').DataTable().ajax.reload();
                        }

                        $('#form_result').html(html);
                    }
                });
            }
            if($('#aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.sub-kegiatan.update') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function(){
                        $('#aksi_button').text('Mengubah...');
                        $('#aksi_button').prop('disabled', true);
                    },
                    success: function(data)
                    {
                        var html = '';
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">'+data.errors+'</div>';
                            program_id = 0;
                            kegiatan_id = 0;
                            $('#aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            // html = '<div class="alert alert-success">'+ data.success +'</div>';
                            program_id = 0;
                            kegiatan_id = 0;
                            $('#sub_kegiatan_form')[0].reset();
                            $('#aksi_button').prop('disabled', false);
                            $('#aksi_button').text('Save');
                            $('#sub_kegiatan_table').DataTable().ajax.reload();
                            $('#addEditModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil di ubah',
                                showConfirmButton: true
                            });
                        }

                        $('#form_result').html(html);
                    }
                });
            }
        });
        $(document).on('click', '.edit', function(){
            var id = $(this).attr('id');
            $('#form_result').html('');
            $.ajax({
                url: "{{ url('/admin/sub-kegiatan/edit') }}"+'/'+id,
                dataType: "json",
                success: function(data)
                {
                    $('#kode').val(data.result.kode);
                    $('#deskripsi').val(data.result.deskripsi);
                    $("[name='urusan_id']").val(data.result.urusan_id).trigger('change');
                    program_id = data.result.program_id;
                    kegiatan_id = data.result.kegiatan_id;
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Data');
                    $('#aksi_button').text('Edit');
                    $('#aksi_button').prop('disabled', false);
                    $('#aksi_button').val('Edit');
                    $('#aksi').val('Edit');
                    $('#addEditModal').modal('show');
                }
            });
        });

        $(document).on('click', '.delete',function(){
            var id = $(this).attr('id');
            return new swal({
                title: "Apakah Anda Yakin Menghapus Ini? Menghapus data ini akan menghapus data yang lain!!!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ url('/admin/sub-kegiatan/destroy') }}" + '/' + id,
                        dataType: "json",
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
                            if(data.errors)
                            {
                                Swal.fire({
                                    icon: 'errors',
                                    title: data.errors,
                                    showConfirmButton: true
                                });
                            }
                            if(data.success)
                            {
                                $('#sub_kegiatan_table').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                });
                            }
                        }
                    });
                }
            });
        });

        $('#btn_impor_template').click(function(){
            $('#importModal').modal('show');
        });
    </script>
@endsection
