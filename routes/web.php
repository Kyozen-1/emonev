<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes(['register' => false, 'login' => false]);

Route::get('opd/login','Auth\OpdLoginController@showLoginForm')->name('opd.login');
Route::post('opd/login', 'Auth\OpdLoginController@login')->name('opd.login.submit');
Route::get('opd/logout', 'Auth\OpdLoginController@logout')->name('opd.logout');

Route::get('admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('admin/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

Route::get('/', 'Auth\OpdLoginController@showLoginForm');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
@include('admin.php');
@include('opd.php');
