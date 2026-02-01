<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Services\CityService;
use Illuminate\Console\Command;

class UpdateFeaturedCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-featured-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily forecast for featured cities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new CityService();

        $featuredCities = City::where('is_featured', 1)->get();

        foreach($featuredCities as $city){
            $result = $service->popupateWeather($city->id, true);

            if($result)
                $this->info("Featured city {$city->name} updated");
            else
                $this->error("Error while updating city {$city->name}");
        }
    }
}
