<?php
Route::group(['middleware' => 'auth:admin'], function(){
    Route::get('/admin/dashboard', 'Admin\DashboardController@index')->name('admin.dashboard.index');
    Route::post('/admin/dashboard/change', 'Admin\DashboardController@change')->name('admin.dashboard.change');

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

    //Manajemen Akun Opd
    Route::get('/admin/manajemen-akun/opd', 'Admin\ManajemenAkun\OpdController@index')->name('admin.manajemen-akun.opd.index');
    Route::get('/admin/manajemen-akun/opd/detail/{id}', 'Admin\ManajemenAkun\OpdController@show');
    Route::post('/admin/manajemen-akun/opd','Admin\ManajemenAkun\OpdController@store')->name('admin.manajemen-akun.opd.store');
    Route::post('/admin/manajemen-akun/opd/change-password','Admin\ManajemenAkun\OpdController@change_password')->name('admin.manajemen-akun.opd.change-password');
    Route::post('/admin/manajemen-akun/opd/destroy','Admin\ManajemenAkun\OpdController@destroy')->name('admin.manajemen-akun.opd.destroy');

    //Nomenklatur
    //Urusan
    Route::get('/admin/urusan', 'Admin\UrusanController@index')->name('admin.urusan.index');
    Route::get('/admin/urusan/detail/{id}', 'Admin\UrusanController@show');
    Route::post('/admin/urusan','Admin\UrusanController@store')->name('admin.urusan.store');
    Route::get('/admin/urusan/edit/{id}','Admin\UrusanController@edit');
    Route::post('/admin/urusan/update','Admin\UrusanController@update')->name('admin.urusan.update');
    Route::get('/admin/urusan/destroy/{id}','Admin\UrusanController@destroy');
    Route::post('/admin/urusan/destroy/impor', 'Admin\UrusanController@impor')->name('admin.urusan.impor');

    //Program
    Route::get('/admin/program', 'Admin\ProgramController@index')->name('admin.program.index');
    Route::get('/admin/program/detail/{id}', 'Admin\ProgramController@show');
    Route::post('/admin/program','Admin\ProgramController@store')->name('admin.program.store');
    Route::get('/admin/program/edit/{id}','Admin\ProgramController@edit');
    Route::post('/admin/program/update','Admin\ProgramController@update')->name('admin.program.update');
    Route::get('/admin/program/destroy/{id}','Admin\ProgramController@destroy');
    Route::post('/admin/program/destroy/impor', 'Admin\ProgramController@impor')->name('admin.program.impor');

    //Program - Indikator
    Route::get('/admin/program/{program_id}/indikator', 'Admin\ProgramIndikatorController@index');
    Route::get('/admin/program/indikator/detail/{id}', 'Admin\ProgramIndikatorController@show');
    Route::post('/admin/program/indikator','Admin\ProgramIndikatorController@store');
    Route::get('/admin/program/indikator/edit/{id}','Admin\ProgramIndikatorController@edit');
    Route::post('/admin/program/indikator/update','Admin\ProgramIndikatorController@update');
    Route::get('/admin/program/indikator/destroy/{id}','Admin\ProgramIndikatorController@destroy');
    Route::post('/admin/program/indikator/impor', 'Admin\ProgramIndikatorController@impor')->name('admin.program.indikator.impor');

    //Kegiatan
    Route::get('/admin/kegiatan', 'Admin\KegiatanController@index')->name('admin.kegiatan.index');
    Route::get('/admin/kegiatan/detail/{id}', 'Admin\KegiatanController@show');
    Route::post('/admin/kegiatan/get-program', 'Admin\KegiatanController@get_program')->name('admin.kegiatan.get-program');
    Route::post('/admin/kegiatan','Admin\KegiatanController@store')->name('admin.kegiatan.store');
    Route::get('/admin/kegiatan/edit/{id}','Admin\KegiatanController@edit');
    Route::post('/admin/kegiatan/update','Admin\KegiatanController@update')->name('admin.kegiatan.update');
    Route::get('/admin/kegiatan/destroy/{id}','Admin\KegiatanController@destroy');
    Route::post('/admin/kegiatan/destroy/impor', 'Admin\KegiatanController@impor')->name('admin.kegiatan.impor');

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
    Route::post('/admin/sub-kegiatan/destroy/impor', 'Admin\SubKegiatanController@impor')->name('admin.sub-kegiatan.impor');

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
    Route::get('/admin/visi/detail/{id}', 'Admin\VisiController@show');
    Route::post('/admin/visi','Admin\VisiController@store')->name('admin.visi.store');
    Route::get('/admin/visi/edit/{id}','Admin\VisiController@edit');
    Route::post('/admin/visi/update','Admin\VisiController@update')->name('admin.visi.update');
    Route::get('/admin/visi/destroy/{id}','Admin\VisiController@destroy');
});
