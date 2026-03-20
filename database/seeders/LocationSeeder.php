<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocationSeeder extends Seeder
{
    private $apiUrl = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/refs/heads/master/json/countries%2Bstates%2Bcities.json';

    public function run(): void
    {
        $response = Http::get($this->apiUrl);
        $data = $response->json();

        foreach ($data as $country) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => $country['name'],
                'code' => $country['iso2'],
                'phone_code' => $country['phone_code'],
                'currency_code' => $country['currency'],
                'currency_symbol' => $country['currency_symbol'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (isset($country['states'])) {
                foreach ($country['states'] as $state) {
                    $stateId = DB::table('states')->insertGetId([
                        'name' => $state['name'],
                        'country_id' => $countryId,
                        'state_code' => $state['state_code'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (isset($state['cities'])) {
                        foreach ($state['cities'] as $city) {
                            DB::table('cities')->insert([
                                'name' => $city['name'],
                                'state_id' => $stateId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}