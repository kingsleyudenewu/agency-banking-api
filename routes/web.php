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

Route::get('/', function (\App\Services\Bitly\ShortUrl $url) {
    return redirect('https://koloo.ng');
});


Route::get('/_messages', function (){

    return \App\Message::latest()->get();
});

Route::get('/_otp', function (){

    return \App\OTP::latest()->get();
});
