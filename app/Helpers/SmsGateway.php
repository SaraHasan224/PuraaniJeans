<?php

namespace App\Helpers;

//use App\Jobs\SendSmsGatewayJob;

use App\Jobs\SendSmsGatewayJob;

class SmsGateway
{
    public static function sendSms($data)
    {
        try {
            $action = $data['action'] ?? '';

            dispatch(new SendSmsGatewayJob($data, $action));
        } catch (\Exception $e) {
            AppException::log($e);

            return false;
        }
    }
}
