@extends('admin.layouts.app')
@section('title', 'Admin | Akun | BAPPEDA')

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
<div class="container">
    <!-- Title and Top Buttons Start -->
    <div class="page-title-container">
        <div class="row">
        <!-- Title Start -->
        <div class="col-12 col-md-7">
            <h1 class="mb-0 pb-0 display-4" id="title">Manajemen Akun</h1>
            <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                <ul class="breadcrumb pt-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Manajemen Akun</a></li>
                    <li class="breadcrumb-item"><a href="#">BAPPEDA</a></li>
                </ul>
            </nav>
        </div>
        <!-- Title End -->
        </div>
    </div>
    <!-- Title and Top Buttons End -->

    <div class="row mb-3">
        <div class="col-12" style="text-align: right">
            <button class="btn btn-outline-primary waves-effect waves-light" id="create" type="button" data-bs-toggle="modal" data-bs-target="#addEditModal">Tambah</button>
        </div>
    </div>

    <div class="data-table-rows slim mb-5">
        <h2 class="small-title">Akun Aktif</h2>
        <!-- Table Start -->
        <div class="data-table-responsive-wrapper">
            <table id="bappeda_table" class="data-table nowrap w-100">
                <thead>
                    <tr>
                        <th class="text-muted text-small text-uppercase">No</th>
                        <th class="text-muted text-small text-uppercase">Nama Admin</th>
                        <th class="text-muted text-small text-uppercase">Email</th>
                        <th class="text-muted text-small text-uppercase">Telp</th>
                        <th class="text-muted text-small text-uppercase">Foto</th>
                        <th class="text-muted text-small text-uppercase">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Table End -->
    </div>

    <div class="data-table-rows slim">
        <h2 class="small-title">Akun Tidak Aktif</h2>
        <!-- Table Start -->
        <div class="data-table-responsive-wrapper">
            <table id="akun_tidak_aktif_bappeda_table" class="data-table nowrap w-100">
                <thead>
                    <tr>
                        <th class="text-muted text-small text-uppercase">No</th>
                        <th class="text-muted text-small text-uppercase">Nama Admin</th>
                        <th class="text-muted text-small text-uppercase">Email</th>
                        <th class="text-muted text-small text-uppercase">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Table End -->
    </div>
</div>

<div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="form_result"></span>
                <form id="bappeda_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-7">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input name="name" id="name" type="text" class="form-control" required/>
                            </div>
                            <div class="mb-3">
                                <label for="no_hp" class="form-label">No. HP</label>
                                <input type="number" name="no_hp" id="no_hp" class="form-control" required>
                            </div>
                            <div class="mb-3" id="form_email">
                                <label class="form-label">Email</label>
                                <input name="email" id="email" type="email" class="form-control" required/>
                            </div>
                            <div class="mb-3" id="form_password">
                                <label class="form-label">Password</label>
                                <input name="password" id="password" type="password" class="form-control" required/>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <label for="" class="form-label">Foto Admin</label>
                            <input type="file" class="dropify" name="foto" id="foto" data-height="300" data-allowed-file-extensions="png jpg jpeg webp" data-show-errors="true" required>
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detail Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input id="detail_name" type="text" class="form-control" disabled/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input id="detail_email" type="text" class="form-control" disabled/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Telp</label>
                            <input type="text" class="form-control" id="detail_no_hp" disabled>
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="mb-3 text-center">
                            <img src="" alt="" class="img-fluid" id="detail_foto">
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
            $('.dropify').dropify();
            $('.dropify-wrapper').css('line-height', '3rem');
            var dataTables = $('#bappeda_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.manajemen-akun.bappeda.index') }}",
                },
                columns:[
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'no_hp',
                        name: 'no_hp'
                    },
                    {
                        data: 'foto',
                        name: 'foto'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false
                    },
                ]
            });

            var dataTables = $('#akun_tidak_aktif_bappeda_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.manajemen-akun.bappeda.get-akun-tidak-aktif') }}",
                },
                columns:[
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false
                    },
                ]
            });
        });

        $('#create').click(function(){
            $('#bappeda_form')[0].reset();
            $('.dropify-clear').click();
            $('#aksi_button').text('Save');
            $('#aksi_button').prop('disabled', false);
            $('.modal-title').text('Add Data');
            $('#aksi_button').val('Save');
            $('#aksi').val('Save');
            $('#form_result').html('');
            $('#form_email').show();
            $('#form_password').show();
        });

        $('#bappeda_form').on('submit', function(e){
            e.preventDefault();
            if($('#aksi').val() == 'Save')
            {
                $.ajax({
                    url: "{{ route('admin.manajemen-akun.bappeda.store') }}",
                    method: "POST",
                    data: new FormData(this),
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
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
                            $('#aksi_button').prop('disabled', false);
                            $('#bappeda_form')[0].reset();
                            $('.dropify-clear').click();
                            $('#aksi_button').text('Save');
                            $('#bappeda_table').DataTable().ajax.reload();
                            $('#addEditModal').modal('hide');
                            Swal.fire({
                                icon: 'error',
                                title: data.errors,
                                showConfirmButton: true
                            });
                        }
                        if(data.success)
                        {
                            $('#aksi_button').prop('disabled', false);
                            $('#bappeda_form')[0].reset();
                            $('.dropify-clear').click();
                            $('#aksi_button').text('Save');
                            $('#bappeda_table').DataTable().ajax.reload();
                            $('#addEditModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: data.success,
                                showConfirmButton: true
                            });
                        }

                        $('#form_result').html(html);
                    }
                });
            }

            if($('#aksi').val() == 'Edit')
            {
                $.ajax({
                    url: "{{ route('admin.manajemen-akun.bappeda.update') }}",
                    method: "POST",
                    data: new FormData(this),
                    dataType: "json",
                    contentType: false,
                    cache: false,
                    processData: false,
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
                            $('#aksi_button').text('Save');
                        }
                        if(data.success)
                        {
                            // html = '<div class="alert alert-success">'+ data.success +'</div>';
                            $('#bappeda_form')[0].reset();
                            $('#aksi_button').prop('disabled', false);
                            $('#aksi_button').text('Save');
                            $('#bappeda_table').DataTable().ajax.reload();
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

        $(document).on('click', '.detail', function(){
            var id = $(this).attr('id');
            $.ajax({
                url: "{{ url('/admin/manajemen-akun/bappeda/detail') }}" + '/' + id,
                dataType: "json",
                success: function(data)
                {
                    $('#detail-title').text('Detail Data');
                    $('#detail_nama').val(data.result.name);
                    $('#detail_email').val(data.result.email);
                    $('#detail_no_hp').val(data.result.no_hp);
                    $('#detail_foto').attr('src', "{{ asset('images/bappeda')}}" + '/' + data.result.foto);
                    $('#detailModal').modal('show');
                }
            });
        });

        $(document).on('click', '.change-password', function(){
            var id = $(this).attr('id');
            return new swal({
                title: "Apakah Anda Yakin Merubah Password?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('admin.manajemen-akun.bappeda.change-password') }}",
                        method: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id' : id
                        },
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
                                $('#bappeda_table').DataTable().ajax.reload();
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

        $(document).on('click', '.delete', function(){
            var id = $(this).attr('id');
            return new swal({
                title: "Apakah Anda Yakin Menghapus Akun?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ url('/admin/manajemen-akun/bappeda/destroy') }}" + '/' + id,
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
                                $('#bappeda_table').DataTable().ajax.reload();
                                $('#akun_tidak_aktif_bappeda_table').DataTable().ajax.reload();
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

        $(document).on('click', '.aktif', function(){
            var id = $(this).attr('id');
            return new swal({
                title: "Apakah Anda Yakin Mengatifkan Kembali Akun Ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ url('/admin/manajemen-akun/bappeda/aktif') }}" + '/' + id,
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
                                $('#bappeda_table').DataTable().ajax.reload();
                                $('#akun_tidak_aktif_bappeda_table').DataTable().ajax.reload();
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

        $(document).on('click', '.edit', function(){
            var id = $(this).attr('id');
            var url = "{{ route('admin.manajemen-akun.bappeda.edit', ['id' =>":id"]) }}"
            url = url.replace(":id", id);
            $('#form_result').html('');
            $.ajax({
                url: url,
                dataType: "json",
                success: function(data)
                {
                    $('#form_password').hide();
                    $('#form_email').hide();
                    $('#name').val(data.result.name);
                    $('#no_hp').val(data.result.no_hp);

                    var lokasi_img_opd = "{{ asset('images/bappeda') }}"+'/'+data.result.foto;
                    var fileDropperFoto = $("#foto").dropify();

                    fileDropperFoto = fileDropperFoto.data('dropify');
                    fileDropperFoto.resetPreview();
                    fileDropperFoto.clearElement();
                    fileDropperFoto.settings['defaultFile'] = lokasi_img_opd;
                    fileDropperFoto.destroy();
                    fileDropperFoto.init();

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
    </script>
@endsection
