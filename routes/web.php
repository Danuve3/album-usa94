<?php

use App\Livewire\HelloWorld;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello-world', HelloWorld::class);
