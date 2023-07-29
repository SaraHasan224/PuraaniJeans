<?php

namespace App\Models;

// use Illuminate\Contracts\auth\MustVerifyEmail;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Helpers\Http;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use phpseclib3\System\SSH\Agent;
use function Ramsey\Uuid\v4; // include this

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'country_code',
        'phone_number',
        'country_id',
        'status',
        'subscription_status',
        'identifier',
        'last_login',
        'login_attempts',
        'origin_source',
        'email_verified_at',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $validationRules = [
        'register' => [
            'country' => 'required|string',
            'email_address' => 'required|email|email:rfc,dns|unique:customers,email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'subscription' => 'required|boolean',
        ],
        'login' => [
            'email_address' => 'required|email|email:rfc,dns',
            'password' => 'required|string',
        ]
    ];

    public static function getValidationRules($type, $params = [])
    {
        $rules = [
            'create'        => [
                'name'  => 'required',
                'email' => 'required|email:rfc,dns|unique:customers,email',
                'password' => 'required|max:30',
                'phone' => [
                    'required',
                    'regex:/^[0-9]+$/'
                ],
            ],
            'update'        => [
                'name'     => 'required',
                'email'    => 'required|email:rfc,dns',//|unique:users,email,' . $user_id,
                'phone'    => [
                    'required',
                    'string',
                    'regex:/^[0-9]+$/'
                ],
                'password' => 'same:confirm-password',
            ],
        ];

        return $rules[$type];
    }

    public function getPhoneNumberInternationalFormat()
    {
        return "+{$this->country_code}{$this->phone_number}";
    }

    public function getPhoneNumberHiddenInternationalFormat()
    {
        $phone = Helper::obfuscateString($this->phone_number);
        return "+({$this->country_code}){$phone}";
    }

    public static function findById($id){
        return self::where('id', $id)->first();
    }

    public static function findByEmail($email){
        return self::where('email', $email)->first();
    }

    public static function findByRef($ref){
        return self::where('identifier', $ref)->first();
    }

    public static function findByPhoneNumber($code, $phone)
    {
        return self::where('country_code',$code)
            ->where('phone_number',$phone)
            ->first();
    }

    public static function findNonAnonymousCustomerByOtp( $otp )
    {
        return self::where('country_code', $otp->country_code)
            ->where('phone_number', $otp->phone_number)
            ->whereNull('deleted_at')
            ->first();
    }


    public static function removeCustomer( $customer_id )
    {
        self::where('id', $customer_id )->delete();
    }

    public function updateNonVerifiedCustomer( $verifiedOtp )
    {
        $updateCols = [
            'is_verified' => Constant::Yes,
            'country_code' => $verifiedOtp->country_code,
            'phone_number' => $verifiedOtp->phone_number,
            'phone_verified_at' => Now(),
        ];
        $this->update($updateCols);
    }

    public static function createCustomer( $requestData, $identifier )
    {
        $emptyString = "";

        $data = [
            'first_name'            => $requestData['first_name'],
            'last_name'             => $requestData['last_name'],
            'email'                 => $requestData['email_address'],
            'country_code'          => array_key_exists("country_code", $requestData) ? $requestData['country_code'] : $emptyString,
            'phone_number'          => array_key_exists("phone_number", $requestData) ? $requestData['phone_number'] : $emptyString,
            'country_id'            => $requestData['country_id'],
            'password'              => $requestData['password'],
            'status'                => Constant::CUSTOMER_STATUS['Active'],
            'subscription_status'   => $requestData['subscription_status'] ?? 0,
            'identifier'            => $identifier,
        ];

        return self::create($data);
    }

    public static function getByFilters($filter)
    {
        $data = self::select('id', 'first_name', 'last_name', 'username', 'email', 'email_verified_at', 'country_code', 'phone_number', 'phone_verified_at', 'country_id', 'identifier', 'last_login', 'status',  'subscription_status', 'created_at','updated_at','deleted_at');
        $data = $data->withTrashed()->orderBy('id', 'DESC');

        if (count($filter))
        {
            if (!empty($filter['name']))
            {
                $data = $data->where('first_name', 'LIKE', '%' . trim($filter['first_name']) . '%')
                             ->orWhere('last_name', 'LIKE', '%' . trim($filter['last_name']) . '%');
            }
            if (!empty($filter['user_name']))
            {
                $data = $data->where('username', 'LIKE', '%' . trim($filter['user_name']) . '%');
            }

            if (!empty($filter['phone']))
            {
                $phone = trim($filter['phone']);
                $phone = Helper::formatPhoneNumber($phone);
                $data = $data->where('phone', 'LIKE', '%' . $phone . '%');
            }

            if (!empty($filter['email']))
            {
                $data = $data->where('email', 'LIKE', '%' . trim($filter['email']) . '%');
            }

            if (!empty($filter['last_login']))
            {
                $memberSince = trim($filter['last_login']);
                $data = $data->whereDate('last_login', '>=', date('Y-m-d', strtotime($memberSince)));
            }

            if (isset($filter['status']))
            {
                $data = $data->where('status', $filter['status']);
            }

            if (isset($filter['subscription_status']))
            {
                $data = $data->where('subscription_status', $filter['subscription_status']);
            }
        }

        $count = $data->count();

//        if (isset($filter['start']) && isset($filter['length']))
//        {
//            $data->skip($filter['start'])->limit($filter['length']);
//        }

        return [
            'count'   => $count,
            'offset'  => isset($filter['start']) ? $filter['start'] : 0,
            'records' => $data->get()
        ];
    }

    public function createAccessToken( $request, $revokeOldToken = false, $refreshOldSession = false )
    {
        $identifier = $request->identifier ?? v4();

        if( $revokeOldToken )
        {
            AccessToken::revokeOldTokensByName( $identifier );
        }

        $ip = Helper::getUserIP( $request );
        $identifier = Http::getRequestIdentifiers($identifier, 'customer-portal');
        $iPDetails = Http::getIpDetails($request, "access-token", $identifier, "Upon creating taptap customer access-token save customer ip details for future use");
        $agent = new Agent();

        $token = $this->createToken( $identifier,['customer-portal']);
        $token->token->ip = $ip;
        $token->token->country = isset($iPDetails['country_name']) ? $iPDetails['country_name'] : null;
        $token->token->user_agent = $agent->getUserAgent();
        $token->token->save();
        $sessionData = [
            'ip' => $ip,
            'ip_details' => $iPDetails,
            'token' => $token,
            'revokeOldToken' => $revokeOldToken,
            'customer' => $this,
            'user_agent' => $agent->getUserAgent(),
        ];

        CustomerAppSession::createSession($sessionData, $refreshOldSession);

        if(!$revokeOldToken){
            RequestResponseLog::addData( $request, [
                'session_id'      => $token->token->name,
                'customer_id'      => $this->id,
            ]);
        }

        return [
            'token' => $token->accessToken,
            'id' => $token->token->name,
            'session' => $token->token,
        ];
    }
}
