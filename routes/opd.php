<?php
Route::group(['middleware' => 'auth:opd'], function(){
    Route::get('/opd/dashboard', 'Opd\DashboardController@index')->name('opd.dashboard.index');
    Route::post('/opd/dashboard/change', 'Opd\DashboardController@change')->name('opd.dashboard.change');

    Route::get('/opd/renstra', 'Opd\RenstraController@index')->name('opd.renstra.index');
    Route::post('/opd/renstra/target-rp-pertahun-sasaran/tambah', 'Opd\RenstraController@tambah_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.tambah');
    Route::get('/opd/renstra/target-rp-pertahun-sasaran/edit/{id}', 'Opd\RenstraController@edit_target_rp_pertahun_sasaran');
    Route::post('/opd/renstra/target-rp-pertahun-sasaran/update', 'Opd\RenstraController@update_target_rp_pertahun_sasaran')->name('opd.renstra.target-rp-pertahun-sasaran.update');
});
