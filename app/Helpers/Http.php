<?php

namespace App\Helpers;

use App\Http\Traits\LoggingTrait;
use App\Models\Country;
use App\Models\MerchantStore;
use App\Models\Order;
use App\Models\SsoRequest;

class Http
{
    static $RequestHeaders = [
        'locale'        =>  'x-locale',
        'currency'      =>  'x-currency',
        'deviceType'    =>  'x-device-type',
        'osVersion'     =>  'x-os-version',
        'appVersion'    =>  'x-app-version',
        'accessToken'   =>  'x-access-token',
        'cfIPCountry'   =>  'cf-ipcountry'
    ];

    static $CurlContentTypes = [
        'JSON'                  =>  'Application/json',
        'MultiPartFormData'     =>  'Multipart/form-data'
    ];


    //in case of successful create, read, update, delete & any successful operation
    const SUCCESS = "success";
    const CREATED = "created";

    //in case of operational or process failure
    const BAD_REQUEST = "bad_request";

    //in case of authentication failure, trying to access any protected route with expired or no API token
    const UNAUTHORISED = "unauthorised";

    //in case of authentication failure, trying to access any protected route with expired or no API token
    const MAINTENANCE = "maintenance";

    //in case of validation failure
    const INPROCESSABLE = "inprocessable";

    //in case of internal server error
    const SERVER_ERROR = "server_error";

    static $Codes = [
        self::SUCCESS           => 200,
        self::CREATED           => 201,
        self::BAD_REQUEST       => 400,
        self::UNAUTHORISED      => 401,
        self::INPROCESSABLE     => 422,
        self::MAINTENANCE       => 503,
        self::SERVER_ERROR      => 500,
    ];

    public static function getApiPossibleCodes(){
        return array_values( self::$Codes );
    }

    public static function getIpDetails($request, $event, $identifier, $description = "")
    {
        $defaultCountry = [
            'country_name' => 'Pakistan'
        ];
//        $ipFound = false;
        $ipStackPermissionsEnabled = env('APP_ENV') == Constant::APP_ENV_TYPES['production'] || env('IPSTACK_API_ENABLED');
        $enableIpStackApiCall = in_array($event, ["one-tap-customer-identifier", "access-token"]);
        try {
            if ($ipStackPermissionsEnabled && $enableIpStackApiCall) {
//                if (!$ipFound) {
                $ip = Helper::getUserIP($request);
                $ipDetails = IpStack::getIpDetails($ip, $identifier, $description);
                if (isset($ipDetails['country_name']) && !empty($ipDetails['country_name'])) {
                    $defaultCountry = $ipDetails;
                    return $defaultCountry;
                }
//                }
            }

            if(!$ipStackPermissionsEnabled || !$enableIpStackApiCall){
                $ipCountry = $request->header(Constant::REQUEST_HEADERS['cf_ip_country']);
                if (!empty($ipCountry)) {
                    $country = Country::getCountryDataFromCode($ipCountry);
                    if ($country) {
//                        $ipFound = true;
                        $defaultCountry['country_name'] = $country->name;
                        return $defaultCountry;
                    } else {
                        throw new \Exception("Unidentified country ip request is triggered");
                    }
                }
            }

            return $defaultCountry;
        } catch (\Exception $e) {
            AppException::log($e);
        }
    }

    public static function getRequestIdentifiers($object, $callFrom = '', $hasObjectDetails = Constant::Yes){
        $identifier = [
            'merchant_id' => '',
            'store_id' => '',
            'env_id' => '',
            'request_id' => '',
            'request_from' => '',
        ];

        if ($callFrom == "checkout") {
            if($hasObjectDetails == Constant::No) {
                $object = Order::getByReference($object);
            }
            $identifier['merchant_id'] = optional($object)->merchant_id;
            $identifier['store_id'] = optional($object)->store_id;
            $identifier['env_id'] = optional($object)->env_id;
            $identifier['request_id'] = optional($object)->order_ref;
            $identifier['request_from'] = $callFrom;
        }
        else if ($callFrom == "sso" || $callFrom == "identity-verification") {
            if($hasObjectDetails == Constant::No) {
                $object = SsoRequest::getByRequestId( $object, Constant::Yes );
            }
            $identifier['merchant_id'] = optional(optional($object)->store)->merchant_id;
            $identifier['store_id'] = optional($object)->store_id;
            $identifier['env_id'] = optional(optional($object)->client)->env_id;
            $identifier['request_id'] = optional($object)->request_id;
            $identifier['request_from'] = $callFrom;
        }
        else if ($callFrom == "customer-portal") {
            $store = MerchantStore::getCustomerPortalStore();

            $identifier['merchant_id'] = optional($store)->merchant_id;
            $identifier['store_id'] = $store->id;
            $identifier['env_id'] = env('UNIVERSAL_CHECKOUT_INTEGRATION_TYPE');
            $identifier['request_id'] = $object;
            $identifier['request_from'] = $callFrom;
        }

        return (object) $identifier;
    }

}
