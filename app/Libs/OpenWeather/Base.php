<?php

namespace App\Libs\OpenWeather;

use Illuminate\Support\Facades\Http;


class Base
{
    private string $url = 'https://api.openweathermap.org';

    private string $apiKey;

    public function __construct($apiKey = null) {
        $this->apiKey = $apiKey;
    }

    public function getApiUrl(){
        return $this->url;
    }

    public function get($uri, $params){
        return Http::get($this->getApiUrl() . $uri, $params);
    }

}
