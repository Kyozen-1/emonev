    <div class="row mb-3">
        <div class="col-6" style="text-align: left">
            <h2 class="small-title">Tambah Data Tema Tahun Pembangunan</h2>
        </div>
        <div class="col-6" style="text-align: right">
            <button class="btn btn-outline-primary waves-effect waves-light mr-2 rkpd_tahun_pembangunan_create" type="button" data-bs-toggle="modal" data-bs-target="#addEditTahunPembangunanModal" title="Tambah Data"><i class="fas fa-plus"></i></button>
        </div>
    </div>
    <div class="data-table-rows slim mb-5">
        <!-- Table Start -->
        <div class="data-table-responsive-wrapper">
            <table id="rkpd_tahun_pembangunan_table" class="data-table w-100">
                <thead>
                    <tr>
                        <th style="width: 0px;">No</th>
                        <th style="width: 0px;">Deskripsi</th>
                        <th style="width: 0px;">Tahun</th>
                        <th style="width: 0px;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Table End -->
    </div>
    {{-- Modal Tema Tahun Pembangunan Start --}}
    <div class="modal fade" id="addEditTahunPembangunanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="rkpd_tahun_pembangunan_form_result"></span>
                    <form id="rkpd_tahun_pembangunan_form" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <textarea name="rkpd_tahun_pembangunan_deskripsi" id="rkpd_tahun_pembangunan_deskripsi" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="rkpd_tahun_pembangunan_tahun" class="form-label">Tahun</label>
                                <select name="rkpd_tahun_pembangunan_tahun" id="rkpd_tahun_pembangunan_tahun" class="form-control" required>
                                    <option value="">--- Pilih Tahun ---</option>
                                    @foreach ($tahuns as $tahun)
                                        <option value="{{$tahun}}">{{$tahun}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_tahun_pembangunan_aksi" id="rkpd_tahun_pembangunan_aksi" value="Save">
                    <input type="hidden" name="rkpd_tahun_pembangunan_hidden_id" id="rkpd_tahun_pembangunan_hidden_id">
                    <button type="submit" class="btn btn-primary" name="rkpd_tahun_pembangunan_aksi_button" id="rkpd_tahun_pembangunan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailRkpdTahunPembangunanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
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
                                <textarea id="rkpd_tahun_pembangunan_detail_deskripsi" rows="5" class="form-control" disabled></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Tahun</label>
                                <input type="text" class="form-control" id="rkpd_tahun_pembangunan_detail_tahun" disabled>
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
    {{-- Modal Tema Tahun Pembangunan End --}}
    <hr>
    <h2 class="small-title">Tema Tahun Pembangunan</h2>

    <div class="border-0 pb-0">
        <ul class="nav nav-pills responsive-tabs" role="tablist">
            @foreach ($tahuns as $tahun)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{$loop->first ? 'active' : ''}} navRkpd" data-bs-toggle="tab" data-bs-target="#rkpd_{{$tahun}}" role="tab" aria-selected="true" type="button" data-tahun="{{$tahun}}">
                        {{$tahun}}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content">
            @foreach ($tahuns as $tahun)
                <div class="tab-pane fade {{$loop->first ? 'active show' : ''}}" id="rkpd_{{$tahun}}" role="tabpanel">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h2 class="small-title">Filter Data</h2>
                        </div>
                        <div class="col">
                            <div class="form-group position-relative mb-3">
                                <label for="" class="form-label">OPD</label>
                                <select id="rkpd_filter_opd_{{$tahun}}" class="form-control rkpd_filter_opd" data-tahun="{{$tahun}}">
                                    <option value="">--- Semua ---</option>
                                    @foreach ($opds as $id => $nama)
                                        <option value="{{$id}}">{{$nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col" style="text-align: right">
                            <label for="" class="form-label">Aksi Filter</label>
                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                <button class="btn btn-primary waves-effect waves-light mr-1 rkpd_btn_filter" type="button" data-tahun="{{$tahun}}">Filter Data</button>
                                <button class="btn btn-secondary waves-effect waves-light rkpd_btn_reset" type="button" data-tahun="{{$tahun}}">Reset</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-6">
                            <h2 class="small-title">Atur OPD Untuk Tema Pembangunan Tahun {{$tahun}}</h2>
                        </div>
                        <div class="col-6" style="text-align: right">
                            <div class="form-group position-relative mb-3 justify-content-center align-self-center">
                                <button class="btn btn-primary waves-effect waves-light mr-1 rkpd_btn_tambah_opd" type="button" data-tahun="{{$tahun}}"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="rkpdNavDiv{{$tahun}}"></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="addOpdTahunPembangunanModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Atur OPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.opd.store') }}" class="tooltip-label-end" method="POST" novalidate enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="rkpd_opd_tahun_pembangunan_tahun" id="rkpd_opd_tahun_pembangunan_tahun">
                        <div class="form-group position-relative mb-3">
                            <label for="rkpd_opd_tahun_pembangunan_opd_id" class="form-label">OPD</label>
                            <select name="rkpd_opd_tahun_pembangunan_opd_id[]" id="rkpd_opd_tahun_pembangunan_opd_id" class="form-control" multiple>
                                @foreach ($opds as $id => $nama)
                                    <option value="{{$id}}">{{$nama}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <input type="hidden" name="rkpd_opd_tahun_pembangunan_aksi" id="rkpd_opd_tahun_pembangunan_aksi" value="Save">
                    <button type="submit" class="btn btn-primary" name="rkpd_opd_tahun_pembangunan_aksi_button" id="rkpd_opd_tahun_pembangunan_aksi_button">Add</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    @push('script_rkpd')
        <script>
            $('.rkpd_tahun_pembangunan_create').click(function(){
                $('#rkpd_tahun_pembangunan_form')[0].reset();
                $('#rkpd_tahun_pembangunan_aksi_button').text('Save');
                $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', false);
                $('.modal-title').text('Add Data');
                $('#rkpd_tahun_pembangunan_aksi_button').val('Save');
                $('#rkpd_tahun_pembangunan_aksi').val('Save');
                $('#rkpd_tahun_pembangunan_form_result').html('');
            });

            $('#rkpd_tahun_pembangunan_form').on('submit', function(e){
                e.preventDefault();
                if($('#rkpd_tahun_pembangunan_aksi').val() == 'Save')
                {
                    $.ajax({
                        url: "{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.store') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        dataType: "json",
                        beforeSend: function()
                        {
                            $('#rkpd_tahun_pembangunan_aksi_button').text('Menyimpan...');
                            $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', true);
                        },
                        success: function(data)
                        {
                            var html = '';
                            if(data.errors)
                            {
                                html = '<div class="alert alert-danger">'+data.errors+'</div>';
                                $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', false);
                                $('#rkpd_tahun_pembangunan_aksi_button').text('Save');
                            }
                            if(data.success)
                            {
                                $('#addEditTahunPembangunanModal').modal('hide');
                                $('#rkpd_tahun_pembangunan_table').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                });
                            }

                            $('#rkpd_tahun_pembangunan_form_result').html(html);
                        }
                    });
                }
                if($('#rkpd_tahun_pembangunan_aksi').val() == 'Edit')
                {
                    $.ajax({
                        url: "{{ route('admin.perencanaan.rkpd.get-tahun-pembangunan.update') }}",
                        method: "POST",
                        data: $(this).serialize(),
                        dataType: "json",
                        beforeSend: function(){
                            $('#rkpd_tahun_pembangunan_aksi_button').text('Mengubah...');
                            $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', true);
                        },
                        success: function(data)
                        {
                            var html = '';
                            if(data.errors)
                            {
                                html = '<div class="alert alert-danger">'+data.errors+'</div>';
                                $('#rkpd_tahun_pembangunan_aksi_button').text('Save');
                            }
                            if(data.success)
                            {
                                $('#rkpd_tahun_pembangunan_form')[0].reset();
                                $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', false);
                                $('#rkpd_tahun_pembangunan_aksi_button').text('Save');
                                $('#rkpd_tahun_pembangunan_table').DataTable().ajax.reload();
                                $('#addEditTahunPembangunanModal').modal('hide');
                                $('#rkpd_tahun_pembangunan_table').DataTable().ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: data.success,
                                    showConfirmButton: true
                                });
                            }

                            $('#rkpd_tahun_pembangunan_form_result').html(html);
                        }
                    });
                }
            });

            $(document).on('click', '.rkpd_tahun_pembangunan_edit', function(){
                var id = $(this).attr('id');
                $('#rkpd_tahun_pembangunan_form_result').html('');
                $.ajax({
                    url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/edit') }}"+ '/' +id,
                    dataType: "json",
                    success: function(data)
                    {
                        $('#rkpd_tahun_pembangunan_deskripsi').val(data.result.deskripsi);
                        $("[name='rkpd_tahun_pembangunan_tahun']").val(data.result.tahun);
                        $('#rkpd_tahun_pembangunan_hidden_id').val(id);
                        $('.modal-title').text('Edit Data');
                        $('#rkpd_tahun_pembangunan_aksi_button').text('Edit');
                        $('#rkpd_tahun_pembangunan_aksi_button').prop('disabled', false);
                        $('#rkpd_tahun_pembangunan_aksi_button').val('Edit');
                        $('#rkpd_tahun_pembangunan_aksi').val('Edit');
                        $('#addEditTahunPembangunanModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.rkpd_tahun_pembangunan_detail', function(){
                var id = $(this).attr('id');
                $.ajax({
                    url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/detail') }}"+'/'+id,
                    dataType: "json",
                    success: function(data)
                    {
                        $('#detail-title').text('Detail Data');
                        $('#rkpd_tahun_pembangunan_detail_deskripsi').val(data.result.deskripsi);
                        $('#rkpd_tahun_pembangunan_detail_tahun').val(data.result.tahun);
                        $('#detailRkpdTahunPembangunanModal').modal('show');
                    }
                });
            });

            $(document).on('click', '.rkpd_tahun_pembangunan_delete',function(){
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
                            url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/destroy') }}" + '/' + id,
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
                                    $('#rkpd_tahun_pembangunan_table').DataTable().ajax.reload();
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

            $('.navRkpd').click(function(){
                var tahun = $(this).attr('data-tahun');
                $.ajax({
                    url: "{{ url('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd') }}" + '/' + tahun,
                    dataType: "json",
                    success: function(data)
                    {
                        $('#rkpdNavDiv'+tahun).html(data.html);
                    }
                });
            });

            $('.rkpd_btn_tambah_opd').click(function(){
                var tahun = $(this).attr('data-tahun');
                $('#rkpd_opd_tahun_pembangunan_tahun').val(tahun);
                $('#addOpdTahunPembangunanModal').modal('show');
            });
        </script>
    @endpush
