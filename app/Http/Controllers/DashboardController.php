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

        $alerts = DB::table('weather_alerts')
            ->where('city_id', $city->id)
            ->where(function ($q) {
                $q->whereNull('end_time')->orWhere('end_time', '>=', \Carbon\Carbon::now());
            })
            ->orderBy('start_time', 'asc')
            ->get();

        $offset = $city->timezone_offset ?? 0;

        return view('dashboard.index', compact('city', 'current', 'daily', 'hourly', 'alerts', 'offset'));
    }

    public function setup(Request $request)
    {
        return view('auth.setup');
    }

}
