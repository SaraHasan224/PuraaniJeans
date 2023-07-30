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
            'merchant-store' => [
                'client_id' => 'required|string|exists:oauth_clients,id',
                'store_id' => 'nullable|string|'.Rule::exists('merchant_stores', 'store_slug')->where('merchant_id', $merchantId),
            ],
            'sso-request'  => [
                'client_id' => 'required|string',//|exists:oauth_clients,id
                'scope' => 'required|string|'.Rule::in('profile'),
                'response_type' => 'required|string|'.Rule::in('code'),
                'state' => 'required|string',
            ],
            'sso-store' => [
                'client_id' => 'required|string|exists:oauth_clients,id',
                'store_id' => 'nullable|string|'.Rule::exists('merchant_stores', 'store_slug')->where('merchant_id', $merchantId),
            ],
        ];

        return $rules[ $type ];
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
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
