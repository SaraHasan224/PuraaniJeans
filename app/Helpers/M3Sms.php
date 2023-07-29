<?php

namespace App\Helpers;

class M3Sms
{
    //private static $attempts=0;

    public static function sendSms($data)
    {
        $wsdl = env('M3TECH_SMS_URL', 'https://secure.m3techservice.com/GenericService/webservice_4_0.asmx?wsdl');

        try {
            $phoneNumber = $data['phone_number'];
            $message = $data['message'];
            $title = $data['title'];

            $requestData = [
                'UserId'       => env('M3_SMS_GATEWAY_USER_ID'),
                'Password'     => env('M3_SMS_GATEWAY_PASSWORD'),
                'MobileNo'     => $phoneNumber,
                'MsgId'        => $data['identifier'],
                'SMS'          => $message,
                'MsgHeader'    => env('M3_SMS_GATEWAY_MSG_HEADER'),
                'soap_version' => SOAP_1_2,
                'trace'        => true,
                'message_lang' => 'en',
            ];

            $options = [
                'stream_context' => stream_context_create(['http' => ['timeout' => 5]]),
            ];

            $client = new \SoapClient($wsdl, $options);
            $apiResponse = $client->SendSMS($requestData);
            $smsResultCode = $apiResponse->SendSMSResult;

            return $smsResultCode == Constant::M3TechSMSResponseCodes['success'];
        } catch (\Exception $e) {
            AppException::log($e);

            return false;
        }
    }
}
