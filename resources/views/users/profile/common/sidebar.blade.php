<div class="card-shadow-primary card-border mb-3 card">
    <div class="dropdown-menu-header">
        <div class="dropdown-menu-header-inner bg-primary">
            <div class="menu-header-image"
                 style="background-image: url('images/dropdown-header/city2.jpg');"></div>
            <div class="menu-header-content">
                <div class="avatar-icon-wrapper avatar-icon-lg">
                    <div class="avatar-icon rounded btn-hover-shine"><img
                                src="/images/avatars/12.jpg"
                                alt="Avatar 5"></div>
                </div>
                <div><h5 class="menu-header-title">{{ \Illuminate\Support\Facades\Auth::user()->name }}</h5></div>
            </div>
        </div>
    </div>
    <div class="scroll-area-sm">
        <div class="scrollbar-container">
            <ul class="list-group list-group-flush">

            </ul>
        </div>
    </div>
    <?php
      $authId = \Illuminate\Support\Facades\Auth::user()->id;
    ?>
    <div class="text-center d-block card-footer">
        <button
            type="button"
            onclick="App.UserProfile.deleteAccount(this)"
            {{--text="Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain."--}}
            {{--title="Delete my account"--}}
            {{--submitTheme="btn-danger submitModelSuccess"--}}
            {{--submitText="Delete my account"--}}
            authId="{{$authId}}"
            class="mr-2 border-0 btn-transition btn btn-outline-danger"
            {{--data-toggle="modal" data-target=".view-stores"--}}
            {{--data-toggle="modal"--}}
            {{--data-target="#customModalWrapper"--}}
        >
            {{ __('Delete my account') }}
        </button>
        {{--<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>--}}
            {{--<form method="post" action="{{ route('profile.destroy') }}" class="p-6">--}}
                {{--@csrf--}}
                {{--@method('delete')--}}

                {{--<h2 class="text-lg font-medium text-gray-900">--}}
                    {{--{{ __('Are you sure you want to delete your account?') }}--}}
                {{--</h2>--}}

                {{--<p class="mt-1 text-sm text-gray-600">--}}
                    {{--{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}--}}
                {{--</p>--}}

                {{--<div class="mt-6">--}}
                    {{--<x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />--}}

                    {{--<x-text-input--}}
                            {{--id="password"--}}
                            {{--name="password"--}}
                            {{--type="password"--}}
                            {{--class="mt-1 block w-3/4"--}}
                            {{--placeholder="{{ __('Password') }}"--}}
                    {{--/>--}}

                    {{--<x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />--}}
                {{--</div>--}}

                {{--<div class="mt-6 flex justify-end">--}}
                    {{--<x-secondary-button x-on:click="$dispatch('close')">--}}
                        {{--{{ __('Cancel') }}--}}
                    {{--</x-secondary-button>--}}

                    {{--<x-danger-button class="ml-3">--}}
                        {{--{{ __('Delete Account') }}--}}
                    {{--</x-danger-button>--}}
                {{--</div>--}}
            {{--</form>--}}
        {{--</x-modal>--}}
        <br/>
        <i>
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}

        </i>
    </div>
</div>