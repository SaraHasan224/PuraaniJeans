<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function getActiveCountries($countryCode = null){
        try
        {
            $countries = Country::getEnabledCountries();
            $selectedCountry = null;
            if($countryCode){
                $selectedCountry = Country::getCountryByCountryCode($countryCode);
            }
            $response = ['countries' => $countries, 'selectedCountry' => $selectedCountry];
            return ApiResponseHandler::success($response);
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::serverError($e);
        }
    }

    public function getPhoneNumberMaskByCode($countryCode ){
        try
        {
            $phoneMask = Country::getPhoneNumberMaskByCode($countryCode);
            $response = ['phoneMask' => $phoneMask->phone_number_mask];
            return ApiResponseHandler::success($response);
        }
        catch (\Exception $e)
        {
            AppException::log($e);
            return ApiResponseHandler::serverError($e);
        }
    }
}
