<?php

namespace Database\Seeders;

use App\Models\Localization\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $country = Country::upsert($this->getAddressInformation('data/country.json'), ['name', 'iso_code_2', 'iso_code_3']);
    }

    protected function getAddressInformation(string $path)
    {
        if ($path) {
            $countryCollection = [];
            $allCountries = $this->getFromStorage('data/country.json');
            foreach ($allCountries as $country) {
                if (! empty($country)) {
                    $data = [
                        'name' => $country->name,
                        'iso_code_2' => $country->iso_code_2,
                        'iso_code_3' => $country->iso_code_3,
                        'isd_code' => $country->isd_code,
                        'address_format' => $country->address_format,
                        'postcode_required' => $country->postcode_required,
                        'status' => ! ((config('services.defaults.country') != $country->iso_code_2)),
                        'region' => $country->region,
                        'timezone' => $country->timezone,
                        'timezone_diff' => $country->timezone_diff,
                        'currency' => strtoupper($country->currency),
                        'flag' => $country->flag,
                    ];
                    $countryCollection[] = $data;
                }
            }

            return $countryCollection;
        }
    }



    protected function getFromStorage(string $path)
    {
        return json_decode(Storage::disk('local')->get($path));
    }

}
