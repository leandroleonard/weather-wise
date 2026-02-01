<?php

namespace App\Http\Controllers;

use App\Services\CityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private CityService $cityService;

    public function __construct()
    {
        $this->cityService = new CityService();
    }

    public function index()
    {
        $user = auth()->user();
        $city = $user->city;

        if (!$city) {
            return view('dashboard.index')->with('city', null);
        }

        $data = $this->cityService->getCity($city->id);

        return view('dashboard.index', $data);
    }

    public function setup(Request $request)
    {
        return view('auth.setup');
    }

}
