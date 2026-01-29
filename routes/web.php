<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::get('register', [LoginController::class, 'register'])->name('register');


Route::group(['prefix' => 'dashboard'], function(){
    Route::get('/', [DashboardController::class, 'index']);
});
