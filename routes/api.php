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

    Route::group(['prefix' => 'customers', 'namespace' => 'Api\Customer', 'as' => 'customers.'], function () {

        Route::post('/', 'CustomerController@store')->name('new');

    });

    Route::group(['prefix' => 'geo', 'namespace' => 'Api\Geo', 'as' => 'countries.'], function () {

        Route::get('/countries', 'GeoController@countries')->name('countries');
        Route::get('/countries/{id}/states', 'GeoController@states')->name('states');

        Route::post('/countries', 'GeoController@createCountry')
            ->middleware(['auth:api', 'admin-check'])
            ->name('countries.store');

        Route::post('/countries/{id}/states', 'GeoController@createState')
            ->middleware(['auth:api', 'admin-check'])
            ->name('countries.state.store');

    });


    /**
     * Profile endpoint
     */
    Route::group(['prefix' => 'profile', 'namespace' => 'Api\Account', 'as' => 'profile.', 'middleware' => ['auth:api']], function () {

        Route::get('/', 'ProfileController@index')->name('get');

    });

    Route::group(['prefix' => 'accounts', 'namespace' => 'Api\Account', 'as' => 'accounts.', 'middleware' => ['auth:api']], function () {

        Route::get('/', 'AccountsController@index')->name('get');

    });



    // savings outside of admin
    Route::group([
        'prefix' => 'savings',
        'namespace' => 'Api\Savings',
        'as' => 'agents.',
        'middleware' => ['auth:api']], function () {

        Route::get('/cycles', 'SavingCyclesController@index')->name('savings.cycles');

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

        Route::group(['prefix' => 'savings', 'as' => 'savings.'], function(){

            Route::get('/cycle', 'SavingCycleManagement@index')->name('cycle.get');
            Route::post('/cycle', 'SavingCycleManagement@store')->name('cycle.create');
            Route::patch('/cycle', 'SavingCycleManagement@update')->name('cycle.update');
        });


        Route::group(['prefix' => 'account', 'as' => 'balance.'], function(){

            Route::post('/balance', 'BalanceController@store')->name('store');
        });


    });




    Route::group([
        'prefix' => 'savings',
        'namespace' => 'Api\Savings',
        'as' => 'savings.',
        'middleware' => ['auth:api']], function () {


        Route::get('/', 'SavingsController@show')->name('show');
        Route::post('/', 'SavingsController@store')->name('new');

    });



    Route::group([
        'prefix' => 'transactions',
        'namespace' => 'Api\Customer',
        'as' => 'transactions.',
        'middleware' => ['auth:api']], function () {


        Route::get('/', 'TransactionController@index')->name('list');


    });



});

/**
 *  08066100333
 *  239aHD#DL1230
 */
