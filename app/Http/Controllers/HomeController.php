<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCities = City::where('is_featured', 1)
            ->with(['weather'])
            ->get();

        return view('app', compact('featuredCities'));
    }
}
