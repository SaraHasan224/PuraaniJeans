<?php

namespace App\Helpers;

use App\Models\OtpBlocklist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiResponseHandler
{

    /**
     * @param array $body
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success( $body = [], $message ="messages.general.success" ){

        return  self::send(
            Http::$Codes[ Http::SUCCESS ],
            is_array($message) ? $message : [Language::getMessage( $message )],
            (object) $body,
            null
        );
    }

    /**
     * @param $validationErrors (coudle be array of errors or validator object)
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationError( $validationErrors, $message = "general.validation", $body = [] ){

        $errorMessages = [];

        if( is_array( $validationErrors ) ){
            $errorMessages = array_values( $validationErrors );
        }
        else
        {
            foreach( $validationErrors->getMessages() as $key => $errors )
            {
                $errorMessages = array_merge( $errorMessages, $errors );
            }
        }

        return self::send(
            Http::$Codes[ Http::INPROCESSABLE ],
            $errorMessages,
            (object) $body,
            null
        );
    }

    /**
     * @param $validationErrors (coudle be array of errors or validator object)
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationFailureError( $validationErrors, $message = "general.validation", $body = [] ){

        $errorMessages = [];

        if( is_array( $validationErrors ) ){
            $errorMessages = array_values( $validationErrors );
        }
        else
        {
            foreach( $validationErrors->getMessages() as $key => $errors )
            {
                $errorMessages = array_merge( $errorMessages, $errors );
            }
        }

        return self::send(
            Http::$Codes[ Http::BAD_REQUEST ],
            $errorMessages,
            (object) $body,
            null
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function authenticationError(){

        return self::send(
            Http::$Codes[ Http::UNAUTHORISED ],
            [Language::getMessage( "general.unauthenticated" )],
            (object) [],
            null
        );
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function underMaintenance(){

        return self::send(
            Http::$Codes[ Http::MAINTENANCE ],
            [Language::getMessage( "general.maintenance" )],
            (object) [],
            null
        );
    }

    /**
     * @param string $message
     * @param null $exception
     * @param array $body
     * @return \Illuminate\Http\JsonResponse
     */
    public static function failure( $message = 'general.error', $exception=null, $body = [] ){

        return self::send(
            Http::$Codes[ Http::BAD_REQUEST ],
            is_array($message) ? $message : [ Language::getMessage( $message ) ],
            (object) $body,
            $exception
        );
    }

    public static function serverError( $exception = null, $message = "" )
    {
        $message = empty($message) ? __('messages.general.crashed') : $message;
        $exceptionMsg = $exception ? $exception->getMessage() : '';
        return self::send(
            Http::$Codes[ Http::SERVER_ERROR ],
            $message,
            (object) [],
            $exceptionMsg
        );
    }

    /**
     * @param $code
     * @param $message
     * @param $exception
     * @return \Illuminate\Http\JsonResponse
     */

    public static function exception( $code, $message, $exception=null ){
        return self::send(
            $code,
            [$message],
            [],
            $exception
        );
    }

    /**
     * @param $status
     * @param $message
     * @param $body
     * @param $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private static function send( $status, $message, $body, $exception ){

        return response()->json([
            'status'    =>  $status,
            'message'   =>  $message,
            'body'      =>  $body,
            'exception' =>  $exception
        ], $status, [], JSON_UNESCAPED_UNICODE );
    }
}
