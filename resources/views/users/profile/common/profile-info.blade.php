
<form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('patch')
    <div class="position-relative form-group">
        <label for="exampleEmail" class="">{{ __('Name') }}</label>
        <input
                id="name"
                name="name"
                placeholder="with a placeholder"
                type="text"
                value="{{ old('name', \Illuminate\Support\Facades\Auth::user()->name) }}"
                class="form-control mt-1 block w-full"
                autofocus
                autocomplete="name"
                required
        >
        @if ($errors->get('name'))
            <div class="mt-1 text-red-500 text-sm">{{ $errors->get('name') }}</div>
        @endif
    </div>
    <div class="position-relative form-group">
        <label for="examplePassword" class="">{{ __('Email') }}</label>
        <input
                name="email"
                id="email"
                placeholder="password placeholder"
                type="email"
                value="{{old('email', \Illuminate\Support\Facades\Auth::user()->email)}}"
                class="form-control mt-1 block w-full"
                autocomplete="username"
                required
        >
        @if ($errors->get('email'))
            <div class="mt-1 text-red-500 text-sm">{{ $errors->get('email') }}</div>
        @endif
        @if (\Illuminate\Support\Facades\Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! \Illuminate\Support\Facades\Auth::user()->hasVerifiedEmail())
            <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif
    </div>
    <div class="position-relative form-group profileMobileNo">
        <label class="col-12">Mobile no *</label>
        <input
            type="hidden"
            name="country_code"
            id="profile_country_code"
            value="{{ old('country_code', \App\Helpers\Helper::clean(\Illuminate\Support\Facades\Auth::user()->country_code)) }}"
        >
        <input
            class="form-control"
            type="tel"
            name="phone_number"
            id="profile_phone"
            value="{{ old('phone_number', \App\Helpers\Helper::clean(\Illuminate\Support\Facades\Auth::user()->phone_number)) }}"
        >
        <p id="mcc_code_error" class="help-block error"></p>
    </div>
    <div class="flex items-center gap-4">
        @if (session('status') !== 'profile-updated')
            <button class="mt-1 btn btn-primary" type="submit">{{ __('Save') }}</button>
        @endif
        @if (session('status') === 'profile-updated')
            <button
                {{--x-data="{ show: true }"--}}
                {{--x-show="show"--}}
                {{--x-transition--}}
                {{--x-init="setTimeout(() => show = false, 2000)"--}}
                class="mt-1 btn btn-success text-sm text-gray-600"
                type="submit"
            >
                {{ __('Saved') }}
            </button>
        @endif
    </div>

</form>