<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    protected $table =  "oauth_clients";

    protected $fillable = [
      'customer_id',
      'name',
      'secret',
      'redirect_url',
      'personal_access_client',
      'password_client',
      'revoked',
    ];

    public static function getValidationRules( $type, $params = [] )
    {
        $merchantId = array_key_exists('merchant_id',$params) ? $params['merchant_id'] : null;
        $rules = [
            'merchant-token' => [
                'client_id' => 'required|string',//|exists:oauth_clients,id
                'client_secret' => 'required|string|exists:oauth_clients,secret',
                'grant_type' => 'required|string|'.Rule::in('client_credentials'),
            ],
        ];

        return $rules[ $type ];
    }

    public static function getClientById( $id )
    {
        return self::where('id',$id)->first();
    }

    public static function getClientByIdAndSecret( $requestData )
    {
        return self::where('id',$requestData['client_id'])
            ->where('secret',$requestData['client_secret'])
            ->where('revoked', 0)->first();
    }
}
