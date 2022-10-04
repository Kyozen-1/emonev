<?php
Route::group(['middleware' => 'auth:opd'], function(){
    Route::get('/opd/dashboard', 'Opd\DashboardController@index')->name('opd.dashboard.index');
    Route::post('/opd/dashboard/change', 'Opd\DashboardController@change')->name('opd.dashboard.change');

    Route::get('/opd/renstra', 'Opd\RenstraController@index')->name('opd.renstra.index');
    Route::post('/opd/renstra/get-tujuan', 'Opd\RenstraController@get_tujuan')->name('opd.renstra.get-tujuan');
    Route::post('/opd/renstra/get-sasaran', 'Opd\RenstraController@get_sasaran')->name('opd.renstra.get-sasaran');
    Route::post('/opd/renstra/get-program-rpjmd', 'Opd\RenstraController@get_program_rpjmd')->name('opd.renstra.get-program-rpjmd');


    Route::post('/opd/renstra/tambah-item-renstra', 'Opd\RenstraController@tambah_item_renstra')->name('opd.renstra.tambah-item-renstra');
    Route::post('/opd/renstra/target-rp-pertahun-sasaran/tambah', 'Opd\RenstraController@tambah_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.tambah');
    Route::get('/opd/renstra/target-rp-pertahun-sasaran/edit/{id}', 'Opd\RenstraController@edit_target_rp_pertahun_sasaran');
    Route::post('/opd/renstra/target-rp-pertahun-sasaran/update', 'Opd\RenstraController@update_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.update');
});
