<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'submit'])->name('login_submit');
Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

Route::get('register', [RegisterController::class, 'create'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register_submit');


Route::group(['prefix' => 'dashboard'], function(){
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('teste', function(){return session()->all();});
})->middleware('auth');
