<?php
Route::group(['middleware' => 'auth:admin'], function(){
    Route::get('/admin/dashboard', 'Admin\DashboardController@index')->name('admin.dashboard.index');
    Route::post('/admin/dashboard/change', 'Admin\DashboardController@change')->name('admin.dashboard.change');
    Route::get('/normalisasi-opd', 'Admin\DashboardController@normalisasi_opd');

    //Kecamatan
    Route::get('/admin/kecamatan', 'Admin\KecamatanController@index')->name('admin.kecamatan.index');
    Route::get('/admin/kecamatan/detail/{id}', 'Admin\KecamatanController@show');
    Route::post('/admin/kecamatan','Admin\KecamatanController@store')->name('admin.kecamatan.store');
    Route::get('/admin/kecamatan/edit/{id}','Admin\KecamatanController@edit');
    Route::post('/admin/kecamatan/update','Admin\KecamatanController@update')->name('admin.kecamatan.update');
    Route::get('/admin/kecamatan/destroy/{id}','Admin\KecamatanController@destroy');

    //Kelurahan
    Route::get('/admin/kelurahan', 'Admin\KelurahanController@index')->name('admin.kelurahan.index');
    Route::get('/admin/kelurahan/detail/{id}', 'Admin\KelurahanController@show');
    Route::post('/admin/kelurahan','Admin\KelurahanController@store')->name('admin.kelurahan.store');
    Route::get('/admin/kelurahan/edit/{id}','Admin\KelurahanController@edit');
    Route::post('/admin/kelurahan/update','Admin\KelurahanController@update')->name('admin.kelurahan.update');
    Route::get('/admin/kelurahan/destroy/{id}','Admin\KelurahanController@destroy');

    //Jenis OPD
    Route::get('/admin/jenis-opd', 'Admin\JenisOpdController@index')->name('admin.jenis-opd.index');
    Route::get('/admin/jenis-opd/detail/{id}', 'Admin\JenisOpdController@show');
    Route::post('/admin/jenis-opd','Admin\JenisOpdController@store')->name('admin.jenis-opd.store');
    Route::get('/admin/jenis-opd/edit/{id}','Admin\JenisOpdController@edit');
    Route::post('/admin/jenis-opd/update','Admin\JenisOpdController@update')->name('admin.jenis-opd.update');
    Route::get('/admin/jenis-opd/destroy/{id}','Admin\JenisOpdController@destroy');

    //Master OPD
    Route::get('/admin/master-opd', 'Admin\MasterOpdController@index')->name('admin.master-opd.index');
    Route::get('/admin/master-opd/detail/{id}', 'Admin\MasterOpdController@show');
    Route::post('/admin/master-opd','Admin\MasterOpdController@store')->name('admin.master-opd.store');
    Route::get('/admin/master-opd/edit/{id}','Admin\MasterOpdController@edit');
    Route::post('/admin/master-opd/update','Admin\MasterOpdController@update')->name('admin.master-opd.update');
    Route::get('/admin/master-opd/destroy/{id}','Admin\MasterOpdController@destroy');

    //Manajemen Akun Opd
    Route::get('/admin/manajemen-akun/opd', 'Admin\ManajemenAkun\OpdController@index')->name('admin.manajemen-akun.opd.index');
    Route::get('/admin/manajemen-akun/opd/detail/{id}', 'Admin\ManajemenAkun\OpdController@show');
    Route::post('/admin/manajemen-akun/opd','Admin\ManajemenAkun\OpdController@store')->name('admin.manajemen-akun.opd.store');
    Route::get('/admin/manajemen-akun/opd/edit/{id}', 'Admin\ManajemenAkun\OpdController@edit')->name('admin.manajemen-akun.opd.edit');
    Route::post('/admin/manajemen-akun/opd/update','Admin\ManajemenAkun\OpdController@update')->name('admin.manajemen-akun.opd.update');
    Route::post('/admin/manajemen-akun/opd/change-password','Admin\ManajemenAkun\OpdController@change_password')->name('admin.manajemen-akun.opd.change-password');
    Route::post('/admin/manajemen-akun/opd/destroy','Admin\ManajemenAkun\OpdController@destroy')->name('admin.manajemen-akun.opd.destroy');

    //Manajemen Akun BAPPEDA
    Route::get('/admin/manajemen-akun/bappeda', 'Admin\ManajemenAkun\BappedaController@index')->name('admin.manajemen-akun.bappeda.index');
    Route::get('/admin/manajemen-akun/bappeda/get-akun-tidak-aktif', 'Admin\ManajemenAkun\BappedaController@get_akun_tidak_aktif')->name('admin.manajemen-akun.bappeda.get-akun-tidak-aktif');
    Route::get('/admin/manajemen-akun/bappeda/detail/{id}', 'Admin\ManajemenAkun\BappedaController@show');
    Route::get('/admin/manajemen-akun/bappeda/edit/{id}', 'Admin\ManajemenAkun\BappedaController@edit')->name('admin.manajemen-akun.bappeda.edit');
    Route::post('/admin/manajemen-akun/bappeda','Admin\ManajemenAkun\BappedaController@store')->name('admin.manajemen-akun.bappeda.store');
    Route::post('/admin/manajemen-akun/bappeda/update','Admin\ManajemenAkun\BappedaController@update')->name('admin.manajemen-akun.bappeda.update');
    Route::post('/admin/manajemen-akun/bappeda/change-password','Admin\ManajemenAkun\BappedaController@change_password')->name('admin.manajemen-akun.bappeda.change-password');
    Route::get('/admin/manajemen-akun/bappeda/destroy/{id}','Admin\ManajemenAkun\BappedaController@destroy');
    Route::get('/admin/manajemen-akun/bappeda/aktif/{id}','Admin\ManajemenAkun\BappedaController@aktif');

    //Master TW
    Route::get('/admin/master-tw', 'Admin\MasterTwController@index')->name('admin.master-tw.index');
    Route::get('/admin/master-tw/detail/{id}', 'Admin\MasterTwController@show');
    Route::post('/admin/master-tw','Admin\MasterTwController@store')->name('admin.master-tw.store');
    Route::get('/admin/master-tw/edit/{id}','Admin\MasterTwController@edit');
    Route::post('/admin/master-tw/update','Admin\MasterTwController@update')->name('admin.master-tw.update');
    Route::get('/admin/master-tw/destroy/{id}','Admin\MasterTwController@destroy');

    //Nomenklatur
    Route::get('/admin/nomenklatur', 'Admin\NomenklaturController@index')->name('admin.nomenklatur.index');
    Route::get('/admin/nomenklatur/get-program', 'Admin\NomenklaturController@get_program')->name('admin.nomenklatur.get-program');
    Route::get('/admin/nomenklatur/get-program/{tahun}', 'Admin\NomenklaturController@get_program_tahun');
    Route::get('/admin/nomenklatur/get-kegiatan', 'Admin\NomenklaturController@get_kegiatan')->name('admin.nomenklatur.get-kegiatan');
    Route::get('/admin/nomenklatur/get-kegiatan/{tahun}', 'Admin\NomenklaturController@get_kegiatan_tahun');
    Route::get('/admin/nomenklatur/get-sub-kegiatan', 'Admin\NomenklaturController@get_sub_kegiatan')->name('admin.nomenklatur.get-sub-kegiatan');
    Route::get('/admin/nomenklatur/get-sub-kegiatan/{tahun}', 'Admin\NomenklaturController@get_sub_kegiatan_tahun');
    Route::post('/admin/nomenklatur/filter/get-program', 'Admin\NomenklaturController@filter_get_program')->name('admin.nomenklatur.filter.get-program');
    Route::post('/admin/nomenklatur/filter/get-kegiatan', 'Admin\NomenklaturController@filter_get_kegiatan')->name('admin.nomenklatur.filter.get-kegiatan');
    Route::post('/admin/nomenklatur/filter/get-sub-kegiatan', 'Admin\NomenklaturController@filter_get_sub_kegiatan')->name('admin.nomenklatur.filter.get-sub-kegiatan');
    Route::post('/admin/nomenklatur/filter/sub-kegiatan', 'Admin\NomenklaturController@filter_sub_kegiatan')->name('admin.nomenklatur.filter.sub-kegiatan');
    Route::post('/admin/nomenklatur/reset/sub-kegiatan', 'Admin\NomenklaturController@get_sub_kegiatan')->name('admin.nomenklatur.reset.sub-kegiatan');
    Route::post('/admin/nomenklatur/filter/kegiatan', 'Admin\NomenklaturController@filter_kegiatan')->name('admin.nomenklatur.filter.kegiatan');
    Route::post('/admin/nomenklatur/reset/kegiatan', 'Admin\NomenklaturController@get_kegiatan')->name('admin.nomenklatur.reset.kegiatan');
    Route::post('/admin/nomenklatur/filter/program', 'Admin\NomenklaturController@filter_program')->name('admin.nomenklatur.filter.program');
    Route::post('/admin/nomenklatur/reset/program', 'Admin\NomenklaturController@get_program')->name('admin.nomenklatur.reset.program');

    // Perencanaan
    Route::get('/admin/perencanaan', 'Admin\PerencanaanController@index')->name('admin.perencanaan.index');
    Route::get('/admin/perencanaan/get-misi', 'Admin\PerencanaanController@get_misi')->name('admin.perencanaan.get-misi');
    Route::get('/admin/perencanaan/get-misi/{tahun}', 'Admin\PerencanaanController@get_misi_tahun');
    Route::get('/admin/perencanaan/get-tujuan', 'Admin\PerencanaanController@get_tujuan')->name('admin.perencanaan.get-tujuan');
    Route::get('/admin/perencanaan/get-tujuan/{tahun}', 'Admin\PerencanaanController@get_tujuan_tahun');
    Route::get('/admin/perencanaan/get-sasaran', 'Admin\PerencanaanController@get_sasaran')->name('admin.perencanaan.get-sasaran');
    Route::get('/admin/perencanaan/get-sasaran/{tahun}', 'Admin\PerencanaanController@get_sasaran_tahun');
    Route::get('/admin/perencanaan/get-program', 'Admin\PerencanaanController@get_program')->name('admin.perencanaan.get-program');
    Route::get('/admin/perencanaan/get-program/{tahun}', 'Admin\PerencanaanController@get_program_tahun');
    Route::post('/admin/perencanaan/filter/get-misi', 'Admin\PerencanaanController@filter_get_misi')->name('admin.perencanaan.filter.get-misi');
    Route::post('/admin/perencanaan/filter/get-tujuan', 'Admin\PerencanaanController@filter_get_tujuan')->name('admin.perencanaan.filter.get-tujuan');
    Route::post('/admin/perencanaan/filter/get-sasaran', 'Admin\PerencanaanController@filter_get_sasaran')->name('admin.perencanaan.filter.get-sasaran');
    Route::post('/admin/perencanaan/filter/get-program', 'Admin\PerencanaanController@filter_get_program')->name('admin.perencanaan.filter.get-program');
    Route::post('/admin/perencanaan/filter/get-kegiatan', 'Admin\PerencanaanController@filter_get_kegiatan')->name('admin.perencanaan.filter.get-kegiatan');
    Route::post('/admin/perencanaan/filter/program', 'Admin\PerencanaanController@filter_program')->name('admin.perencanaan.filter.program');
    Route::post('/admin/perencanaan/reset/program', 'Admin\PerencanaanController@get_program')->name('admin.perencanaan.reset.program');
    Route::post('/admin/perencanaan/filter/sasaran', 'Admin\PerencanaanController@filter_sasaran')->name('admin.perencanaan.filter.sasaran');
    Route::post('/admin/perencanaan/reset/sasaran', 'Admin\PerencanaanController@get_sasaran')->name('admin.perencanaan.reset.sasaran');
    Route::post('/admin/perencanaan/filter/tujuan', 'Admin\PerencanaanController@filter_tujuan')->name('admin.perencanaan.filter.tujuan');
    Route::post('/admin/perencanaan/reset/tujuan', 'Admin\PerencanaanController@get_tujuan')->name('admin.perencanaan.reset.tujuan');
    Route::post('/admin/perencanaan/filter/misi', 'Admin\PerencanaanController@filter_misi')->name('admin.perencanaan.filter.misi');
    Route::post('/admin/perencanaan/reset/misi', 'Admin\PerencanaanController@get_misi')->name('admin.perencanaan.reset.misi');
    Route::get('/admin/perencanaan/renstra/get-tujuan', 'Admin\Perencanaan\RenstraController@renstra_get_tujuan')->name('admin.perencanaan.renstra.get-tujuan');
    Route::get('/admin/perencanaan/renstra/get-tujuan/{tahun}', 'Admin\Perencanaan\RenstraController@renstra_get_tujuan_tahun');
    Route::post('/admin/perencanaan/renstra/filter/tujuan', 'Admin\Perencanaan\RenstraController@renstra_filter_tujuan')->name('admin.perencanaan.renstra.filter.tujuan');
    Route::post('/admin/perencanaan/renstra/reset/tujuan', 'Admin\Perencanaan\RenstraController@renstra_get_tujuan')->name('admin.perencanaan.renstra.reset.tujuan');
    Route::post('/admin/perencanaan/renstra/tujuan/lock-indikator', 'Admin\Perencanaan\RenstraController@renstra_tujuan_lock_indikator')->name('admin.perencanaan.renstra.tujuan.lock-indikator');
    Route::get('/admin/perencanaan/renstra/get-sasaran', 'Admin\Perencanaan\RenstraController@renstra_get_sasaran')->name('admin.perencanaan.renstra.get-sasaran');
    Route::get('/admin/perencanaan/renstra/get-sasaran/{tahun}', 'Admin\Perencanaan\RenstraController@renstra_get_sasaran_tahun');
    Route::post('/admin/perencanaan/renstra/filter/sasaran', 'Admin\Perencanaan\RenstraController@renstra_filter_sasaran')->name('admin.perencanaan.renstra.filter.sasaran');
    Route::post('/admin/perencanaan/renstra/reset/sasaran', 'Admin\Perencanaan\RenstraController@renstra_get_sasaran')->name('admin.perencanaan.renstra.reset.sasaran');
    Route::post('/admin/perencanaan/renstra/sasaran/lock-indikator', 'Admin\Perencanaan\RenstraController@renstra_sasaran_lock_indikator')->name('admin.perencanaan.renstra.sasaran.lock-indikator');
    Route::get('/admin/perencanaan/renstra/get-program', 'Admin\Perencanaan\RenstraController@renstra_get_program')->name('admin.perencanaan.renstra.get-program');
    Route::get('/admin/perencanaan/renstra/get-program/{tahun}', 'Admin\Perencanaan\RenstraController@renstra_get_program_tahun');
    Route::post('/admin/perencanaan/renstra/filter/program', 'Admin\Perencanaan\RenstraController@renstra_filter_program')->name('admin.perencanaan.renstra.filter.program');
    Route::post('/admin/perencanaan/renstra/reset/program', 'Admin\Perencanaan\RenstraController@renstra_get_program')->name('admin.perencanaan.renstra.reset.program');
    Route::get('/admin/perencanaan/renstra/get-kegiatan', 'Admin\Perencanaan\RenstraController@renstra_get_kegiatan')->name('admin.perencanaan.renstra.get-kegiatan');
    Route::get('/admin/perencanaan/renstra/get-kegiatan/{tahun}', 'Admin\Perencanaan\RenstraController@renstra_get_kegiatan_tahun');
    Route::post('/admin/perencanaan/renstra/filter/kegiatan', 'Admin\Perencanaan\RenstraController@renstra_filter_kegiatan')->name('admin.perencanaan.renstra.filter.kegiatan');
    Route::post('/admin/perencanaan/renstra/reset/kegiatan', 'Admin\Perencanaan\RenstraController@renstra_get_kegiatan')->name('admin.perencanaan.renstra.reset.kegiatan');
    Route::post('/admin/perencanaan/rpjmd/filter/program/status', 'Admin\PerencanaanController@rpjmd_filter_program_status')->name('admin.perencanaan.rpjmd.filter.program.status');
    Route::get('/admin/perencanaan/renja/get-tujuan', 'Admin\Perencanaan\RenjaController@renja_get_tujuan')->name('admin.perencanaan.renja.get-tujuan');
    Route::post('/admin/perencanaan/renja/get-tujuan/filter', 'Admin\Perencanaan\RenjaController@renja_get_tujuan_filter')->name('admin.perencanaan.renja.get-tujuan.filter');
    Route::post('/admin/perencanaan/renja/get-tujuan/reset', 'Admin\Perencanaan\RenjaController@renja_get_tujuan')->name('admin.perencanaan.renja.get-tujuan.reset');
    Route::get('/admin/perencanaan/renja/get-sasaran', 'Admin\Perencanaan\RenjaController@renja_get_sasaran')->name('admin.perencanaan.renja.get-sasaran');
    Route::post('/admin/perencanaan/renja/get-sasaran/filter', 'Admin\Perencanaan\RenjaController@renja_get_sasaran_filter')->name('admin.perencanaan.renja.get-sasaran.filter');
    Route::post('/admin/perencanaan/renja/get-sasaran/reset', 'Admin\Perencanaan\RenjaController@renja_get_sasaran')->name('admin.perencanaan.renja.get-sasaran.reset');
    Route::get('/admin/perencanaan/renja/get-program', 'Admin\Perencanaan\RenjaController@renja_get_program')->name('admin.perencanaan.renja.get-program');
    Route::post('/admin/perencanaan/renja/get-program/filter', 'Admin\Perencanaan\RenjaController@renja_get_program_filter')->name('admin.perencanaan.renja.get-program.filter');
    Route::post('/admin/perencanaan/renja/get-program/reset', 'Admin\Perencanaan\RenjaController@renja_get_program')->name('admin.perencanaan.renja.get-program.reset');
    Route::get('/admin/perencanaan/renja/get-kegiatan', 'Admin\Perencanaan\RenjaController@renja_get_kegiatan')->name('admin.perencanaan.renja.get-kegiatan');
    Route::post('/admin/perencanaan/renja/get-kegiatan/filter', 'Admin\Perencanaan\RenjaController@renja_get_kegiatan_filter')->name('admin.perencanaan.renja.get-kegiatan.filter');
    Route::post('/admin/perencanaan/renja/get-kegiatan/reset', 'Admin\Perencanaan\RenjaController@renja_get_kegiatan')->name('admin.perencanaan.renja.get-kegiatan.reset');
    Route::get('/admin/perencanaan/renja/get-sub-kegiatan', 'Admin\Perencanaan\RenjaController@renja_get_sub_kegiatan')->name('admin.perencanaan.renja.get-sub-kegiatan');
    Route::post('/admin/perencanaan/renja/get-sub-kegiatan/filter', 'Admin\Perencanaan\RenjaController@renja_get_sub_kegiatan_filter')->name('admin.perencanaan.renja.get-sub-kegiatan.filter');
    Route::post('/admin/perencanaan/renja/get-sub-kegiatan/reset', 'Admin\Perencanaan\RenjaController@renja_get_sub_kegiatan')->name('admin.perencanaan.renja.get-sub-kegiatan.reset');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan', 'Admin\Perencanaan\RkpdController@renja_tahun_pembangunan')->name('admin.perencanaan.rkpd.get-tahun-pembangunan');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/store','Admin\Perencanaan\RkpdController@renja_tahun_pembangunan_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.store');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/detail/{id}','Admin\Perencanaan\RkpdController@renja_tahun_pembangunan_detail');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/edit/{id}','Admin\Perencanaan\RkpdController@renja_tahun_pembangunan_edit');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/update','Admin\Perencanaan\RkpdController@renja_tahun_pembangunan_update')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.update');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/destroy/{id}','Admin\Perencanaan\RkpdController@renja_tahun_pembangunan_destroy');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/opd/store','Admin\Perencanaan\RkpdController@renja_opd_tahun_pembangunan_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.opd.store');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/{tahun}', 'Admin\Perencanaan\RkpdController@data_per_opd');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/get-opd/{tahun}', 'Admin\Perencanaan\RkpdController@get_opd');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/{opd_id}/{tahun}', 'Admin\Perencanaan\RkpdController@data_per_opd_atur')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/urusan/store', 'Admin\Perencanaan\RkpdController@data_per_opd_atur_urusan_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.urusan.store');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/program/{opd_id}/{tahun}/{urusan_id}', 'Admin\Perencanaan\RkpdController@get_program_rkpd');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/program/store', 'Admin\Perencanaan\RkpdController@data_per_opd_atur_program_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.program.store');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/kegiatan/{opd_id}/{tahun}/{urusan_id}/{program_id}', 'Admin\Perencanaan\RkpdController@get_kegiatan_rkpd');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/kegiatan/store', 'Admin\Perencanaan\RkpdController@data_per_opd_atur_kegiatan_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.kegiatan.store');
    Route::get('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/sub-kegiatan/{opd_id}/{tahun}/{urusan_id}/{program_id}/{kegiatan_id}', 'Admin\Perencanaan\RkpdController@get_sub_kegiatan_rkpd');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/atur/sub-kegiatan/store', 'Admin\Perencanaan\RkpdController@data_per_opd_atur_sub_kegiatan_store')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.atur.sub-kegiatan.store');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/filter', 'Admin\Perencanaan\RkpdController@data_per_opd_filter')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.filter');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/reset', 'Admin\Perencanaan\RkpdController@data_per_opd_reset')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.reset');
    Route::post('/admin/perencanaan/rkpd/get-tahun-pembangunan/data-per-opd/destroy', 'Admin\Perencanaan\RkpdController@data_per_opd_destroy')->name('admin.perencanaan.rkpd.get-tahun-pembangunan.data-per-opd.destroy');

    // RPJMD Tujuan
    Route::prefix('admin')->group(function(){
        Route::prefix('perencanaan')->group(function(){
            Route::prefix('rpjmd')->group(function(){
                Route::prefix('tujuan')->group(function(){
                    Route::prefix('tw')->group(function(){
                        Route::post('/tambah', 'Admin\Perencanaan\Rpjmd\TujuanController@tambah')->name('admin.perencanaan.rpjmd.tujuan.tw.tambah');
                        Route::post('/ubah', 'Admin\Perencanaan\Rpjmd\TujuanController@ubah')->name('admin.perencanaan.rpjmd.tujuan.tw.ubah');
                    });
                });

                Route::prefix('sasaran')->group(function(){
                    Route::prefix('tw')->group(function(){
                        Route::post('/tambah', 'Admin\Perencanaan\Rpjmd\SasaranController@tambah')->name('admin.perencanaan.rpjmd.sasaran.tw.tambah');
                        Route::post('/ubah', 'Admin\Perencanaan\Rpjmd\SasaranController@ubah')->name('admin.perencanaan.rpjmd.sasaran.tw.ubah');
                    });
                });
            });
        });
    });

    // Laporan
    Route::get('/admin/laporan', 'Admin\LaporanController@index')->name('admin.laporan.index');

    Route::get('/admin/laporan/tc-14', 'Admin\Laporan\Tc14Controller@tc_14')->name('admin.laporan.tc-14');
    Route::get('/admin/laporan/tc-14/ekspor/pdf', 'Admin\Laporan\Tc14Controller@tc_14_ekspor_pdf')->name('admin.laporan.tc-14.ekspor.pdf');
    Route::get('/admin/laporan/tc-14/ekspor/excel', 'Admin\Laporan\Tc14Controller@tc_14_ekspor_excel')->name('admin.laporan.tc-14.ekspor.excel');

    Route::get('/admin/laporan/tc-19/get-data', 'Admin\Laporan\Tc19Controller@tc_19')->name('admin.laporan.tc-19.get-data');
    Route::post('/admin/laporan/tc-19', 'Admin\Laporan\Tc19Controller@laporan_tc_19')->name('admin.laporan.tc-19');
    Route::get('/admin/laporan/tc-19/ekspor/pdf/{tahun}', 'Admin\Laporan\Tc19Controller@tc_19_ekspor_pdf')->name('admin.laporan.tc-19.ekspor.pdf');
    Route::get('/admin/laporan/tc-19/ekspor/excel/{tahun}', 'Admin\Laporan\Tc19Controller@tc_19_ekspor_excel')->name('admin.laporan.tc-19.ekspor.excel');

    Route::get('/admin/laporan/e-79/get-data', 'Admin\Laporan\E79Controller@e_79')->name('admin.laporan.e-79.get-data');
    Route::post('/admin/laporan/e-79', 'Admin\Laporan\E79Controller@laporan_e_79')->name('admin.laporan.e-79');
    Route::get('/admin/laporan/e-79/ekspor/pdf/{tahun}', 'Admin\Laporan\E79Controller@e_79_ekspor_pdf')->name('admin.laporan.e-79.ekspor.pdf');
    Route::get('/admin/laporan/e-79/ekspor/excel/{tahun}', 'Admin\Laporan\E79Controller@e_79_ekspor_excel')->name('admin.laporan.e-79.ekspor.excel');

    Route::get('/admin/laporan/e-78', 'Admin\Laporan\E78Controller@e_78')->name('admin.laporan.e-78');
    Route::get('/admin/laporan/e-78/ekspor/excel', 'Admin\Laporan\E78Controller@e_78_ekspor_excel')->name('admin.laporan.e-78.ekspor.excel');
    Route::get('/admin/laporan/e-78/ekspor/pdf', 'Admin\Laporan\E78Controller@e_78_ekspor_pdf')->name('admin.laporan.e-78.ekspor.pdf');

    Route::post('/admin/laporan/tc-23', 'Admin\Laporan\Tc23Controller@tc_23')->name('admin.laporan.tc-23');
    Route::get('/admin/laporan/tc-23/ekspor/pdf/{opd_id}', 'Admin\Laporan\Tc23Controller@tc_23_ekspor_pdf');
    Route::get('/admin/laporan/tc-23/ekspor/excel/{opd_id}', 'Admin\Laporan\Tc23Controller@tc_23_ekspor_excel');

    Route::post('/admin/laporan/tc-24', 'Admin\Laporan\Tc24Controller@tc_24')->name('admin.laporan.tc-24');
    Route::get('/admin/laporan/tc-24/ekspor/pdf/{opd_id}', 'Admin\Laporan\Tc24Controller@tc_24_ekspor_pdf');
    Route::get('/admin/laporan/tc-24/ekspor/excel/{opd_id}', 'Admin\Laporan\Tc24Controller@tc_24_ekspor_excel');

    Route::post('/admin/laporan/tc-27', 'Admin\Laporan\Tc27Controller@tc_27')->name('admin.laporan.tc-27');
    Route::get('/admin/laporan/tc-27/ekspor/pdf/{opd_id}', 'Admin\Laporan\Tc27Controller@tc_27_ekspor_pdf');
    Route::get('/admin/laporan/tc-27/ekspor/excel/{opd_id}', 'Admin\Laporan\Tc27Controller@tc_27_ekspor_excel');

    Route::post('/admin/laporan/e-80', 'Admin\Laporan\E80Controller@laporan_e_80')->name('admin.laporan.e-80');
    Route::get('/admin/laporan/e-80/ekspor/pdf/{opd_id}', 'Admin\Laporan\E80Controller@e_80_ekspor_pdf');
    Route::get('/admin/laporan/e-80/ekspor/excel/{opd_id}', 'Admin\Laporan\E80Controller@e_80_ekspor_excel');

    Route::post('/admin/laporan/e-81', 'Admin\Laporan\E81Controller@laporan_e_81')->name('admin.laporan.e-81');
    Route::get('/admin/laporan/e-81/ekspor/pdf/{opd_id}/{tahun}', 'Admin\Laporan\E81Controller@e_81_ekspor_pdf');
    Route::get('/admin/laporan/e-81/ekspor/excel/{opd_id}/{tahun}', 'Admin\Laporan\E81Controller@e_81_ekspor_excel');

    //Urusan
    Route::get('/admin/urusan', 'Admin\UrusanController@index')->name('admin.urusan.index');
    Route::get('/admin/urusan/get-urusan/{tahun}', 'Admin\UrusanController@get_urusan');
    Route::get('/admin/urusan/detail/{id}/{tahun}', 'Admin\UrusanController@show');
    Route::post('/admin/urusan','Admin\UrusanController@store')->name('admin.urusan.store');
    Route::get('/admin/urusan/edit/{id}/{tahun}','Admin\UrusanController@edit');
    Route::post('/admin/urusan/update','Admin\UrusanController@update')->name('admin.urusan.update');
    Route::get('/admin/urusan/destroy/{id}','Admin\UrusanController@destroy');
    Route::post('/admin/urusan/destroy/impor', 'Admin\UrusanController@impor')->name('admin.urusan.impor');

    //Program
    Route::get('/admin/program', 'Admin\ProgramController@index')->name('admin.program.index');
    Route::get('/admin/program/detail/{id}/{tahun}', 'Admin\ProgramController@show');
    Route::post('/admin/program','Admin\ProgramController@store')->name('admin.program.store');
    Route::get('/admin/program/edit/{id}/{tahun}','Admin\ProgramController@edit');
    Route::post('/admin/program/update','Admin\ProgramController@update')->name('admin.program.update');
    Route::get('/admin/program/destroy/{id}','Admin\ProgramController@destroy');
    Route::post('/admin/program/hapus','Admin\ProgramController@hapus')->name('admin.program.hapus');
    Route::post('/admin/program/destroy/impor', 'Admin\ProgramController@impor')->name('admin.program.impor');
    Route::post('/admin/program/indikator-kinerja/tambah', 'Admin\ProgramController@indikator_kinerja_tambah')->name('admin.program.indikator-kinerja.tambah');
    Route::post('/admin/program/indikator-kinerja/hapus', 'Admin\ProgramController@indikator_kinerja_hapus')->name('admin.program.indikator-kinerja.hapus');
    Route::get('/admin/program/indikator-kinerja/edit/{id}', 'Admin\ProgramController@indikator_kinerja_edit');
    Route::post('/admin/program/indikator-kinerja/update', 'Admin\ProgramController@indikator_kinerja_update')->name('admin.program.indikator-kinerja.update');
    Route::get('/admin/program/indikator-kinerja/edit/opd/{id}', 'Admin\ProgramController@opd_indikator_kinerja_edit');
    Route::post('/admin/program/indikator-kinerja/edit/opd/hapus', 'Admin\ProgramController@opd_indikator_kinerja_hapus')->name('admin.program.indikator-kinerja.opd-hapus');
    Route::post('/admin/program/indikator-kinerja/edit/opd/update', 'Admin\ProgramController@opd_indikator_kinerja_update')->name('admin.program.indikator-kinerja.opd-update');

    //Program - Indikator
    Route::get('/admin/program/{program_id}/indikator', 'Admin\ProgramIndikatorController@index');
    Route::get('/admin/program/indikator/detail/{id}', 'Admin\ProgramIndikatorController@show');
    Route::post('/admin/program/indikator','Admin\ProgramIndikatorController@store');
    Route::get('/admin/program/indikator/edit/{id}','Admin\ProgramIndikatorController@edit');
    Route::post('/admin/program/indikator/update','Admin\ProgramIndikatorController@update');
    Route::get('/admin/program/indikator/destroy/{id}','Admin\ProgramIndikatorController@destroy');
    Route::post('/admin/program/indikator/impor', 'Admin\ProgramIndikatorController@impor')->name('admin.program.indikator.impor');
    Route::post('/admin/program/indikator/target-satuan-rp-realisasi', 'Admin\ProgramIndikatorController@store_program_target_satuan_rp_realisasi')->name('admin.program.indikator.target-satuan-rp-realisasi');
    Route::post('/admin/program/indikator/target-satuan-rp-realisasi/update', 'Admin\ProgramIndikatorController@update_program_target_satuan_rp_realisasi')->name('admin.program.indikator.target-satuan-rp-realisasi_update');

    //Kegiatan
    Route::get('/admin/kegiatan', 'Admin\KegiatanController@index')->name('admin.kegiatan.index');
    Route::get('/admin/kegiatan/detail/{id}/{tahun}', 'Admin\KegiatanController@show');
    Route::post('/admin/kegiatan/get-program', 'Admin\KegiatanController@get_program')->name('admin.kegiatan.get-program');
    Route::post('/admin/kegiatan','Admin\KegiatanController@store')->name('admin.kegiatan.store');
    Route::get('/admin/kegiatan/edit/{id}/{tahun}','Admin\KegiatanController@edit');
    Route::post('/admin/kegiatan/update','Admin\KegiatanController@update')->name('admin.kegiatan.update');
    Route::get('/admin/kegiatan/destroy/{id}','Admin\KegiatanController@destroy');
    Route::post('/admin/kegiatan/hapus','Admin\KegiatanController@hapus')->name('admin.kegiatan.hapus');
    Route::post('/admin/kegiatan/destroy/impor', 'Admin\KegiatanController@impor')->name('admin.kegiatan.impor');
    Route::post('/admin/kegiatan/indikator-kinerja/tambah', 'Admin\KegiatanController@indikator_kinerja_tambah')->name('admin.kegiatan.indikator-kinerja.tambah');
    Route::post('/admin/kegiatan/indikator-kinerja/hapus', 'Admin\KegiatanController@indikator_kinerja_hapus')->name('admin.kegiatan.indikator-kinerja.hapus');

    //Kegiatan - Indikator
    Route::get('/admin/kegiatan/{kegiatan_id}/indikator', 'Admin\KegiatanIndikatorController@index');
    Route::get('/admin/kegiatan/indikator/detail/{id}', 'Admin\KegiatanIndikatorController@show');
    Route::post('/admin/kegiatan/indikator','Admin\KegiatanIndikatorController@store');
    Route::get('/admin/kegiatan/indikator/edit/{id}','Admin\KegiatanIndikatorController@edit');
    Route::post('/admin/kegiatan/indikator/update','Admin\KegiatanIndikatorController@update');
    Route::get('/admin/kegiatan/indikator/destroy/{id}','Admin\KegiatanIndikatorController@destroy');
    Route::post('/admin/kegiatan/indikator/impor', 'Admin\KegiatanIndikatorController@impor')->name('admin.kegiatan.indikator.impor');

    //Sub Kegiatan
    Route::get('/admin/sub-kegiatan', 'Admin\SubKegiatanController@index')->name('admin.sub-kegiatan.index');
    Route::get('/admin/sub-kegiatan/detail/{id}', 'Admin\SubKegiatanController@show');
    Route::post('/admin/sub-kegiatan/get-program', 'Admin\SubKegiatanController@get_program')->name('admin.sub-kegiatan.get-program');
    Route::post('/admin/sub-kegiatan/get-kegiatan', 'Admin\SubKegiatanController@get_kegiatan')->name('admin.sub-kegiatan.get-kegiatan');
    Route::post('/admin/sub-kegiatan','Admin\SubKegiatanController@store')->name('admin.sub-kegiatan.store');
    Route::get('/admin/sub-kegiatan/edit/{id}','Admin\SubKegiatanController@edit');
    Route::post('/admin/sub-kegiatan/update','Admin\SubKegiatanController@update')->name('admin.sub-kegiatan.update');
    Route::get('/admin/sub-kegiatan/destroy/{id}','Admin\SubKegiatanController@destroy');
    Route::post('/admin/sub-kegiatan/impor', 'Admin\SubKegiatanController@impor')->name('admin.sub-kegiatan.impor');
    Route::post('/admin/sub-kegiatan/indikator-kinerja/tambah', 'Admin\SubKegiatanController@indikator_kinerja_tambah')->name('admin.sub-kegiatan.indikator-kinerja.tambah');
    Route::post('/admin/sub-kegiatan/indikator-kinerja/hapus', 'Admin\SubKegiatanController@indikator_kinerja_hapus')->name('admin.sub-kegiatan.indikator-kinerja.hapus');

    //Sub Kegiatan - Indikator
    Route::get('/admin/sub-kegiatan/{sub_kegiatan_id}/indikator', 'Admin\SubKegiatanIndikatorController@index');
    Route::get('/admin/sub-kegiatan/indikator/detail/{id}', 'Admin\SubKegiatanIndikatorController@show');
    Route::post('/admin/sub-kegiatan/indikator','Admin\SubKegiatanIndikatorController@store');
    Route::get('/admin/sub-kegiatan/indikator/edit/{id}','Admin\SubKegiatanIndikatorController@edit');
    Route::post('/admin/sub-kegiatan/indikator/update','Admin\SubKegiatanIndikatorController@update');
    Route::get('/admin/sub-kegiatan/indikator/destroy/{id}','Admin\SubKegiatanIndikatorController@destroy');
    Route::post('/admin/sub-kegiatan/indikator/impor', 'Admin\SubKegiatanIndikatorController@impor')->name('admin.sub-kegiatan.indikator.impor');

    //Visi
    Route::get('/admin/visi', 'Admin\VisiController@index')->name('admin.visi.index');
    Route::get('/admin/visi/get-visi/{tahun}', 'Admin\VisiController@get_visi');
    Route::get('/admin/visi/detail/{id}', 'Admin\VisiController@show');
    Route::post('/admin/visi','Admin\VisiController@store')->name('admin.visi.store');
    Route::get('/admin/visi/edit/{id}','Admin\VisiController@edit');
    Route::post('/admin/visi/update','Admin\VisiController@update')->name('admin.visi.update');
    Route::get('/admin/visi/destroy/{id}','Admin\VisiController@destroy');

    //Misi
    Route::get('/admin/misi', 'Admin\MisiController@index')->name('admin.misi.index');
    Route::get('/admin/misi/detail/{id}/{tahun}', 'Admin\MisiController@show');
    Route::post('/admin/misi','Admin\MisiController@store')->name('admin.misi.store');
    Route::get('/admin/misi/edit/{id}/{tahun}','Admin\MisiController@edit');
    Route::post('/admin/misi/update','Admin\MisiController@update')->name('admin.misi.update');
    Route::get('/admin/misi/destroy/{id}','Admin\MisiController@destroy');

    //Tujuan
    Route::get('/admin/tujuan', 'Admin\TujuanController@index')->name('admin.tujuan.index');
    Route::get('/admin/tujuan/detail/{id}', 'Admin\TujuanController@show');
    Route::post('/admin/tujuan/get-misi', 'Admin\TujuanController@get_misi')->name('admin.tujuan.get-misi');
    Route::post('/admin/tujuan','Admin\TujuanController@store')->name('admin.tujuan.store');
    Route::get('/admin/tujuan/edit/{id}','Admin\TujuanController@edit');
    Route::post('/admin/tujuan/update','Admin\TujuanController@update')->name('admin.tujuan.update');
    Route::get('/admin/tujuan/destroy/{id}','Admin\TujuanController@destroy');
    Route::post('/admin/tujuan/destroy/impor', 'Admin\TujuanController@impor')->name('admin.tujuan.impor');
    Route::post('/admin/tujuan/indikator-kinerja/tambah', 'Admin\TujuanController@indikator_kinerja_tambah')->name('admin.tujuan.indikator-kinerja.tambah');
    Route::get('/admin/tujuan/indikator-kinerja/edit/{id}', 'Admin\TujuanController@indikator_kinerja_edit');
    Route::post('/admin/tujuan/indikator-kinerja/update', 'Admin\TujuanController@indikator_kinerja_update')->name('admin.tujuan.indikator-kinerja.update');
    Route::post('/admin/tujuan/hapus', 'Admin\TujuanController@hapus')->name('admin.tujuan.hapus');
    Route::post('/admin/tujuan/indikator-kinerja/hapus', 'Admin\TujuanController@indikator_kinerja_hapus')->name('admin.tujuan.indikator-kinerja.hapus');
    Route::post('/admin/tujuan/indikator-kinerja/target-satuan-rp-realisasi', 'Admin\TujuanController@store_tujuan_target_satuan_rp_realisasi')->name('admin.tujuan.indikator.target-satuan-rp-realisasi');
    Route::post('/admin/tujuan/indikator-kinerja/target-satuan-rp-realisasi/update', 'Admin\TujuanController@update_tujuan_target_satuan_rp_realisasi')->name('admin.tujuan.indikator.target-satuan-rp-realisasi_update');

    //Tujuan - Indikator
    Route::get('/admin/tujuan/{tujuan_id}/indikator', 'Admin\TujuanIndikatorController@index');
    Route::get('/admin/tujuan/indikator/detail/{id}', 'Admin\TujuanIndikatorController@show');
    Route::post('/admin/tujuan/indikator','Admin\TujuanIndikatorController@store');
    Route::get('/admin/tujuan/indikator/edit/{id}','Admin\TujuanIndikatorController@edit');
    Route::post('/admin/tujuan/indikator/update','Admin\TujuanIndikatorController@update');
    Route::get('/admin/tujuan/indikator/destroy/{id}','Admin\TujuanIndikatorController@destroy');
    Route::post('/admin/tujuan/indikator/impor', 'Admin\TujuanIndikatorController@impor')->name('admin.tujuan.indikator.impor');

    //Sasaran
    Route::get('/admin/sasaran', 'Admin\SasaranController@index')->name('admin.sasaran.index');
    Route::get('/admin/sasaran/detail/{id}', 'Admin\SasaranController@show');
    Route::post('/admin/sasaran/get-misi', 'Admin\SasaranController@get_misi')->name('admin.sasaran.get-misi');
    Route::post('/admin/sasaran/get-tujuan', 'Admin\SasaranController@get_tujuan')->name('admin.sasaran.get-tujuan');
    Route::post('/admin/sasaran','Admin\SasaranController@store')->name('admin.sasaran.store');
    Route::get('/admin/sasaran/edit/{id}','Admin\SasaranController@edit');
    Route::post('/admin/sasaran/update','Admin\SasaranController@update')->name('admin.sasaran.update');
    Route::get('/admin/sasaran/destroy/{id}','Admin\SasaranController@destroy');
    Route::post('/admin/sasaran/destroy/impor', 'Admin\SasaranController@impor')->name('admin.sasaran.impor');
    Route::post('/admin/sasaran/indikator-kinerja/tambah', 'Admin\SasaranController@indikator_kinerja_tambah')->name('admin.sasaran.indikator-kinerja.tambah');
    Route::get('/admin/sasaran/indikator-kinerja/edit/{id}', 'Admin\SasaranController@indikator_kinerja_edit');
    Route::post('/admin/sasaran/indikator-kinerja/update', 'Admin\SasaranController@indikator_kinerja_update')->name('admin.sasaran.indikator-kinerja.update');
    Route::post('/admin/sasaran/hapus', 'Admin\SasaranController@sasaran_hapus')->name('admin.sasaran.hapus');
    Route::post('/admin/sasaran/indikator-kinerja/hapus', 'Admin\SasaranController@indikator_kinerja_hapus')->name('admin.sasaran.indikator-kinerja.hapus');
    Route::post('/admin/sasaran/indikator-kinerja/target-satuan-rp-realisasi', 'Admin\SasaranController@store_sasaran_target_satuan_rp_realisasi')->name('admin.sasaran.indikator.target-satuan-rp-realisasi');
    Route::post('/admin/sasaran/indikator-kinerja/target-satuan-rp-realisasi/update', 'Admin\SasaranController@update_sasaran_target_satuan_rp_realisasi')->name('admin.sasaran.indikator.target-satuan-rp-realisasi_update');

    // Sasaran Indikator
    Route::post('/admin/sasaran/indikator', 'Admin\SasaranController@sasaran_indikator_store')->name('admin.sasaran.indikator.store');
    Route::get('/admin/sasaran/indikator/edit/{id}', 'Admin\SasaranController@sasaran_indikator_edit');
    Route::post('/admin/sasaran/indikator/update', 'Admin\SasaranController@sasaran_indikator_update')->name('admin.sasaran.indikator.update');

    // //Sasaran - Indikator
    // Route::get('/admin/sasaran/{sasaran_id}/indikator', 'Admin\SasaranIndikatorController@index');
    // Route::get('/admin/sasaran/indikator/detail/{id}', 'Admin\SasaranIndikatorController@show');
    // Route::post('/admin/sasaran/indikator','Admin\SasaranIndikatorController@store');
    // Route::get('/admin/sasaran/indikator/edit/{id}','Admin\SasaranIndikatorController@edit');
    // Route::post('/admin/sasaran/indikator/update','Admin\SasaranIndikatorController@update');
    // Route::get('/admin/sasaran/indikator/destroy/{id}','Admin\SasaranIndikatorController@destroy');
    // Route::post('/admin/sasaran/indikator/impor', 'Admin\SasaranIndikatorController@impor')->name('admin.sasaran.indikator.impor');

    //Program RPJMD
    Route::get('/admin/program-rpjmd', 'Admin\ProgramRpjmdController@index')->name('admin.program-rpjmd.index');
    Route::get('/admin/program-rpjmd/get-sasaran/{id}', 'Admin\ProgramRpjmdController@get_sasaran');
    Route::post('/admin/program-rpjmd','Admin\ProgramRpjmdController@store')->name('admin.program-rpjmd.store');
    Route::get('/admin/program-rpjmd/edit/{id}/{sasaran_indikator_id}','Admin\ProgramRpjmdController@edit');
    Route::get('/admin/program-rpjmd/destroy/{id}','Admin\ProgramRpjmdController@destroy');
    Route::post('/admin/program-rpjmd/get-program', 'Admin\ProgramRpjmdController@get_program')->name('admin.program-rpjmd.get-program');
    Route::post('/admin/program-rpjmd/get-tujuan', 'Admin\ProgramRpjmdController@get_tujuan')->name('admin.program-rpjmd.get-tujuan');
    Route::post('/admin/program-rpjmd/get-sasaran', 'Admin\ProgramRpjmdController@get_sasaran')->name('admin.program-rpjmd.get-sasaran');
    Route::post('/admin/program-rpjmd/get-sasaran-indikator', 'Admin\ProgramRpjmdController@get_sasaran_indikator')->name('admin.program-rpjmd.get-sasaran-indikator');
    Route::get('/admin/program-rpjmd/detail/{id}','Admin\ProgramRpjmdController@detail');
    Route::post('/admin/program-rpjmd/detail/target-rp-pertahun', 'Admin\ProgramRpjmdController@store_target_rp_pertahun')->name('admin.program-rpjmd.detail.target-rp-pertahun');
    Route::get('/admin/program-rpjmd/edit/{id}','Admin\ProgramRpjmdController@edit');
    Route::post('/admin/program-rpjmd/update','Admin\ProgramRpjmdController@update')->name('admin.program-rpjmd.update');
    Route::post('/admin/program-rpjmd/pivot-sasaran-indikator-program-rpmjd/delete','Admin\ProgramRpjmdController@pivot_sasaran_indikator_program_rpjmd_delete')->name('admin.program-rpjmd.pivot-sasaran-indikator-program-rpmjd.delete');

    //Tahun Periode
    Route::get('/admin/tahun-periode', 'Admin\TahunPeriodeController@index')->name('admin.tahun-periode.index');
    Route::post('/admin/tahun-periode','Admin\TahunPeriodeController@store')->name('admin.tahun-periode.store');
    Route::get('/admin/tahun-periode/edit/{id}','Admin\TahunPeriodeController@edit');
    Route::post('/admin/tahun-periode/update','Admin\TahunPeriodeController@update')->name('admin.tahun-periode.update');
    Route::get('/admin/tahun-periode/destroy/{id}','Admin\TahunPeriodeController@destroy');

    //RKPD
    Route::get('/admin/rpkd', 'Admin\RkpdController@index')->name('admin.rkpd.index');

    //Renstra
    Route::post('/admin/renstra/get-kegiatan', 'Admin\RenstraKegiatanController@get_kegiatan')->name('admin.renstra.get-kegiatan');
    Route::post('/admin/renstra/get-opd', 'Admin\RenstraKegiatanController@get_opd')->name('admin.renstra.get-opd');
    Route::post('/admin/renstra/kegiatan', 'Admin\RenstraKegiatanController@store')->name('admin.renstra.kegiatan.store');
    Route::get('/admin/renstra/kegiatan/detail/{id}','Admin\RenstraKegiatanController@detail');
    Route::post('/admin/renstra/kegiatan/detail/target-rp-pertahun', 'Admin\RenstraKegiatanController@store_target_rp_pertahun')->name('admin.renstra.kegiatan.detail.target-rp-pertahun');

    // Normalisasi
    Route::prefix('admin')->group(function(){
        route::prefix('normalisasi')->group(function(){
            Route::get('/memberikan-id-tahun-periode', 'Admin\NormalisasiController@memberikan_id_tahun_periode');
        });
    });
});
