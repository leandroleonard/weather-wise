<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        return view('dashboard.index');
    }

    public function setup(Request $request){
        return view('auth.setup');
    }

}
