<?php

namespace App\Http\Controllers\Web;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;

use App\Helpers\AppException;
use App\Helpers\Helper;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('users.profile.index');
//        return view('profile.edit', [
//            'user' => $request->user(),
//        ]);
    }

    /**
     * Update the user's profile information.
     */
//    public function update(ProfileUpdateRequest $request): RedirectResponse
    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();
            #Data clean up
            $phoneNumber = $requestData['phone_number'];
            $countryCode = $requestData['country_code'];

            if (!empty($phoneNumber)) {
                if ($countryCode == Constant::LocalCountryCode && substr($phoneNumber, 0, 1) == '0') {
                    $phoneNumber = substr($phoneNumber, 1);
                }
            }
            $requestData['phone_number'] = Helper::clean($phoneNumber);

            #Apply validation rule
            $validationErrors = Helper::validationErrors(
                $requestData,
                User::getValidationRules('updateUserProfile', $requestData)
            );
            if ($validationErrors)
            {
                return redirect()->back()->withErrors($validationErrors)->withInput()->with('status', 'error');
            }

            $user = User::findById(Auth::user()->id);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->updateDetails([
                'name' => $requestData['name'],
                'email' => $requestData['email'],
                'phone_number' => $requestData['phone_number'],
                'country_code' => $requestData['country_code'],
            ]);
            $user->save();
            DB::commit();
            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            AppException::log($e);
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
