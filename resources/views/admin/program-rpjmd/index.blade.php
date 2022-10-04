@extends('admin.layouts.app')
@section('title', 'Admin | Program RPJMD')

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
                height:460px;
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
    @endphp
    <div class="container">
        <!-- Title and Top Buttons Start -->
        <div class="page-title-container">
            <div class="row">
            <!-- Title Start -->
            <div class="col-12 col-md-7">
                <h1 class="mb-0 pb-0 display-4" id="title">Program RPJMD</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">RPJMD</a></li>
                        <li class="breadcrumb-item"><a href="#">Program RPJMD</a></li>
                    </ul>
                </nav>
            </div>
            <!-- Title End -->
            </div>
        </div>
    </div>

    @foreach ($sasarans as $sasaran)
        @php
            $cek_perubahan_sasaran = PivotPerubahanSasaran::where('sasaran_id', $sasaran->id)->latest()->first();
            if($cek_perubahan_sasaran)
            {
                $kode_sasaran = $cek_perubahan_sasaran->kode;
                $deskripsi_sasaran = $cek_perubahan_sasaran->deskripsi;
                $tujuan_id = $cek_perubahan_sasaran->tujuan_id;
            } else {
                $sasaran = Sasaran::find($sasaran->id);
                $kode_sasaran = $sasaran->kode;
                $deskripsi_sasaran = $sasaran->deskripsi;
                $tujuan_id = $sasaran->tujuan_id;
            }

            $cek_perubahan_tujuan = PivotPerubahanTujuan::where('tujuan_id', $tujuan_id)->latest()->first();
            if($cek_perubahan_tujuan)
            {
                $kode_tujuan = $cek_perubahan_tujuan->kode;
                $deskripsi_tujuan = $cek_perubahan_tujuan->deskripsi;
                $misi_id = $cek_perubahan_tujuan->misi_id;
            } else {
                $tujuan = Tujuan::find($tujuan_id);
                $kode_tujuan = $tujuan->kode;
                $deskripsi_tujuan = $tujuan->deskripsi;
                $misi_id = $tujuan->misi_id;
            }

            $cek_perubahan_misi = PivotPerubahanMisi::where('misi_id', $misi_id)->latest()->first();
            if($cek_perubahan_misi)
            {
                $kode_misi = $cek_perubahan_misi->kode;
                $deskripsi_misi = $cek_perubahan_misi->deskripsi;
            } else {
                $misi = Misi::find($misi_id);
                $kode_misi = $misi->kode;
                $deskripsi_misi = $misi->deskripsi;
            }
        @endphp
        <div class="card mb-5">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <p>
                            Kode Misi: {{$kode_misi}}<br>
                            Misi: {{$deskripsi_misi}}<br>
                            <br>
                            Kode Tujuan: {{$kode_tujuan}}<br>
                            Tujuan: {{$deskripsi_tujuan}}<br>
                            <br>
                            Kode Sasaran: {{$kode_sasaran}}<br>
                            Sasaran: {{$deskripsi_sasaran}}<br>
                        </p>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-6 justify-content-center align-self-center">
                        <label for="" class="form-label">Tambah Program</label>
                    </div>
                    <div class="col-6" style="text-align: right">
                        <button class="btn btn-primary waves-effect waves-light tambah_program" data-sasaran-id="{{$sasaran->id}}"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <hr>
                <h2 class="small-title">Program Prioritas</h2>
                @php
                    $program_prioritases = ProgramRpjmd::where('status_program', 'Program Prioritas')->where('sasaran_id', $sasaran->id)->get();
                @endphp
                <table class="table table-borderless">
                    <thead>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Program</th>
                                <th>OPD</th>
                                <th>Pagu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program_prioritases as $program_prioritas)
                            @php
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_prioritas->program_id)->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $kode_program = $cek_perubahan_program->kode;
                                    $pagu_program = $cek_perubahan_program->pagu;
                                    $urusan_id = $cek_perubahan_program->urusan_id;
                                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                                } else {
                                    $program = Program::find($program_prioritas->program_id);
                                    $pagu_program = $program->pagu;
                                    $kode_program = $program->kode;
                                    $urusan_id = $program->urusan_id;
                                    $deskripsi_program = $program->deskripsi;
                                }

                                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                                if($cek_perubahan_urusan)
                                {
                                    $kode_urusan = $cek_perubahan_urusan->kode;
                                } else {
                                    $urusan = Urusan::find($urusan_id);
                                    $kode_urusan = $urusan->kode;
                                }
                            @endphp
                                <tr>
                                    <td width="10%">{{$kode_urusan}}.{{$kode_program}}</td>
                                    <td width="30%">{{$deskripsi_program}}</td>
                                    <td width="25%">{{$program_prioritas->master_opd->nama}}</td>
                                    <th width="20%">Rp. {{number_format($pagu_program, 2)}}</th>
                                    <td width="15%">
                                        <button class="btn btn-warning waves-effect waves-light edit-program mr-2" type="button" data-id="{{$program_prioritas->id}}"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger waves-effect waves-light delete-program" type="button" data-id="{{$program_prioritas->id}}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
                <h2 class="small-title">Program Pendukung</h2>
                @php
                    $program_pendukunges = ProgramRpjmd::where('status_program', 'Program Pendukung')->where('sasaran_id', $sasaran->id)->get();
                @endphp
                <table class="table table-borderless">
                    <thead>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Program</th>
                                <th>OPD</th>
                                <th>Pagu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($program_pendukunges as $program_pendukung)
                            @php
                                $cek_perubahan_program = PivotPerubahanProgram::where('program_id', $program_pendukung->program_id)->latest()->first();
                                if($cek_perubahan_program)
                                {
                                    $kode_program = $cek_perubahan_program->kode;
                                    $pagu_program = $cek_perubahan_program->pagu;
                                    $urusan_id = $cek_perubahan_program->urusan_id;
                                    $deskripsi_program = $cek_perubahan_program->deskripsi;
                                } else {
                                    $program = Program::find($program_pendukung->program_id);
                                    $pagu_program = $program->pagu;
                                    $kode_program = $program->kode;
                                    $urusan_id = $program->urusan_id;
                                    $deskripsi_program = $program->deskripsi;
                                }

                                $cek_perubahan_urusan = PivotPerubahanUrusan::where('urusan_id', $urusan_id)->latest()->first();
                                if($cek_perubahan_urusan)
                                {
                                    $kode_urusan = $cek_perubahan_urusan->kode;
                                } else {
                                    $urusan = Urusan::find($urusan_id);
                                    $kode_urusan = $urusan->kode;
                                }
                            @endphp
                                <tr>
                                    <td width="10%">{{$kode_urusan}}.{{$kode_program}}</td>
                                    <td width="30%">{{$deskripsi_program}}</td>
                                    <td width="25%">{{$program_pendukung->master_opd->nama}}</td>
                                    <th width="20%">Rp. {{number_format($pagu_program, 2)}}</th>
                                    <td width="15%">
                                        <button class="btn btn-warning waves-effect waves-light edit-program mr-2" type="button" data-id="{{$program_pendukung->id}}"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger waves-effect waves-light delete-program" type="button" data-id="{{$program_pendukung->id}}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

    <div class="modal fade" id="addEditModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form id="program_rpjmd_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_id" id="sasaran_id">
                        <div class="mb-3">
                            <label for="" class="form-label">Misi</label>
                            <textarea rows="2" id="misi" class="form-control" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Tujuan</label>
                            <textarea rows="2" id="tujuan" class="form-control" disabled></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Sasaran</label>
                            <textarea rows="2" id="sasaran" class="form-control" disabled></textarea>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="" class="form-label">Urusan</label>
                            <select name="urusan_id" id="urusan_id" class="form-control" required>
                                <option value="">--- Urusan ---</option>
                                @foreach ($urusans as $urusan)
                                    <option value="{{$urusan['id']}}">{{$urusan['deskripsi']}}</option>
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
                            <label for="" class="form-label">Status Program</label>
                            <select name="status_program" id="status_program" class="form-control" required>
                                <option value="">--- Pilih Status Program ---</option>
                                <option value="Program Prioritas">Program Prioritas</option>
                                <option value="Program Pendukung">Program Pendukung</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Opd</label>
                            <select name="opd_id" id="opd_id" class="form-control" required>
                                <option value="">--- Pilih OPD ---</option>
                                @foreach ($master_opd as $id => $nama)
                                    <option value="{{$id}}">{{$nama}}</option>
                                @endforeach
                            </select>
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
    $(document).ready(function(){
        $('#urusan_id').select2();
        $('#program_id').select2();
        $('#opd_id').select2();
    });

    $('.tambah_program').click(function(){
        program_id = 0;
        var sasaran_id = $(this).attr('data-sasaran-id');
        $('#program_rpjmd_form')[0].reset();
        $("[name='urusan_id']").val('').trigger('change');
        $("[name='program_id']").val('').trigger('change');
        $("[name='opd_id']").val('').trigger('change');
        $.ajax({
            url: "{{ url('/admin/program-rpjmd/get-sasaran') }}" + '/' + sasaran_id,
            dataType: 'json',
            success: function(data)
            {
                $('#sasaran_id').val(sasaran_id);
                $('#misi').val(data.result.misi);
                $('#tujuan').val(data.result.tujuan);
                $('#sasaran').val(data.result.sasaran);
                $('#addEditModal').modal('show');
                $('.modal-title').text('Add Data');
                $('#aksi_button').text('Save');
                $('#aksi_button').prop('disabled', false);
                $('#aksi_button').val('Save');
                $('#aksi').val('Save');
                $('#program_id').prop('disabled', true);
            }
        });
    });

    $('#urusan_id').on('change', function(){
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

    $('.edit-program').click(function(){
        var id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('/admin/program-rpjmd/edit') }}" + '/' + id,
            dataType: "json",
            success: function(data)
            {
                $('#sasaran_id').val(data.result.sasaran_id);
                $('#misi').val(data.result.misi);
                $('#tujuan').val(data.result.tujuan);
                $('#sasaran').val(data.result.sasaran);
                $("[name='urusan_id']").val(data.result.urusan_id).trigger('change');
                $("[name='status_program']").val(data.result.status_program).trigger('change');
                $("[name='opd_id']").val(data.result.opd_id).trigger('change');
                program_id = data.result.program_id;
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

    $('#program_rpjmd_form').on('submit', function(e){
        e.preventDefault();
        if($('#aksi').val() == 'Save')
        {
            $.ajax({
                url: "{{ route('admin.program-rpjmd.store') }}",
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
                        $('#aksi_button').prop('disabled', false);
                        $('#sasaran_form')[0].reset();
                        $("[name='urusan_id']").val('').trigger('change');
                        $("[name='program_id']").val('').trigger('change');
                        $("[name='opd_id']").val('').trigger('change');
                        $('#aksi_button').text('Save');
                        $('#sasaran_table').DataTable().ajax.reload();
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#form_result').html(html);
                }
            });
        }
        if($('#aksi').val() == 'Edit')
        {
            $.ajax({
                url: "{{ route('admin.program-rpjmd.update') }}",
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
                        $('#aksi_button').text('Save');
                    }
                    if(data.success)
                    {
                        window.location.reload();
                    }

                    $('#form_result').html(html);
                }
            });
        }
    });

    $(document).on('click', '.delete-program',function(){
        var id = $(this).attr('data-id');
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
                    url: "{{ url('/admin/program-rpjmd/destroy') }}" + '/' + id,
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
                            window.location.reload();
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
