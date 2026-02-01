<?php

namespace App\Libs\OpenWeather\Events;

use App\Libs\OpenWeather\Base;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CityEvent extends Base
{
    public function getGeo(string $city)
    {
        $response = $this->get( '/geo/1.0/direct', [
            'q' => $city,
            'limit' => 1,
            'appid' => config('services.openweather.key')
        ]);

        Log::debug('Geo response', var_dump($response));

        if ($response->failed() || empty($response[0])) {
            return null;
        }

        $location = $response[0];

        return ['lat' => $location['lat'], 'lon' => $location['lon']];
    }

    public function getCityForecast($lat, $lon){
        $response = $this->get("/data/2.5/forecast", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => config('services.openweather.key'),
            'units' => 'metric',
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    public function oneCall($lat, $lon){
        $response = $this->get('/data/3.0/onecall', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => config('services.openweather.key'),
            'units' => 'metric',
        ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
