<?php
namespace App\Services;
use App\Libs\OpenWeather\Events\CityEvent;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CityService
{

    public function create()
    {

    }

    public function getCity($id)
    {
        $city = City::whereId($id)->first();

        if (!$city->weather || $city->weather->expires_at < Carbon::now()) {
            $this->popupateWeather($id);
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
                $q->whereNull('end_time')->orWhere('end_time', '>=', Carbon::now());
            })
            ->orderBy('start_time', 'asc')
            ->get();


        $offset = $city->timezone_offset ?? 0;

        return ['city' => $city, 'current' => $current, 'daily' => $daily, 'hourly' => $hourly, 'alerts' => $alerts, 'offset' => $offset];
    }

    public function popupateWeather($cityId, $force = false)
    {
        $city = City::with(['daily', 'hourly', 'weather'])->whereId($cityId)->first();
        if (!$city)
            return null;

        $now = Carbon::now();

        if (($city->weather && $city->weather->expires_at > $now) && !$force)
            return true;

        $data = $this->getCityApi()->oneCall($city['lat'], $city['lon']);

        if (!$city->timezone && isset($data['timezone']))
            $city->timezone = $data['timezone'];

        if (!$city->timezone_offset && isset($data['timezone_offeset']))
            $city->timezone_offset = $data['timezone_offset'];

        $city->save();


        if (isset($data['current'])) {
            $current = $data['current'];
            $dtCurrent = Carbon::createFromTimestamp($current['dt']);

            DB::table('weather')->updateOrInsert(
                ['city_id' => $city->id, 'dt' => $dtCurrent],
                [
                    'sunrise' => isset($current['sunrise']) ? Carbon::createFromTimestamp($current['sunrise']) : null,
                    'sunset' => isset($current['sunset']) ? Carbon::createFromTimestamp($current['sunset']) : null,
                    'temperature' => $current['temp'] ?? null,
                    'feels_like' => $current['feels_like'] ?? null,
                    'temp_min' => null,
                    'temp_max' => null,
                    'pressure' => $current['pressure'] ?? null,
                    'humidity' => $current['humidity'] ?? null,
                    'dew_point' => $current['dew_point'] ?? null,
                    'clouds' => $current['clouds'] ?? null,
                    'uvi' => $current['uvi'] ?? null,
                    'visibility' => $current['visibility'] ?? null,
                    'wind_speed' => $current['wind_speed'] ?? null,
                    'wind_gust' => $current['wind_gust'] ?? null,
                    'wind_deg' => $current['wind_deg'] ?? null,
                    'rain_1h' => isset($current['rain']['1h']) ? $current['rain']['1h'] : 0,
                    'snow_1h' => isset($current['snow']['1h']) ? $current['snow']['1h'] : 0,
                    'pop' => null,
                    'weather_id' => $current['weather'][0]['id'] ?? null,
                    'weather_main' => $current['weather'][0]['main'] ?? null,
                    'weather_description' => $current['weather'][0]['description'] ?? null,
                    'weather_icon' => $current['weather'][0]['icon'] ?? null,
                    'cached_at' => $now,
                    'expires_at' => $now->copy()->addMinutes(300),
                    'updated_at' => $now,
                ]
            );
        }

        if (isset($data['hourly']) && is_array($data['hourly'])) {
            foreach ($data['hourly'] as $hour) {
                $dt = Carbon::createFromTimestamp($hour['dt']);

                DB::table('weather_hourly')->updateOrInsert(
                    ['city_id' => $city->id, 'dt' => $dt],
                    [
                        'temperature' => $hour['temp'] ?? null,
                        'feels_like' => $hour['feels_like'] ?? null,
                        'pressure' => $hour['pressure'] ?? null,
                        'humidity' => $hour['humidity'] ?? null,
                        'dew_point' => $hour['dew_point'] ?? null,
                        'clouds' => $hour['clouds'] ?? null,
                        'uvi' => $hour['uvi'] ?? null,
                        'visibility' => $hour['visibility'] ?? null,
                        'wind_speed' => $hour['wind_speed'] ?? null,
                        'wind_gust' => $hour['wind_gust'] ?? null,
                        'wind_deg' => $hour['wind_deg'] ?? null,
                        'pop' => $hour['pop'] ?? 0,
                        'rain_1h' => isset($hour['rain']['1h']) ? $hour['rain']['1h'] : 0,
                        'snow_1h' => isset($hour['snow']['1h']) ? $hour['snow']['1h'] : 0,
                        'weather_id' => $hour['weather'][0]['id'] ?? null,
                        'weather_main' => $hour['weather'][0]['main'] ?? null,
                        'weather_description' => $hour['weather'][0]['description'] ?? null,
                        'weather_icon' => $hour['weather'][0]['icon'] ?? null,
                        'cached_at' => $now,
                        'expires_at' => $now->copy()->addMinutes(300),
                        'updated_at' => $now,
                    ]
                );
            }
        }

        if (isset($data['daily']) && is_array($data['daily'])) {
            foreach ($data['daily'] as $day) {
                $forecastDate = Carbon::createFromTimestamp($day['dt'])->toDateString();

                DB::table('weather_daily')->updateOrInsert(
                    ['city_id' => $city->id, 'forecast_date' => $forecastDate],
                    [
                        'dt' => Carbon::createFromTimestamp($day['dt']),
                        'sunrise' => isset($day['sunrise']) ? Carbon::createFromTimestamp($day['sunrise']) : null,
                        'sunset' => isset($day['sunset']) ? Carbon::createFromTimestamp($day['sunset']) : null,
                        'moonrise' => isset($day['moonrise']) ? Carbon::createFromTimestamp($day['moonrise']) : null,
                        'moonset' => isset($day['moonset']) ? Carbon::createFromTimestamp($day['moonset']) : null,
                        'moon_phase' => $day['moon_phase'] ?? null,
                        'summary' => $day['weather'][0]['description'] ?? null,
                        'temp_day' => $day['temp']['day'] ?? null,
                        'temp_night' => $day['temp']['night'] ?? null,
                        'temp_eve' => $day['temp']['eve'] ?? null,
                        'temp_morn' => $day['temp']['morn'] ?? null,
                        'temp_min' => $day['temp']['min'] ?? null,
                        'temp_max' => $day['temp']['max'] ?? null,
                        'feels_like_day' => $day['feels_like']['day'] ?? null,
                        'feels_like_night' => $day['feels_like']['night'] ?? null,
                        'feels_like_eve' => $day['feels_like']['eve'] ?? null,
                        'feels_like_morn' => $day['feels_like']['morn'] ?? null,
                        'pressure' => $day['pressure'] ?? null,
                        'humidity' => $day['humidity'] ?? null,
                        'dew_point' => $day['dew_point'] ?? null,
                        'wind_speed' => $day['wind_speed'] ?? null,
                        'wind_gust' => $day['wind_gust'] ?? null,
                        'wind_deg' => $day['wind_deg'] ?? null,
                        'clouds' => $day['clouds'] ?? null,
                        'uvi' => $day['uvi'] ?? null,
                        'pop' => $day['pop'] ?? 0,
                        'rain' => $day['rain'] ?? 0,
                        'snow' => $day['snow'] ?? 0,
                        'weather_id' => $day['weather'][0]['id'] ?? null,
                        'weather_main' => $day['weather'][0]['main'] ?? null,
                        'weather_description' => $day['weather'][0]['description'] ?? null,
                        'weather_icon' => $day['weather'][0]['icon'] ?? null,
                        'cached_at' => $now,
                        'expires_at' => $now->copy()->addMinutes(300),
                        'updated_at' => $now,
                    ]
                );
            }
        }

        if (isset($data['alerts']) && is_array($data['alerts'])) {
            foreach ($data['alerts'] as $alert) {
                DB::table('weather_alerts')->updateOrInsert(
                    [
                        'city_id' => $city->id,
                        'event' => $alert['event'],
                        'start_time' => Carbon::createFromTimestamp($alert['start']),
                        'end_time' => Carbon::createFromTimestamp($alert['end']),
                    ],
                    [
                        'sender_name' => $alert['sender_name'] ?? null,
                        'description' => $alert['description'] ?? null,
                        'tags' => json_encode($alert['tags'] ?? []),
                        'cached_at' => $now,
                        'expires_at' => $now->copy()->addMinutes(30),
                        'updated_at' => $now,
                    ]
                );
            }
        }

        return true;
    }

    public function getCityById($id)
    {
        return City::whereId($id)->first();
    }

    private function getCityApi()
    {
        $credential = config('services.openweather.key');

        return (new CityEvent($credential));
    }
}
