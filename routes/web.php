<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Livewire\HelloWorld;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('album');
    }

    return redirect()->route('login');
});

Route::get('/hello-world', HelloWorld::class);

Route::middleware('guest')->group(function () {
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/album', function () {
        return view('album');
    })->name('album');
    Route::get('/intercambios', function () {
        return view('trades');
    })->name('trades');
    Route::get('/mercado', function () {
        return view('market');
    })->name('market');
    Route::get('/estadisticas', function () {
        return view('stats');
    })->name('stats');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
