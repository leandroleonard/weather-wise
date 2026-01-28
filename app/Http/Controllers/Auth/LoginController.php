<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function index(Request $request){
        return Inertia::render('Auth/Login');
    }

    public function register(Request $request){
        return Inertia::render('Auth/Register');
    }
}
