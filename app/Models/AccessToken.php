<?php

namespace App\Models;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\Http;
use Jenssegers\Agent\Agent;
use Laravel\Passport\Token as Token;

class AccessToken extends Token
{
    public static function getById( $id )
    {
        return self::where('id', $id)->first();
    }

    public static function getAllById( $id )
    {
        return self::where('id', $id)->get();
    }

    public static function revokeOldTokensByName( $name )
    {
        return self::where('name', $name)->where('revoked', Constant::No)->update(['revoked' => Constant::Yes]);
    }

    public static function getTokenById( $tokenId ){
        return self::whereId($tokenId)->first();
    }

    public static function addIdentifierToAccessToken( $tokenId, $identifierParam )
    {
        $token = self::where('id', $tokenId)
            ->first();

        if( $token )
        {
            $token->store_id = !empty($identifierParam) ? $identifierParam : null;
            $token->save();

            return $token;
        }

        return false;
    }
}
