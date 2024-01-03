<?php

namespace App\Http\Controllers\Web\Auth;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register.index');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try{

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
//            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'country_code' => '92',
                'phone_number' => '0900786015',
                'password' => Hash::make($request->password),
                'status' => Constant::Yes,
                'user_type' => Constant::USER_TYPES['Admin'],
                'login_attempts' => Constant::No,
            ]);
            $role = Role::where('type_id', Constant::USER_TYPES['Admin'])->first();
//            $user->assignRole([$role->id]);

//            event(new Registered($user));
            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
        }catch (\Exception $e){
            dd($e->getMessage());
            AppException::log($e);
            return redirect("/");
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }
}
