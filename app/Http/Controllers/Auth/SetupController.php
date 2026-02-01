<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSettings;
use App\Services\CityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SetupController extends Controller
{
    private CityService $cityService;

    public function __construct()
    {
        $this->cityService = new CityService();
    }

    public function index()
    {
        if (auth()->user()->has_completed_setup) {
            return redirect()->route('dashboard');
        }
        return view('auth.setup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'city' => 'required|numeric',
            'temp_unit' => 'required|in:celsius,fahrenheit',
            'refresh_rate' => 'required|in:180,300,1440',
        ], [
            'city' => 'Invalid city'
        ]);

        $response = $this->cityService->popupateWeather($request->city);

        $user = auth()->user();
        $user->city_id = $request->city;
        $user->save();

        foreach (Arr::except($request->input(), ['_token', 'city']) as $key => $value)
            UserSettings::insert(['property' => $key, 'value' => $value, 'user_id' => $user->id]);

        $user->has_completed_setup = true;
        $user->save();



        return redirect()->route('dashboard')->with('success', 'Settings saved successfully!');
    }
}
