<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register_submit');


Route::group(['prefix' => 'dashboard'], function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
