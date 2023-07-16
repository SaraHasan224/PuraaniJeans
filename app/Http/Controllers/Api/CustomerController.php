<?php
/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 6/28/2023
 * Time: 12:05 AM
 */

namespace App\Http\Controllers\Api;


use App\Helpers\Constant;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Http\Request;
use function Ramsey\Uuid\v4;

class CustomerController
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
        ]);

        $country = Country::getCountryByCountryCode("PK");
        $user = Customer::create([
            'first_name' => 'Sara',
            'last_name' => 'Hasan',
            'username' => 'Sara.hasan',
            'email' => 'sarahasan224@gmail.com',
            'country_code' => "92",
            'phone_number' => "3452099689",
            'country_id' => $country->id,
            'status' => Constant::Yes,
            'subscription_status' => Constant::Yes,
            'identifier' => v4(),
            'login_attempts' => Constant::No,
        ]);

        $token = $user->createToken('API Token')->accessToken;

        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details. 
            Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return response(['user' => auth()->user(), 'token' => $token]);

    }

}