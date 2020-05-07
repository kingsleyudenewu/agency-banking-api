<?php

namespace App\Http\Controllers\Api\Geo;

use App\Country;
use App\Http\Controllers\APIBaseController;
use App\Http\Requests\CountryCreateRequest;
use  App\Http\Resources\State;
use  App\Http\Resources\Country as CountryTransformer;
use Illuminate\Http\Request;


/**
 * Class GeoController
 *
 * @package \App\Http\Controllers\Api\Geo
 */
class GeoController extends APIBaseController
{

    public function countries()
    {
        return $this->successResponse('countries', CountryTransformer::collection( Country::enabled()->get()));
    }


    public function states($id)
    {
        $country = Country::findOrFail($id);
        return $this->successResponse('states', State::collection($country->states()->enabled()->get()));
    }

    public function createCountry(CountryCreateRequest $request)
    {
         $data = $request->validated();
         $data['currency'] = strtoupper($data['currency']);

         return $this->successResponse('OK', Country::create($data));

    }


    public function createState(Request $request, $countryId)
    {
        $data = $request->validate(['name' => 'required:max:255']);

        $country = Country::findOrFail($countryId);


        return $this->successResponse('OK', new State( $country->states()->create($data)));
    }
}
