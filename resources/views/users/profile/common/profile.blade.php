<div class="card-body">
    <h5 class="card-title">{{ __('Profile Information') }}</h5>
    <h6 class="text-left mt-2">
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </h6>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    @include('users.profile.common.profile-info')

    <h5 class="card-title mt-5">{{ __('Update Password') }}</h5>
    <h6 class="text-left mt-2">
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Ensure your account is using a long, random password to stay secure.") }}
        </p>
    </h6>
    @include('users.profile.common.change-password')
</div>