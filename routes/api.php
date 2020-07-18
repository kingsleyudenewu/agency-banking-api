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

Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['check-account']], function () {


    Route::get('/', 'IsAlive@check');

    Route::post('/register', '\App\Http\Controllers\Api\Customer\CustomerController@store')->name('new.customer');
    Route::post('/auth/new_password', '\App\Http\Controllers\Api\Customer\SetPasswordController@store')->name('new.password');


    Route::get('/stats', '\App\Http\Controllers\Api\StatsController@index')->middleware(['auth:api']);




    /**
     * Auth endpoints
     */
    Route::group(['prefix' => 'auth', 'namespace' => 'Api\Auth', 'as' => 'auth.'], function () {

        Route::post('/login', 'LoginController@postLogin')->name('login.post');
        Route::post('/login/otp', 'LoginOTPController@process')->name('login.post.top');
        Route::post('/password_request', 'PasswordResetController@store');

    });

    Route::group(['prefix' => 'customers', 'middleware' => ['auth:api'], 'namespace' => 'Api\Customer', 'as' => 'customers.'], function () {

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
        Route::get('/{id}', 'AccountsController@show')->name('show');
        Route::get('/wallet', 'AccountsController@wallet')->name('wallet');
        Route::post('/fund', 'FundAccountController@fund')
            ->middleware('otp-required-for-auth-user')
            ->name('fund');
        Route::post('/withdrawal', 'WithdrawalController@store');

        Route::group(['prefix' => 'commission'], function(){
            Route::get('/payouts', 'CommissionPayoutRequest@index');
            Route::post('/payouts/{id}', 'CommissionPayoutRequest@update');
            Route::post('/payouts', 'CommissionPayoutRequest@store')->middleware('otp-required-for-auth-user');
        });


        Route::post('/transaction/pin', 'TransactionPinController@store');
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
        Route::post('/update', 'UpdateAgentController@store');
        Route::post('/documents', 'DocumentManagement@upload')->name('document.upload');

    });



    Route::group([
        'prefix' => 'admin',
        'namespace' => 'Api\Admin',
        'as' => 'admin.',
        'middleware' => ['auth:api', 'admin-check']], function () {

        Route::get('/super-agents', 'GetSuperAgentsController@index');
        Route::post('/password', 'PasswordManagement@store')->name('set-password');

        Route::put('/account/{id}/{action}', 'AccountSuspensionController@update');
        Route::get('/savings', 'AdminViewSavingsController@index');


         Route::get('/settings', 'SettingsController@index')->name('settings');
         Route::post('/settings', 'SettingsController@store')
                ->middleware('otp-required-for-auth-user')
              ->name('settings.store');

        Route::group(['prefix' => 'savings', 'as' => 'savings.'], function(){

            Route::get('/cycle', 'SavingCycleManagement@index')->name('cycle.get');
            Route::post('/cycle', 'SavingCycleManagement@store')->name('cycle.create');
            Route::patch('/cycle', 'SavingCycleManagement@update')->name('cycle.update');
        });


        Route::group(['prefix' => 'account', 'as' => 'balance.'], function(){
            Route::post('/approval', 'AgentApprovalController@store')->name('approval');
            Route::get('/pending', 'PendingAccountController@index')->name('pending');
        });


    });




    Route::group([
        'prefix' => 'savings',
        'namespace' => 'Api\Savings',
        'as' => 'savings.',
        'middleware' => ['auth:api']], function () {


        Route::get('/', 'SavingsController@show')->name('show');
        Route::post('/', 'SavingsController@store')
            ->middleware('otp-required-for-auth-user')
            ->name('new');
        Route::post('/{id}/contribute', 'SavingsController@contribute')
            ->name('contribute');
        Route::get('/{id}/contribute', 'SavingsController@getContributions')->name('get.contributions');


    });



    Route::group([
        'prefix' => 'transactions',
        'namespace' => 'Api\Customer',
        'as' => 'transactions.',
        'middleware' => ['auth:api']], function () {
        Route::get('/', 'TransactionController@index')->name('list');
    });


    Route::group([
        'prefix' => 'services',
        'namespace' => 'Api\Service',
        'as' => 'services.',
        'middleware' => []], function () {
        Route::post('/monnify/check', 'MonnifyController@check')->name('monnify.check');
    });


    Route::group([
        'prefix' => 'banks',
        'namespace' => 'Api\Bank',
        'as' => 'banks.',
        'middleware' => []], function () {
        Route::get('/', 'BanksController@index')->name('list');
        Route::post('/', 'BanksController@store')
            ->middleware(['auth:api', 'admin-check'])
            ->name('store');
        Route::put('/{id}', 'BanksController@update')
            ->middleware(['auth:api', 'admin-check']);
    });


});
