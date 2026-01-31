<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SetupController extends Controller
{
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
            'city' => 'required|string|max:100',
            'temp_unit' => 'required|in:celsius,fahrenheit',
            'refresh_rate' => 'required|in:180,300,1440',
        ]);

        $user = auth()->user();

        foreach(Arr::except($request->input(), '_token') as $key => $value)
            UserSettings::insert(['property' => $key, 'value' => $value, 'user_id' => $user->id]);

        $user->has_completed_setup = true;
        $user->save();



        return redirect()->route('dashboard')->with('success', 'Settings saved successfully!');
    }
}
