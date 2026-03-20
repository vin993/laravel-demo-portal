<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function searchCountries(Request $request)
    {
        return Country::select('id', 'name', 'code')
            ->where('name', 'like', '%' . $request->q . '%')
            ->get();
    }

    public function searchStates(Request $request)
    {
        return State::select('id', 'name', 'state_code')
            ->where('country_id', $request->country_id)
            ->where('name', 'like', '%' . $request->q . '%')
            ->get()
            ->map(function ($state) {
                return [
                    'id' => $state->id,
                    'text' => $state->name,
                    'state_code' => $state->state_code
                ];
            });
    }
    public function searchCities(Request $request)
    {
        $search = $request->get('q');
        $stateId = $request->get('state_id');

        $cities = City::select('id', 'name')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when($stateId, function ($query) use ($stateId) {
                return $query->where('state_id', $stateId);
            })
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        return response()->json($cities);
    }

    public function getStates($countryId)
    {
        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();
        return response()->json($states);
    }

    public function getCities($stateId)
    {
        $cities = City::where('state_id', $stateId)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();
        return response()->json($cities);
    }
}