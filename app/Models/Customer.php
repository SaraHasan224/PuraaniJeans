<?php

namespace App\Models;

// use Illuminate\Contracts\auth\MustVerifyEmail;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens; // include this

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
        'name',
        'email',
        'password',
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

    public static function findById($id){
        return self::where('id', $id)->first();
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
}
