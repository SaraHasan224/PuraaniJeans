<?php

namespace App\Helpers;

class Language
{
    static function getMessage($key){
        try{
            return __($key);
        } catch( \Exception $e )
        {
        	AppException::log($e);
            return __('general.error');
        }
    }
}
