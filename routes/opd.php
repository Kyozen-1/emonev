<?php
Route::group(['middleware' => 'auth:opd'], function(){
    Route::get('/opd/dashboard', 'Opd\DashboardController@index')->name('opd.dashboard.index');
    Route::post('/opd/dashboard/change', 'Opd\DashboardController@change')->name('opd.dashboard.change');

    Route::get('/opd/renstra', 'Opd\RenstraController@index')->name('opd.renstra.index');
    Route::post('/opd/renstra/option/kegiatan', 'Opd\RenstraController@option_kegiatan')->name('opd.renstra.option.kegiatan');
    Route::post('/opd/renstra/kegiatan/store', 'Opd\RenstraController@store')->name('opd.renstra.kegiatan.store');
    Route::get('/opd/renstra/kegiatan/detail/{id}', 'Opd\RenstraController@detail');
    Route::post('/opd/renstra/kegiatan/detail/target-rp-pertahun', 'Opd\RenstraController@store_target_rp_pertahun')->name('opd.renstra.kegiatan.detail.target-rp-pertahun');
    Route::get('/opd/renstra/get-misi', 'Opd\RenstraController@get_misi')->name('opd.renstra.get-misi');
    Route::get('/opd/renstra/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.get-tujuan');
    Route::get('/opd/renstra/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.get-sasaran');
    Route::get('/opd/renstra/get-program', 'Opd\RenstraController@get_program')->name('opd.renstra.get-program');
    Route::get('/opd/renstra/get-kegiatan', 'Opd\RenstraController@get_kegiatan')->name('opd.renstra.get-kegiatan');
    Route::post('/opd/renstra/filter-get-misi', 'Opd\RenstraController@filter_get_misi')->name('opd.renstra.filter-get-misi');
    Route::post('/opd/renstra/filter-get-tujuan', 'Opd\RenstraController@filter_get_tujuan')->name('opd.renstra.filter-get-tujuan');
    Route::post('/opd/renstra/filter-get-sasaran', 'Opd\RenstraController@filter_get_sasaran')->name('opd.renstra.filter-get-sasaran');
    Route::post('/opd/renstra/filter-get-program', 'Opd\RenstraController@filter_get_program')->name('opd.renstra.filter-get-program');
    Route::post('/opd/renstra/filter-get-kegiatan', 'Opd\RenstraController@filter_get_kegiatan')->name('opd.renstra.filter-get-kegiatan');
    Route::post('/opd/renstra/filter/get-misi', 'Opd\RenstraController@get_filter_misi')->name('opd.renstra.filter.get-misi');
    Route::post('/opd/renstra/reset/get-misi', 'Opd\RenstraController@get_misi')->name('opd.renstra.reset.get-misi');
    Route::post('/opd/renstra/filter/get-tujuan', 'Opd\RenstraController@get_filter_tujuan')->name('opd.renstra.filter.get-tujuan');
    Route::post('/opd/renstra/reset/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.reset.get-tujuan');
    Route::post('/opd/renstra/filter/get-sasaran', 'Opd\RenstraController@get_filter_sasaran')->name('opd.renstra.filter.get-sasaran');
    Route::post('/opd/renstra/reset/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.reset.get-sasaran');
    Route::post('/opd/renstra/filter/get-program', 'Opd\RenstraController@get_filter_program')->name('opd.renstra.filter.get-program');
    Route::post('/opd/renstra/reset/get-program', 'Opd\RenstraController@get_program')->name('opd.renstra.reset.get-program');
    Route::post('/opd/renstra/filter/get-kegiatan', 'Opd\RenstraController@get_filter_kegiatan')->name('opd.renstra.filter.get-kegiatan');
    Route::post('/opd/renstra/reset/get-kegiatan', 'Opd\RenstraController@get_kegiatan')->name('opd.renstra.reset.get-kegiatan');
    // Route::post('/opd/renstra/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.get-tujuan');
    // Route::post('/opd/renstra/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.get-sasaran');
    // Route::post('/opd/renstra/get-program-rpjmd', 'Opd\RenstraController@get_program_rpjmd')->name('opd.renstra.get-program-rpjmd');
    // Route::post('/opd/renstra/tambah-item-renstra', 'Opd\RenstraController@tambah_item_renstra')->name('opd.renstra.tambah-item-renstra');
    // Route::post('/opd/renstra/target-rp-pertahun-tujuan/tambah', 'Opd\RenstraController@tambah_target_rp_pertahun_tujuan')->name('opd.renstra.target-rp-pertahun-tujuan.tambah');
    // Route::get('/opd/renstra/target-rp-pertahun-tujuan/edit/{id}', 'Opd\RenstraController@edit_target_rp_pertahun_tujuan');
    // Route::post('/opd/renstra/target-rp-pertahun-tujuan/update', 'Opd\RenstraController@update_target_rp_pertahun_tujuan')->name('opd.renstra.target-rp-pertahun-tujuan.update');
    // Route::post('/opd/renstra/target-rp-pertahun-sasaran/tambah', 'Opd\RenstraController@tambah_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.tambah');
    // Route::get('/opd/renstra/target-rp-pertahun-sasaran/edit/{id}', 'Opd\RenstraController@edit_target_rp_pertahun_sasaran');
    // Route::post('/opd/renstra/target-rp-pertahun-sasaran/update', 'Opd\RenstraController@update_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.update');
    // Route::post('/opd/renstra/target-rp-pertahun-program/tambah', 'Opd\RenstraController@tambah_target_rp_pertahun_program')->name('opd.renstra.target-rp-pertahun-program.tambah');
    // Route::get('/opd/renstra/target-rp-pertahun-program/edit/{id}', 'Opd\RenstraController@edit_target_rp_pertahun_program');
    // Route::post('/opd/renstra/target-rp-pertahun-program/update', 'Opd\RenstraController@update_target_rp_pertahun_program')->name('opd.renstra.target-rp-pertahun-program.update');

    Route::get('/opd/renja', 'Opd\RenjaController@index')->name('opd.renja.index');

    Route::get('/opd/laporan', 'Opd\LaporanController@index')->name('opd.laporan.index');

    Route::post('/opd/renstra/tujuan-pd/tambah', 'Opd\TujuanPdController@tambah')->name('opd.renstra.tujuan-pd.tambah');
    Route::get('/opd/renstra/tujuan-pd/edit/{id}', 'Opd\TujuanPdController@edit');
    Route::post('/opd/renstra/tujuan-pd/update', 'Opd\TujuanPdController@update')->name('opd.renstra.tujuan-pd.update');
    Route::post('/opd/renstra/tujuan-pd/hapus', 'Opd\TujuanPdController@hapus')->name('opd.renstra.tujuan-pd.hapus');
    Route::post('/opd/renstra/tujuan-pd/indikator-kinerja/tambah', 'Opd\TujuanPdController@indikator_kinerja_tambah')->name('opd.renstra.tujuan-pd.indikator-kinerja.tambah');
    Route::post('/opd/renstra/tujuan-pd/indikator-kinerja/hapus', 'Opd\TujuanPdController@indikator_kinerja_hapus')->name('opd.renstra.tujuan-pd.indikator-kinerja.hapus');
    Route::post('/opd/renstra/tujuan-pd/indikator-kinerja/target-satuan-realisasi/tambah', 'Opd\TujuanPdController@target_satuan_realisasi_tambah')->name('opd.renstra.tujuan-pd.indikator-kinerja.target-satuan-realisasi.tambah');
    Route::post('/opd/renstra/tujuan-pd/indikator-kinerja/target-satuan-realisasi/ubah', 'Opd\TujuanPdController@target_satuan_realisasi_ubah')->name('opd.renstra.tujuan-pd.indikator-kinerja.target-satuan-realisasi.ubah');

    Route::post('/opd/renstra/sasaran-pd/tambah', 'Opd\SasaranPdController@tambah')->name('opd.renstra.sasaran-pd.tambah');
    Route::get('/opd/renstra/sasaran-pd/edit/{id}', 'Opd\SasaranPdController@edit');
    Route::post('/opd/renstra/sasaran-pd/update', 'Opd\SasaranPdController@update')->name('opd.renstra.sasaran-pd.update');
    Route::post('/opd/renstra/sasaran-pd/hapus', 'Opd\SasaranPdController@hapus')->name('opd.renstra.sasaran-pd.hapus');
    Route::post('/opd/renstra/sasaran-pd/indikator-kinerja/tambah', 'Opd\SasaranPdController@indikator_kinerja_tambah')->name('opd.renstra.sasaran-pd.indikator-kinerja.tambah');
    Route::post('/opd/renstra/sasaran-pd/indikator-kinerja/hapus', 'Opd\SasaranPdController@indikator_kinerja_hapus')->name('opd.renstra.sasaran-pd.indikator-kinerja.hapus');
    Route::post('/opd/renstra/sasaran-pd/indikator-kinerja/target-satuan-realisasi/tambah', 'Opd\SasaranPdController@target_satuan_realisasi_tambah')->name('opd.renstra.sasaran-pd.indikator-kinerja.target-satuan-realisasi.tambah');
    Route::post('/opd/renstra/sasaran-pd/indikator-kinerja/target-satuan-realisasi/ubah', 'Opd\SasaranPdController@target_satuan_realisasi_ubah')->name('opd.renstra.sasaran-pd.indikator-kinerja.target-satuan-realisasi.ubah');
});
