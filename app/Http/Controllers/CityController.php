<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\Request;


class CityController extends Controller
{
    private CityService $cityService;

    public function __construct()
    {
        $this->cityService = new CityService();
    }
    public function index()
    {
        echo "<pre>";
        var_dump($this->cityService->getCity("Luanda"));

    }

    public function get(Request $request)
    {
        $q = $request->query('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $cities = City::where('name', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'name', 'state', 'country']);

        return response()->json($cities);
    }
}
