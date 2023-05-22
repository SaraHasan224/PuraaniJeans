<?php

namespace App\Http\Controllers\Web\Auth;

use App\Helpers\ApiResponseHandler;
use App\Helpers\AppException;
use App\Helpers\Constant;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        try{
            if (Auth::check())
            {
                return redirect('/');
            }
            return view('auth.login.index');
        }catch (\Exception $e){
            AppException::log($e);
            return ApiResponseHandler::failure(__('messages.general.failed'), $e->getMessage());
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $formData = $request->all();
        $validationErrors = Helper::validationErrors(
            $formData,
            User::getValidationRules('signInUser', $formData)
        );
        if ($validationErrors)
        {
            // return back if email and password not provided.
            return redirect()->back()->withErrors($validationErrors)->withInput();
        }

        $authenticated = $request->authenticate();
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user)
        {
            if ($user->user_type != Constant::USER_TYPES['Admin'])
            {
                return redirect()->back()->with('error', __('messages.user.invalid_access_forbidden'))->withInput();
            }
            // $otp = User::createSignInOtp('login', $user);
            User::setUsersLoginAttempts($user->id, 0);
            //Sms: Admin Sign in
            //User::sendOtp($user, $otp, 'signin');
            //Email: Admin Sign in
            //EmailHandler::merchantSignin_upEmail($user, $otp, 'signin');
            return redirect()->route('home');
        }
        else
        {
            return redirect()->back()->with('error', $user)->withInput();
            return $user;
            if ($user)
            {
                $userStatuses = array_flip(Constant::USER_STATUS);
                if ($this->__checkLoginAttempts($user['id']))
                {
                    $updateResponse = User::updateUserStatus(['id' => $user['id'], 'account_status' => $userStatuses['Inactive']]);
                    if ($updateResponse)
                    {
                        EmailHandler::userBlockEmail($formData['email']);
                        User::setUsersLoginAttempts($user['id'], 0);
                    }
                    return redirect()->back()->with('error', __('messages.user.login_attempts_expired'))->withInput();
                }
                return redirect()->back()->with('error', __('messages.password.remaining_attempts') . self::getRemainingLoginAttempts($user['id']))->withInput();
            }
            return redirect()->back()->with('error', __('messages.user.email_not_exists'))->withInput();
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
