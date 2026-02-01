<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $guarded = [''];

    function users()
    {
        return $this->hasMany(User::class, 'city_id', 'id');
    }

    public function weather()
    {
        return $this->hasOne(Weather::class)->latestOfMany('dt');
    }

    function daily()
    {
        return $this->belongsTo(WeatherDaily::class, 'id', 'city_id');
    }

    function hourly()
    {
        return $this->belongsTo(WeatherHourly::class, 'id', 'city_id');
    }

    function alerts()
    {
        return $this->belongsTo(WeatherAlerts::class, 'id', 'city_id');
    }
}
