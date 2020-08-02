<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('gclogs', 'GclogController@index')->middleware('auth:sanctum');

Route::get('/parsegclogs', 'GclogController@parseGclogs')->middleware('auth:sanctum');

Route::post('upload', 'UploadController')->middleware('auth:sanctum');

Route::post('login', 'Auth\LoginController@login')->name('login.api');
Route::post('logout', 'Auth\LoginController@logout')->name('logout.api');