<?php
Route::group(['middleware' => 'auth:opd'], function(){
    Route::get('/opd/dashboard', 'Opd\DashboardController@index')->name('opd.dashboard.index');
    Route::post('/opd/dashboard/change', 'Opd\DashboardController@change')->name('opd.dashboard.change');

    Route::get('/opd/renstra', 'Opd\RenstraController@index')->name('opd.renstra.index');
    Route::post('/opd/renstra/option/kegiatan', 'Opd\RenstraController@option_kegiatan')->name('opd.renstra.option.kegiatan');
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

    Route::get('/opd/renja', 'Opd\RenjaController@index')->name('opd.renja.index');
    Route::get('/opd/renja/get-program', 'Opd\RenjaController@get_program')->name('opd.renja.get_program');
    Route::get('/opd/renja/get-kegiatan', 'Opd\RenjaController@get_kegiatan')->name('opd.renja.get_kegiatan');
    Route::get('/opd/renja/get-sub-kegiatan', 'Opd\RenjaController@get_sub_kegiatan')->name('opd.renja.get-sub-kegiatan');
    // Renja Sub Kegiatan
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/tambah', 'Opd\Renja\SubKegiatanController@indikator_kinerja_tambah')->name('opd.renja.sub-kegiatan.indikator-kinerja.tambah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/hapus', 'Opd\Renja\SubKegiatanController@indikator_kinerja_hapus')->name('opd.renja.sub-kegiatan.indikator-kinerja.hapus');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/target-satuan-realisasi/tambah', 'Opd\Renja\SubKegiatanController@target_satuan_realisasi_tambah')->name('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.tambah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/target-satuan-realisasi/ubah', 'Opd\Renja\SubKegiatanController@target_satuan_realisasi_ubah')->name('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.ubah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/target-satuan-realisasi/tambah', 'Opd\Renja\SubKegiatanController@target_satuan_realisasi_tambah')->name('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.tambah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/target-satuan-realisasi/ubah', 'Opd\Renja\SubKegiatanController@target_satuan_realisasi_ubah')->name('opd.renja.sub-kegiatan.indikator-kinerja.target-satuan-realisasi.ubah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/tw/tambah', 'Opd\Renja\SubKegiatanController@tw_tambah')->name('opd.renja.sub-kegiatan.indikator-kinerja.tw.tambah');
    Route::post('/opd/renja/sub-kegiatan/indikator-kinerja/tw/ubah', 'Opd\Renja\SubKegiatanController@tw_ubah')->name('opd.renja.sub-kegiatan.indikator-kinerja.tw.ubah');
    // Atur TW
    Route::post('/opd/renja/program/tw/tambah', 'Opd\Renja\ProgramTwController@tambah')->name('opd.renja.program.tw.tambah');
    Route::post('/opd/renja/program/tw/ubah', 'Opd\Renja\ProgramTwController@ubah')->name('opd.renja.program.tw.ubah');
    Route::post('/opd/renja/kegiatan/tw/tambah', 'Opd\Renja\KegiatanTwController@tambah')->name('opd.renja.kegiatan.tw.tambah');
    Route::post('/opd/renja/kegiatan/tw/ubah', 'Opd\Renja\KegiatanTwController@ubah')->name('opd.renja.kegiatan.tw.ubah');

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

    Route::post('/opd/program/indikator/target-satuan-rp-realisasi/update', 'Opd\ProgramIndikatorController@update_program_target_satuan_rp_realisasi')->name('opd.program.indikator.target-satuan-rp-realisasi.update');

    Route::post('/opd/renstra/kegiatan/indikator-kinerja/tambah', 'Opd\KegiatanController@indikator_kinerja_tambah')->name('opd.renstra.kegiatan.indikator-kinerja.tambah');
    Route::post('/opd/renstra/kegiatan/indikator-kinerja/hapus', 'Opd\KegiatanController@indikator_kinerja_hapus')->name('opd.renstra.kegiatan.indikator-kinerja.hapus');
    Route::post('/opd/renstra/kegiatan/indikator-kinerja/target-satuan-realisasi/tambah', 'Opd\KegiatanController@target_satuan_realisasi_tambah')->name('opd.renstra.kegiatan.indikator-kinerja.target-satuan-realisasi.tambah');
    Route::post('/opd/renstra/kegiatan/indikator-kinerja/target-satuan-realisasi/ubah', 'Opd\KegiatanController@target_satuan_realisasi_ubah')->name('opd.renstra.kegiatan.indikator-kinerja.target-satuan-realisasi.ubah');
});
