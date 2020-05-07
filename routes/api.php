<?php

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


    Route::get('/', 'IsAlive@check');


    /**
     * Auth endpoints
     */
    Route::group(['prefix' => 'auth', 'namespace' => 'Api\Auth', 'as' => 'auth.'], function () {

        Route::post('/login', 'LoginController@postLogin')->name('login.post');
        Route::post('/login/otp', 'LoginOTPController@process')->name('login.post.top');

    });

    /**
     * Profile endpoint
     */
    Route::group(['prefix' => 'profile', 'namespace' => 'Api\Account', 'as' => 'profile.'], function () {

        Route::get('/', 'ProfileController@index')->name('get');

    });



    Route::group([
        'prefix' => 'agents',
        'namespace' => 'Api\Agent',
        'as' => 'agents.',
        'middleware' => ['auth:api']], function () {

        Route::post('/', 'CreateAgentController@store')->name('create-new');
        Route::post('/documents', 'DocumentManagement@upload')->name('document.upload');

    });



    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Api\Admin',
        'as' => 'admin.',
        'middleware' => ['auth:api', 'admin-check']], function () {


        Route::post('/password', 'PasswordManagement@store')->name('set-password');

    });







});
