<?php

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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['auth','admin']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::group(['prefix' => 'users'], function () {
        Route::get('/password', 'UserController@password')->name('users.password');
        Route::post('/password', 'UserController@changePassword')->name('users.changePassword');
    });
    Route::group(['prefix' => 'users','middleware' => 'admin'], function () {
        Route::get('/', 'UserController@index')->name('users.index');
        Route::get('/create', 'UserController@create')->name('users.create');
        Route::delete('/{id}', 'UserController@destroy')->name('users.destroy');
        Route::get('/active/{id}', 'UserController@active')->name('users.active');
        Route::post('/', 'UserController@store')->name('users.store');
    });
    Route::group(['prefix' => 'books','middleware' => 'admin','as'=>'books.'], function () {
        Route::get('/', 'BookController@index')->name('index');
        Route::get('/create', 'BookController@create')->name('create');
        Route::post('/store', 'BookController@store')->name('store');
        Route::get('/{id}/edit', 'BookController@edit')->name('edit');
        Route::put('/{id}/update', 'BookController@update')->name('update');
        Route::delete('/{id}/destroy', 'BookController@destroy')->name('destroy');
    });
    Route::group(['prefix' => 'volumes','middleware' => 'admin','as'=>'volumes.'], function () {
        Route::get('/', 'VolumeController@index')->name('index');
        Route::get('/create', 'VolumeController@create')->name('create');
        Route::post('/store', 'VolumeController@store')->name('store');
        Route::get('/{id}/edit', 'VolumeController@edit')->name('edit');
        Route::put('/{id}/update', 'VolumeController@update')->name('update');
        Route::delete('/{id}/destroy', 'VolumeController@destroy')->name('destroy');
    });
    Route::group(['prefix' => 'page','middleware' => 'admin'], function () {
        Route::post('/store', 'PageController@store')->name('store');
        Route::put('/{id}/update', 'PageController@update')->name('update');
        Route::delete('/{id}/destroy', 'PageController@destroy')->name('destroy');
    });
    Route::group(['prefix' => 'file-manager','middleware' => 'admin','as'=>'file-manager.'], function () {
        Route::get('/', 'FileManagerController@index')->name('index');
        Route::get('/refresh-dir', 'FileManagerController@refreshDir')->name('refreshDir');
    });
    Route::group(['prefix' => 'ajax'], function () {
        Route::post('ajaxGetUsers', 'UserController@ajaxGetUsers')->name('ajaxGetUsers');
        Route::get('ajaxGetFolder','FileManagerController@ajaxGetFolder')->name('ajaxGetFolder');
        Route::post('ajaxSaveFile', 'FileManagerController@ajaxSaveFile')->name('ajaxSaveFile');
        Route::post('ajaxGetBooks', 'BookController@ajaxGetBooks')->name('ajaxGetBooks');
        Route::post('ajaxGetVolumes', 'VolumeController@ajaxGetVolumes')->name('ajaxGetVolumes');
    });
});


//add middleware admin and change to this
Route::group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'users'], function () {
});

Route::group(['middleware' => 'guest', 'namespace' => 'App\Http\Controllers\Auth'], function () {
    Route::get('/login', 'LoginController@showLoginForm')->name('login');
    Route::post('/login', 'LoginController@login');
});

Route::group(['middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Auth'], function () {
    Route::post('/logout', 'LoginController@logout')->name('logout');
});