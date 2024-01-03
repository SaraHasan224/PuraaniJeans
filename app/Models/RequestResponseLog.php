<?php

/*
 * Author: Miesam Jafry
 * Dated: 12 November 2018
 * Description: Class to dump request and response into database
 */

namespace App\Models;

use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\Http;
use App\Http\Traits\LoggingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class RequestResponseLog extends Model
{
    static $requestUpdateData = [];
    static $requestStartTime = null;
    protected $table = 'request_response_logs';
    public $timestamps = false;
    protected $guarded = [];

    public static function logRequest( $request )
    {
        $requestData = $request->except( Constant::unSerializableFields );
        $sessionId = null;
        $orderRef = null;

        if($request->has('order_ref')) {
            $orderRef =  $request->has('order_ref') ? $request->order_ref : null;
        }
        if($request->has('sso_request_id')) {
            $orderRef =  $request->has('sso_request_id') ? $request->sso_request_id : null;
        }

        RequestResponseLog::create([
            'request_id'=>  $request->request_id,
            'base_url'        => URL::to(''),
            'url'       =>  $request->path(),
            'ip'        =>  Helper::getUserIP( $request ),
            'method'    =>  $request->getMethod(),
            'time_in'   =>  self::$requestStartTime,
            'request_data' => serialize( Helper::removeSensitiveData( $requestData ) ),
            'request_headers' => serialize( $request->header() ),
            'merchant_id' => $request->has('merchant_id') ? $request->merchant_id : null,
            'client_id' => $request->has('client_id') ? $request->client_id : null,
            'store_id' => $request->has('merchant_store_id') ? $request->merchant_store_id : null,
            'env_id' => $request->has('env_id') ? $request->env_id : null,
            'oauth_token_id'  =>  $request->has('auth_token_id') ? $request->token_id : null,
            'merchant_order_id'  =>  $request->has('merchant_order_id') ? $request->merchant_order_id : null,
            'order_ref' => $orderRef,
            'session_id' => $request->has('session_id') && empty($orderRef) ? $request->session_id : $sessionId,
            'created_at' => now()
        ]);
        $request->request->remove('auth_token_id');
    }

    public static function logResponse( $request, $response )
    {
        $dataToAddInRequest = [];
        $decodedResponse = json_decode( $response->getContent(), true );

        if( isset( $decodedResponse['status'] ) && $decodedResponse['status'] == Http::$Codes[ Http::SUCCESS ] )
        {
            if( isset( $decodedResponse['body'] )  && is_array( $decodedResponse['body'] ) )
            {
                if( isset( $decodedResponse['body']['order_reference'] ) && !empty( $decodedResponse['body']['order_reference'] ) )
                {
                    $dataToAddInRequest['order_ref'] = $decodedResponse['body']['order_reference'];
                }

                if( isset( $decodedResponse['body']['merchant_order_id'] ) && !empty( $decodedResponse['body']['merchant_order_id'] ) )
                {
                    $dataToAddInRequest['merchant_order_id'] = $decodedResponse['body']['merchant_order_id'];
                }
            }
        }

        $orderRef = $request->has('order_ref') && !array_key_exists('order_ref', $dataToAddInRequest) ? $request->order_ref : null;

        if($request->has('sso_request_id')) {
            $orderRef = $request->has('sso_request_id') && !array_key_exists('sso_request_id', $dataToAddInRequest) ? $request->sso_request_id : null;
        }

        $responseData = json_decode( $response->getContent(), true );
        $dataToAddInRequest['time_out'] =  microtime(false );
        $dataToAddInRequest['http_status'] =  $response->getStatusCode();
        $dataToAddInRequest['session_id'] =  $request->has('session_id') && !empty($request->session_id) ? $request->session_id : null;

        $dataToAddInRequest['client_id'] =  $request->has('client_id') && !array_key_exists('order_ref', $dataToAddInRequest) ? $request->client_id : null;
        $dataToAddInRequest['store_id'] =  $request->has('store_id') && !array_key_exists('store_id', $dataToAddInRequest) ? $request->store_id : null;
        $dataToAddInRequest['env_id'] =  $request->has('env_id') && !array_key_exists('env_id', $dataToAddInRequest) ? $request->env_id : null;
        $dataToAddInRequest['order_ref'] =  $orderRef;
        $dataToAddInRequest['customer_id'] =  $request->has('customer_id') && !array_key_exists('customer_id', $dataToAddInRequest) ? $request->customer_id : null;

        if( !empty( $responseData ) )
        {
            $dataToAddInRequest['response_data'] =  serialize( $responseData );
        }

        RequestResponseLog::where('request_id', $request->request_id)->update( $dataToAddInRequest );
    }

    public static function addData( $request, $data )
    {
        foreach( $data as $key => $value )
        {
            self::$requestUpdateData[ $key ] = $value;
        }
    }

    public static function logCustomRequest( $request, $requestData, $response, $params = [] )
    {
        RequestResponseLog::create([
            'request_id'=>  $request->request_id,
            'url' => array_key_exists('url', $params) ? $params['url'] : null,
            'method'    =>  $request->getMethod(),
            'time_in'   =>  self::$requestStartTime,
            'request_data' => serialize( Helper::removeSensitiveData( $requestData ) ),
            'response_data' => serialize( $response ),
            'request_headers' => serialize( $request->header() ),
            'merchant_id' => array_key_exists('merchant_id', $params) ? $params['merchant_id'] : null,
            'client_id' => array_key_exists('client_id', $params) ? $params['client_id'] : null,
            'store_id' => array_key_exists('merchant_store_id', $params) ? $params['merchant_store_id'] : null,
            'env_id' => array_key_exists('env_id', $params) ? $params['env_id'] : null,
            'order_ref' => array_key_exists('order_ref', $params) ? $params['order_ref'] : null,
            'sso_request_id' => array_key_exists('sso_request_id', $params) ? $params['sso_request_id'] : null,
            'created_at' => now()
        ]);
    }
}
