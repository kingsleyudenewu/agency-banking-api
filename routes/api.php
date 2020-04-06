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

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {


    /**
     * Auth endpoints
     */
    Route::group(['prefix' => 'auth', 'namespace' => 'Api\Auth', 'as' => 'auth.'], function () {

        Route::post('/login', 'LoginController@postLogin')->name('login.post');

    });







});
