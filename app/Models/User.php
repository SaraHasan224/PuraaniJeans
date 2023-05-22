<?php

namespace App\Models;

// use Illuminate\Contracts\auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

use App\Helpers\Helper;
use App\Helpers\Constant;



//use Laravel\Passport\HasApiTokens; // include this

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    use HasRoles;
    use canResetPassword;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone_number',
        'password',
        'status',
        'user_type',
        'login_attempts'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'last_login',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];


    public static function getValidationRules($type, $params = [])
    {
        $logged_in_user_id = Auth::check() ? auth()->user()->id : null;
        $user_id = isset($params['id']) ? $params['id'] : null;
        $rules = [
            'createUser'        => [
                'name'  => 'required',
                'email' => 'required|email:rfc,dns', //|unique:users,email
                'phone' => [
                    'required',
                    'regex:/^[0-9]+$/'
                ],
                'roles' => 'required',
            ],
            'updateUserProfile' => [
                'name'         => 'required',
                'country_code' => ['required'],
                'email'        => 'required|email:rfc,dns|unique:users,email,' . $logged_in_user_id,
                'phone_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]+$/'
                ],
            ],
            'updateUserPassword' => [
                'current_password' => 'required|max:30',
                'password' => 'required|max:30',
                'password_confirmation' => 'required|same:password',
            ],
            'updateUser'        => [
                'name'     => 'required',
                'email'    => 'required|email:rfc,dns',//|unique:users,email,' . $user_id,
                'phone'    => [
                    'required',
                    'string',
                    'regex:/^[0-9]+$/'
                ],
                'password' => 'same:confirm-password',
                'roles'    => 'required'
            ],
            'signInUser'        => [
                'email'    => ['required', 'max:255'],
                'password' => ['required', 'string', 'min:4', 'max:100'],
            ],
        ];

        return $rules[$type];
    }

    public function updateDetails($data){
        return $this->update($data);
    }

    public static function getUserByEmail($email, $checkIsActive = false)
    {
        $query = self::where('email', $email);
        if ($checkIsActive)
        {
            $query = $query->where('status', Constant::Yes);
        }

        return $query->where(function ($query) {
            $query->where('user_type', '=', Constant::USER_TYPES['Admin']);
        })->first();
    }

    public static function verifyPassword($password){
        $activePassword = self::select('password')->where('status', Constant::Yes)->first();
        if($activePassword){
            if (Hash::check($password, $activePassword->password)) {
                return true;
            }
        }
        return false;
    }


    public static function createSignInOtp($action, $user, $resend = false, $phone = false, $sendSms = false)
    {
        $otp = Otp::createOtp($action, $user, $resend, $phone, $sendSms);

//        if (env('OTP_ENABLED'))
//        {
//            $data = ['otp' => $otp, 'user' => $user];
//            Mail::to($user->email)->send(new UserRegistered($data));
//        }
        return $otp;
    }


    public static function sendOtp($action, $user, $resend = false, $phone = false, $sendSms = false)
    {
//        if (env('OTP_ENABLED'))
//        {
//            $data = ['otp' => $otp, 'user' => $user];
//            Mail::to($user->email)->send(new UserRegistered($data));
//        }
        return true;
    }

    public static function setUsersLoginAttempts($userId, $attempts)
    {
        self::where('id', $userId)->update(['login_attempts' => $attempts]);
    }

    public static function findById($id){
        return self::where('id', $id)->first();
    }

    public static function profileValidate($requestData)
    {
        $messages = [];
        $rules = self::getValidationRules('updateUserProfile');
        $messages["password.regex"] = 'The password format is invalid, your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character.';

        if (isset($requestData['change_password']))
        {
            $rules['password'] = 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/';
            $rules['password_confirmation'] = 'required|min:6';
        }
        return ['rules' => $rules, 'messages' => $messages];
    }
}
