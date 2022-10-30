<?php
Route::group(['middleware' => 'auth:opd'], function(){
    Route::get('/opd/dashboard', 'Opd\DashboardController@index')->name('opd.dashboard.index');
    Route::post('/opd/dashboard/change', 'Opd\DashboardController@change')->name('opd.dashboard.change');

    Route::get('/opd/renstra', 'Opd\RenstraController@index')->name('opd.renstra.index');
    Route::get('/opd/renstra/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.get-tujuan');
    Route::get('/opd/renstra/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.get-sasaran');
    Route::get('/opd/renstra/get-program', 'Opd\RenstraController@get_program')->name('opd.renstra.get-program');
    Route::get('/opd/renstra/get-kegiatan', 'Opd\RenstraController@get_kegiatan')->name('opd.renstra.get-kegiatan');
    Route::post('/opd/renstra/filter-get-misi', 'Opd\RenstraController@filter_get_misi')->name('opd.renstra.filter-get-misi');
    Route::post('/opd/renstra/filter-get-tujuan', 'Opd\RenstraController@filter_get_tujuan')->name('opd.renstra.filter-get-tujuan');
    Route::post('/opd/renstra/filter-get-sasaran', 'Opd\RenstraController@filter_get_sasaran')->name('opd.renstra.filter-get-sasaran');
    Route::post('/opd/renstra/filter-get-program', 'Opd\RenstraController@filter_get_program')->name('opd.renstra.filter-get-program');
    Route::post('/opd/renstra/filter/get-tujuan', 'Opd\RenstraController@get_filter_tujuan')->name('opd.renstra.filter.get-tujuan');
    Route::post('/opd/renstra/reset/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.reset.get-tujuan');
    Route::post('/opd/renstra/filter/get-sasaran', 'Opd\RenstraController@get_filter_sasaran')->name('opd.renstra.filter.get-sasaran');
    Route::post('/opd/renstra/reset/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.reset.get-sasaran');
    Route::post('/opd/renstra/filter/get-program', 'Opd\RenstraController@get_filter_program')->name('opd.renstra.filter.get-program');
    Route::post('/opd/renstra/reset/get-program', 'Opd\RenstraController@get_program')->name('opd.renstra.reset.get-program');
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
});
