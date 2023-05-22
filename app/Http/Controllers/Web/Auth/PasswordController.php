<?php

namespace App\Http\Controllers\Web\Auth;

use App\Helpers\AppException;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)//: RedirectResponse
    {
        DB::beginTransaction();
        try {
            $requestData = $request->all();

//            $validated = $request->validateWithBag('updatePassword', [
//
//                'current_password' => ['required', 'current_password'],
//                'password' => ['required', Password::defaults(), 'confirmed'],
//            ]);

            #Apply validation rule
            $validationErrors = Helper::validationErrors(
                $requestData,
                User::getValidationRules('updateUserPassword', $requestData)
            );
            if ($validationErrors)
            {
                return redirect()->back()->withErrors($validationErrors)->withInput()->with('status', 'error');
            }

            $user = User::findById(Auth::user()->id);

            $user->update([
                'password' => Hash::make($requestData['password']),
            ]);
            $user->save();
            DB::commit();
            return back()->with('status', 'password-updated');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            AppException::log($e);
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

    }


    /**
     * Update the user's password.
     */
    public static function updatePassword(Request $request)
    {
        $user = auth()->user();
        $userId = Auth::user()->id;
        $formData = $request->all();
        $validation = User::profileValidate($formData);

        if ($countryCode == Constant::LocalCountryCode && substr($phoneNumber, 0, 1) == '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        $formData['phone_number'] = isset($formData['phone_number']) ? str_replace("-", "", $request->phone_number) : null;
        $validationErrors = Helper::validationErrors($formData, $validation['rules'], $validation['messages']);

        if ($validationErrors)
        {
            return ResponseHandler::validationError($validationErrors);
        }

        if ($this->checkIfUserExistsWithSameData($formData, 'phone', $user->id))
        {
            return ResponseHandler::validationError([__('messages.user.phone_taken')]);
        }

        $request->phone = isset($request->phone) ? str_replace("-", "", $request->phone) : null;
        $user->fill([
            'name' => $formData['name']
        ]);
        $user->save();

        if (isset($formData['change_password']))
        {
            if (!UserPasswords::verifyPassword(auth()->user()->id, $formData['previous_password']))
            {
                return ResponseHandler::validationError([__('messages.password.incorrect_previous_password')]);
            }

            if (!UserPasswords::isPasswordUnique(auth()->user()->id, $formData['password']))
            {
                return ResponseHandler::validationError([__('messages.password.is_not_unique')]);
            }

            UserPasswords::updateProfilePassword(auth()->user()->id, Hash::make($formData['password']));
        }

        HubspotService::updateHubspotContact($user);
        // Log Activity
        ActivityLogHandler::log(null, $user, 'profile_update  ', 'user', 'update');

        return ResponseHandler::success([], __('messages.user.profile_update'));
    }

}
