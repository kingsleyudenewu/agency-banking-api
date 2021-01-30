<?php


use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('https://koloo.ng');
});


Route::get('/sandbox', function(){

    $today = \Carbon\Carbon::parse('1984-01-12');

    dd($today->format('l'));


});
