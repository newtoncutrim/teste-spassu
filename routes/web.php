<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Dashboard');
});

Route::get('/book', function () {
    return view('Book');
});

Route::get('/author', function () {
    return view('Author');
});

Route::get('/topic', function () {
    return view('Topic');
});