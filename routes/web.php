<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', 'IndexController@index')->name('index');

// validate access token:
Route::get('/token', 'TokenController@index')->name('token.index');
Route::get('/token/validate', 'TokenController@doValidate')->name('token.validate');

// start import thing.
Route::get('/import/start', ['uses' => 'Import\StartController@index', 'as' => 'import.start']);
Route::post('/import/upload', ['uses' => 'Import\UploadController@upload', 'as' => 'import.upload']);
