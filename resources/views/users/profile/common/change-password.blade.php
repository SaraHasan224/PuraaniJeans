<form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('put')
    <div class="position-relative form-group">
        <label for="exampleEmail" class="">{{ __('Current Password') }}</label>
        <input
            id="current_password"
            name="current_password"
            type="password"
            class="form-control mt-1 block w-full"
            autocomplete="current-password"
            autofocus
            required
        >
        @if ($errors->get('current_password'))
            <div class="mt-1 text-red-500 text-sm">{{ $errors->get('current_password') }}</div>
        @endif
    </div>
    <div class="position-relative form-group">
        <label for="examplePassword" class="">{{ __('New Password') }}</label>
        <input
            id="password"
            name="password"
            type="password"
            class="form-control mt-1 block w-full"
            autocomplete="new-password"
            required
        >
        @if ($errors->get('password'))
            <div class="mt-1 text-red-500 text-sm">{{ $errors->get('password') }}</div>
        @endif
    </div>
    <div class="position-relative form-group">
        <label for="examplePassword" class="">{{ __('Confirm Password') }}</label>
        <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            class="form-control mt-1 block w-full"
            autocomplete="new-password"
            required
        >
        @if ($errors->get('password_confirmation'))
            <div class="mt-1 text-red-500 text-sm">{{ $errors->get('password_confirmation') }}</div>
        @endif
    </div>
    <div class="flex items-center gap-4">
        <button class="mt-1 btn btn-primary" type="submit">{{ __('Save') }}</button>
        @if (session('status') === 'password-updated')
        <p
                {{--x-data="{ show: true }"--}}
                {{--x-show="show"--}}
                {{--x-transition--}}
                {{--x-init="setTimeout(() => show = false, 2000)"--}}
                class="text-sm text-gray-600"
        >{{ __('Saved.') }}</p>
        @endif
    </div>

</form>