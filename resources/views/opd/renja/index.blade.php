@extends('opd.layouts.app')
@section('title', 'OPD | Renja')

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
                <h1 class="mb-0 pb-0 display-4" id="title">Renja</h1>
                <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
                    <ul class="breadcrumb pt-0">
                        <li class="breadcrumb-item"><a href="{{ route('opd.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Renja</a></li>
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
                        <button class="nav-link active" id="renja_tujuan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_tujuan" role="tab" aria-selected="false" type="button">Tujuan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renja_sasaran_tab_button" data-bs-toggle="tab" data-bs-target="#renja_sasaran" role="tab" aria-selected="false" type="button">Sasaran</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renja_program_tab_button" data-bs-toggle="tab" data-bs-target="#renja_program" role="tab" aria-selected="false" type="button">Program</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renja_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_kegiatan" role="tab" aria-selected="false" type="button">Kegiatan</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="renja_sub_kegiatan_tab_button" data-bs-toggle="tab" data-bs-target="#renja_sub_kegiatan" role="tab" aria-selected="false" type="button">Sub Kegiatan</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    {{-- Tujuan Start --}}
                    <div class="tab-pane fade show active" id="renja_tujuan" role="tabpanel">
                        <div id="renjaTujuanNavDiv"></div>
                    </div>
                    {{-- Tujuan End --}}
                    {{-- Sasaran Start --}}
                    <div class="tab-pane fade" id="renja_sasaran" role="tabpanel">
                        <div id="renjaSasaranNavDiv"></div>
                    </div>
                    {{-- Sasaran End --}}
                    {{-- Program Start --}}
                    <div class="tab-pane fade" id="renja_program" role="tabpanel">
                        <div id="renjaProgramNavDiv"></div>
                    </div>
                    {{-- Program End --}}
                    {{-- Kegiatan Start --}}
                    <div class="tab-pane fade" id="renja_kegiatan" role="tabpanel">
                        <div id="renjaKegiatanNavDiv"></div>
                    </div>
                    {{-- Kegiatan End --}}
                    {{-- Sub Kegiatan Start --}}
                    <div class="tab-pane fade" id="renja_sub_kegiatan" role="tabpanel">
                        <div id="renjaSubKegiatanNavDiv"></div>
                    </div>
                    {{-- Sub Kegiatan End --}}
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Tujuan Start --}}
    <div id="editTujuanPdRealisasiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTujuanPdRealisasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Realisasi Tujuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.tujuan.realisasi.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tujuan_pd_realisasi_renja_id" id="tujuan_pd_realisasi_renja_id">
                        <div class="form-group position-relative mb-3">
                            <label for="tujuan_pd_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="tujuan_pd_edit_realisasi" name="tujuan_pd_edit_realisasi" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Tujuan End --}}

    {{-- Modal Sasaran Start --}}
    <div id="editSasaranPdRealisasiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editSasaranPdRealisasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Realisasi Sasaran</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.sasaran.realisasi.update') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sasaran_pd_realisasi_renja_id" id="sasaran_pd_realisasi_renja_id">
                        <div class="form-group position-relative mb-3">
                            <label for="sasaran_pd_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="sasaran_pd_edit_realisasi" name="sasaran_pd_edit_realisasi" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Sasaran End --}}

    {{-- Modal Program Start --}}
    <div id="editTargetProgramModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Tw Program</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.program.tw.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="program_tw_realisasi_id" id="program_tw_realisasi_id">
                        <div class="form-group position-relative mb-3">
                            <label for="program_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="program_edit_realisasi" name="program_edit_realisasi" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Program End --}}

    {{-- Modal Kegiatan Start --}}
    <div id="editTargetKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Tw Kegiatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.kegiatan.tw.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="kegiatan_tw_realisasi_id" id="kegiatan_tw_realisasi_id">
                        <div class="form-group position-relative">
                            <label for="kegiatan_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="kegiatan_edit_realisasi" name="kegiatan_edit_realisasi" required>
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label for="kegiatan_edit_realisasi_rp" class="form-label">Realisasi RP</label>
                            <input type="text" class="form-control" id="kegiatan_edit_realisasi_rp" name="kegiatan_edit_realisasi_rp" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Kegiatan End --}}

    {{-- Modal Sub Kegiatan Start --}}
    <div id="indikatorKinerjaSubKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="indikatorKinerjaSubKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Tambah Indikator Kinerja Sub Kegiatan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.sub-kegiatan.indikator-kinerja.tambah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="indikator_kinerja_sub_kegiatan_sub_kegiatan_id" id="indikator_kinerja_sub_kegiatan_sub_kegiatan_id">
                        <div class="mb-3 position-relative form-group">
                            <label class="d-block form-label">Tambah Indikator Kinerja</label>
                            <input id="indikator_kinerja_sub_kegiatan_deskripsi" name="indikator_kinerja_sub_kegiatan_deskripsi"/>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Tambah Indikator Kinerja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editTargetSubKegiatanModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editTargetSubKegiatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data Target Satuan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sub_kegiatan_target_satuan_rp_realisasi" id="sub_kegiatan_target_satuan_rp_realisasi">
                        <div class="form-group position-relative">
                            <label for="sub_kegiatan_edit_target" class="form-label">Target</label>
                            <input type="text" class="form-control" id="sub_kegiatan_edit_target" name="sub_kegiatan_edit_target" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="sub_kegiatan_edit_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="sub_kegiatan_edit_satuan" name="sub_kegiatan_edit_satuan" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="sub_kegiatan_edit_target_rp" class="form-label">Target RP</label>
                            <input type="text" class="form-control" id="sub_kegiatan_edit_target_rp" name="sub_kegiatan_edit_target_rp" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editSubKegiatanRealisasiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editSubKegiatanRealisasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="detail-title">Edit Data TW Realisasi</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('opd.renja.sub-kegiatan.indikator-kinerja.tw.ubah') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="sub_kegiatan_tw_realisasi_id" id="sub_kegiatan_tw_realisasi_id">
                        <div class="form-group position-relative">
                            <label for="sub_kegiatan_edit_realisasi" class="form-label">Realisasi</label>
                            <input type="text" class="form-control" id="sub_kegiatan_edit_realisasi" name="sub_kegiatan_edit_realisasi" required>
                        </div>
                        <div class="form-group position-relative">
                            <label for="sub_kegiatan_edit_realisasi_rp" class="form-label">Realisasi RP</label>
                            <input type="text" class="form-control" id="sub_kegiatan_edit_realisasi_rp" name="sub_kegiatan_edit_realisasi_rp" required>
                        </div>
                        <div class="position-relative form-group" style="text-align: right">
                            <button class="btn btn-success waves-effect waves-light">Simpan</button>
                        </div>
                    </form>
                </div>
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
            $.ajax({
                url: "{{ route('opd.renja.get_tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });

            new Tagify(document.querySelector('#indikator_kinerja_sub_kegiatan_deskripsi'));
        });

        // Renja Tujuan Start
        $('#renja_tujuan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renja.get_tujuan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaTujuanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('click', '.button-tujuan-pd-realisasi-renja', function(){
            var tujuan_pd_indikator_kinerja_id = $(this).attr('data-tujuan-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var tujuan_pd_target_satuan_rp_realisasi_id = $(this).attr('data-tujuan-pd-target-satuan-rp-realisasi-id');

            var realisasi = $('.tujuan-pd-add-realisasi.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id+'.data-tujuan-pd-target-satuan-rp-realisasi-'+tujuan_pd_target_satuan_rp_realisasi_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.tujuan.realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tujuan_pd_target_satuan_rp_realisasi_id:tujuan_pd_target_satuan_rp_realisasi_id,
                            realisasi:realisasi
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-tujuan-pd-edit-realisasi-renja', function(){
            var tujuan_pd_indikator_kinerja_id = $(this).attr('data-tujuan-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var tujuan_pd_target_satuan_rp_realisasi_pd_id = $(this).attr('data-tujuan-pd-target-satuan-rp-realisasi-id');
            var tujuan_pd_realisasi_renja_id = $(this).attr('data-tujuan-pd-realisasi-renja-id');

            var realisasi = $('.tujuan-pd-span-realisasi.'+tahun+'.data-tujuan-pd-indikator-kinerja-'+tujuan_pd_indikator_kinerja_id+'.data-tujuan-pd-target-satuan-rp-realisasi-'+tujuan_pd_target_satuan_rp_realisasi_pd_id+'.data-tujuan-pd-realisasi-renja-'+tujuan_pd_realisasi_renja_id).text();

            $('#tujuan_pd_realisasi_renja_id').val(tujuan_pd_realisasi_renja_id);
            $('#tujuan_pd_edit_realisasi').val(realisasi);
            $('#editTujuanPdRealisasiModal').modal('show');
        });
        // Renja Tujuan End

        // Renja Sasaran Start
        $('#renja_sasaran_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renja.get_sasaran') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaSasaranNavDiv').html(data.html);
                }
            });
        });

        $(document).on('click', '.button-sasaran-pd-realisasi-renja', function(){
            var sasaran_pd_indikator_kinerja_id = $(this).attr('data-sasaran-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var sasaran_pd_target_satuan_rp_realisasi_id = $(this).attr('data-sasaran-pd-target-satuan-rp-realisasi-id');

            var realisasi = $('.sasaran-pd-add-realisasi.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id+'.data-sasaran-pd-target-satuan-rp-realisasi-'+sasaran_pd_target_satuan_rp_realisasi_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.sasaran.realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sasaran_pd_target_satuan_rp_realisasi_id:sasaran_pd_target_satuan_rp_realisasi_id,
                            realisasi:realisasi
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sasaran-pd-edit-realisasi-renja', function(){
            var sasaran_pd_indikator_kinerja_id = $(this).attr('data-sasaran-pd-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');
            var sasaran_pd_target_satuan_rp_realisasi_pd_id = $(this).attr('data-sasaran-pd-target-satuan-rp-realisasi-id');
            var sasaran_pd_realisasi_renja_id = $(this).attr('data-sasaran-pd-realisasi-renja-id');

            var realisasi = $('.sasaran-pd-span-realisasi.'+tahun+'.data-sasaran-pd-indikator-kinerja-'+sasaran_pd_indikator_kinerja_id+'.data-sasaran-pd-target-satuan-rp-realisasi-'+sasaran_pd_target_satuan_rp_realisasi_pd_id+'.data-sasaran-pd-realisasi-renja-'+sasaran_pd_realisasi_renja_id).text();

            $('#sasaran_pd_realisasi_renja_id').val(sasaran_pd_realisasi_renja_id);
            $('#sasaran_pd_edit_realisasi').val(realisasi);
            $('#editSasaranPdRealisasiModal').modal('show');
        });
        // Renja Sasaran End

        // Renja Program Start
        $('#renja_program_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renja.get_program') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaProgramNavDiv').html(data.html);
                }
            });
        });

        $(document).on('click', '.btn-open-tw-realisasi', function(){
            var value = $(this).val();
            var tahun = $(this).attr('data-tahun');
            var program_target_satuan_rp_realisasi_id = $(this).attr('data-program-target-satuan-rp-realisasi-id');
            $('.btn-open-tw-realisasi.'+tahun+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).empty();
            if(value == 'close')
            {
                $('.btn-open-tw-realisasi.'+tahun+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).val('open');
                $('.btn-open-tw-realisasi.'+tahun+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).html('<i class="fas fa-chevron-down"></i>');
            }
            if(value == 'open')
            {
                $('.btn-open-tw-realisasi.'+tahun+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).val('close');
                $('.btn-open-tw-realisasi.'+tahun+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).html('<i class="fas fa-chevron-right"></i>');
            }
        });

        $(document).on('click', '.button-add-program-target-satuan-rp-realisasi', function(){
            var tw_id = $(this).attr('data-tw-id');
            var program_target_satuan_rp_realisasi_id = $(this).attr('data-program-target-satuan-rp-realisasi-id');

            var realisasi = $('.input-program-tw-realisasi-renja-realisasi.'+tw_id+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi_id).val();
            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.program.tw.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            program_target_satuan_rp_realisasi_id:program_target_satuan_rp_realisasi_id,
                            tw_id:tw_id,
                            realisasi:realisasi,
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-edit-program-target-satuan-rp-realisasi', function(){
            var program_tw_realisasi_id = $(this).attr('data-program-tw-realisasi-renja-id');
            var program_target_satuan_rp_realisasi = $(this).attr('data-program-target-satuan-rp-realisasi-id');
            var tw_id = $(this).attr('data-tw-id');

            var realisasi = $('.span-program-tw-realisasi-renja.'+tw_id+'.data-program-target-satuan-rp-realisasi-'+program_target_satuan_rp_realisasi+'.data-program-tw-realisasi-renja-'+program_tw_realisasi_id).text();
            $('#program_tw_realisasi_id').val(program_tw_realisasi_id);
            $('#program_edit_realisasi').val(realisasi);
            $('#editTargetProgramModal').modal('show');
        });
        // Renja Program End

        // Renja Kegiatan Start
        $('#renja_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renja.get_kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaKegiatanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('click', '.button-kegiatan-tw-realisasi', function(){
            var kegiatan_target_satuan_rp_realisasi_id = $(this).attr('data-kegiatan-target-satuan-rp-realisasi-id');
            var tw_id = $(this).attr('data-tw-id');

            var realisasi = $('.kegiatan-add-realisasi.'+tw_id+'.data-kegiatan-target-satuan-rp-realisasi-'+kegiatan_target_satuan_rp_realisasi_id).val();
            var realisasi_rp = $('.kegiatan-add-realisasi-rp.'+tw_id+'.data-kegiatan-target-satuan-rp-realisasi-'+kegiatan_target_satuan_rp_realisasi_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.kegiatan.tw.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            kegiatan_target_satuan_rp_realisasi_id:kegiatan_target_satuan_rp_realisasi_id,
                            tw_id:tw_id,
                            realisasi:realisasi,
                            realisasi_rp:realisasi_rp
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-kegiatan-edit-tw-realisasi', function(){
            var kegiatan_tw_realisasi_id = $(this).attr('data-kegiatan-tw-realisasi-id');
            var tw_id = $(this).attr('data-tw-id');

            var realisasi = $('.kegiatan-span-realisasi.'+tw_id+'.data-kegiatan-tw-realisasi-'+kegiatan_tw_realisasi_id).text();
            var realisasi_rp = $('.kegiatan-span-realisasi-rp.'+tw_id+'.data-kegiatan-tw-realisasi-'+kegiatan_tw_realisasi_id).text();
            $('#kegiatan_tw_realisasi_id').val(kegiatan_tw_realisasi_id);
            $('#kegiatan_edit_realisasi').val(realisasi);
            $('#kegiatan_edit_realisasi_rp').val(realisasi_rp);
            $('#editTargetKegiatanModal').modal('show');
        });
        // Renja Kegiatan End

        // Renja Sub Kegiatan Start
        $('#renja_sub_kegiatan_tab_button').click(function(){
            $.ajax({
                url: "{{ route('opd.renja.get-sub-kegiatan') }}",
                dataType: "json",
                success: function(data)
                {
                    $('#renjaSubKegiatanNavDiv').html(data.html);
                }
            });
        });

        $(document).on('click', '.tambah-sub-kegiatan-indikator-kinerja', function(){
            var sub_kegiatan_id = $(this).attr('data-sub-kegiatan-id');
            $('#indikator_kinerja_sub_kegiatan_sub_kegiatan_id').val(sub_kegiatan_id);
            $('#indikatorKinerjaSubKegiatanModal').modal('show');
        });

        $(document).on('click', '.btn-hapus-sub-kegiatan-indikator-kinerja', function(){
            var sub_kegiatan_indikator_kinerja_id = $(this).attr('data-sub-kegiatan-indikator-kinerja-id');

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
                        url: "{{ route('opd.renja.sub-kegiatan.indikator-kinerja.hapus') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            sub_kegiatan_indikator_kinerja_id:sub_kegiatan_indikator_kinerja_id
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sub-kegiatan-target-satuan-rp-realisasi', function(){
            var opd_sub_kegiatan_indikator_kinerja_id = $(this).attr('data-opd-sub-kegiatan-indikator-kinerja-id');
            var tahun = $(this).attr('data-tahun');

            var target = $('.sub-kegiatan-add-target.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id).val();
            var satuan = $('.sub-kegiatan-add-satuan.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id).val();
            var target_rp = $('.sub-kegiatan-add-target-rp.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tahun:tahun,
                            opd_sub_kegiatan_indikator_kinerja_id:opd_sub_kegiatan_indikator_kinerja_id,
                            target:target,
                            satuan:satuan,
                            target_rp:target_rp
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sub-kegiatan-edit-target-satuan-rp-realisasi', function(){
            var opd_sub_kegiatan_indikator_kinerja_id = $(this).attr('data-opd-sub-kegiatan-indikator-kinerja-id');
            var sub_kegiatan_id = $(this).attr('data-sub-kegiatan-id');
            var tahun = $(this).attr('data-tahun');
            var sub_kegiatan_target_satuan_rp_realisasi = $(this).attr('data-sub-kegiatan-target-satuan-rp-realisasi');
            var target = $('.sub-kegiatan-span-target.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id+'.data-sub-kegiatan-id-'+sub_kegiatan_id).text();
            var satuan = $('.sub-kegiatan-span-satuan.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id+'.data-sub-kegiatan-id-'+sub_kegiatan_id).text();
            var target_rp = $('.sub-kegiatan-span-target-rp.'+tahun+'.data-opd-sub-kegiatan-indikator-kinerja-'+opd_sub_kegiatan_indikator_kinerja_id+'.data-sub-kegiatan-id-'+sub_kegiatan_id).attr('data-target-rp');

            $('#sub_kegiatan_target_satuan_rp_realisasi').val(sub_kegiatan_target_satuan_rp_realisasi);
            $('#sub_kegiatan_edit_target').val(target);
            $('#sub_kegiatan_edit_satuan').val(satuan);
            $('#sub_kegiatan_edit_target_rp').val(target_rp);

            $('#editTargetSubKegiatanModal').modal('show');
        });

        $(document).on('click', '.button-sub-kegiatan-sub-kegiatan-tw-realisasi', function(){
            var sub_kegiatan_target_satuan_rp_realisasi_id = $(this).attr('data-sub-kegiatan-target-satuan-rp-realisasi-id');
            var tw_id = $(this).attr('data-tw-id');

            var realisasi= $('.sub-kegiatan-add-realisasi.'+tw_id+'.data-sub-kegiatan-target-satuan-rp-realisasi-'+sub_kegiatan_target_satuan_rp_realisasi_id).val();
            var realisasi_rp = $('.sub-kegiatan-add-realisasi-rp.'+tw_id+'.data-sub-kegiatan-target-satuan-rp-realisasi-'+sub_kegiatan_target_satuan_rp_realisasi_id).val();

            return new swal({
                title: "Apakah Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#1976D2",
                confirmButtonText: "Ya"
            }).then((result)=>{
                if(result.value)
                {
                    $.ajax({
                        url: "{{ route('opd.renja.sub-kegiatan.indikator-kinerja.tw.tambah') }}",
                        method: 'POST',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            tw_id:tw_id,
                            sub_kegiatan_target_satuan_rp_realisasi_id:sub_kegiatan_target_satuan_rp_realisasi_id,
                            realisasi:realisasi,
                            realisasi_rp:realisasi_rp
                        },
                        dataType: "json",
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
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                }).then(function() {
                                    window.location.href = "{{ route('opd.renja.index') }}";
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.button-sub-kegiatan-edit-sub-kegiatan-tw-realisasi', function(){
            var sub_kegiatan_tw_realisasi_id = $(this).attr('data-sub-kegiatan-tw-realisasi-id');
            var sub_kegiatan_target_satuan_rp_realisasi_id = $(this).attr('data-sub-kegiatan-target-satuan-rp-realisasi-id');
            var tw_id = $(this).attr('data-tw-id');
            var sub_kegiatan_target_satuan_rp_realisasi = $(this).attr('data-sub-kegiatan-target-satuan-rp-realisasi');
            var realisasi = $('.sub-kegiatan-span-realisasi.'+tw_id+'.data-sub-kegiatan-target-satuan-rp-realisasi-'+sub_kegiatan_target_satuan_rp_realisasi_id+'.data-sub-kegiatan-tw-realisasi-id-'+sub_kegiatan_tw_realisasi_id).text();
            var realisasi_rp = $('.sub-kegiatan-span-realisasi-rp.'+tw_id+'.data-sub-kegiatan-target-satuan-rp-realisasi-'+sub_kegiatan_target_satuan_rp_realisasi_id+'.data-sub-kegiatan-tw-realisasi-id-'+sub_kegiatan_tw_realisasi_id).text();

            $('#sub_kegiatan_tw_realisasi_id').val(sub_kegiatan_tw_realisasi_id);
            $('#sub_kegiatan_edit_realisasi').val(realisasi);
            $('#sub_kegiatan_edit_realisasi_rp').val(realisasi_rp);

            $('#editSubKegiatanRealisasiModal').modal('show');
        });
        // Renja Sub Kegiatan End

    </script>
@endsection
