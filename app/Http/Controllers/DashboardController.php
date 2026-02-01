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

    // public function index(Request $request)
    // {
    //     $user = auth()->user();

    //     $city = null;

    //     if ($user->city_id)
    //         $city = $this->cityService->getCityById($user->city_id);

    //     // echo "<pre>";
    //     // exit(var_dump($city));


    //     return view('dashboard.index', ['city' => $city]);
    // }

    public function index()
    {
        $user = auth()->user();
        $city = $user->city;

        if (!$city) {
            return view('dashboard.index')->with('city', null);
        }

        $current = DB::table('weather')
            ->where('city_id', $city->id)
            ->orderByDesc('dt')
            ->first();

        $daily = DB::table('weather_daily')
            ->where('city_id', $city->id)
            ->orderBy('forecast_date')
            ->limit(5)
            ->get();

        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();

        $hourly = DB::table('weather_hourly')
            ->where('city_id', $city->id)
            ->whereBetween('dt', [$today, $tomorrow])
            ->orderBy('dt')
            ->get();

        return view('dashboard.index', compact('city', 'current', 'daily', 'hourly'));
    }

    public function setup(Request $request)
    {
        return view('auth.setup');
    }

}
