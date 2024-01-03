@extends('layouts.master')

@section('auth_layout', true)
@section('content')
<div class="app-container app-theme-white body-tabs-shadow">
    <div class="app-container">
        <div class="h-100">
            <div class="h-100 no-gutters row">
                <div class="d-lg-block col-lg-4">
                    @include('auth.common.slider')
                </div>
                <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
                    <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                        <div class="app-logo"></div>
                        <h4 class="mb-0">
                            <span class="d-block">Welcome back,</span>
                            <span>Please sign in to your account.</span></h4>
                        <div class="divider row"></div>
                        <div>
                            <form method="POST" id="form-signin" autoComplete="off" action="{{ route('register') }}">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-12">
                                        @if(session('status'))
                                            <div class="col-12 alert alert-success">
                                                <strong></strong> {{ session('status') }}
                                            </div>
                                        @endif
                                        @if(session('error'))
                                            <div class="col-12 alert alert-danger">
                                                <strong></strong> {{ session('error') }}
                                            </div>
                                        @endif
                                        @if (count($errors) > 0)
                                            <div class="col-12 alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword" class="">
                                                <span class="text-danger">*</span> Name
                                            </label>
                                            <input
                                                    name="name"
                                                    id="name"
                                                    placeholder="Enter your name"
                                                    type="text"
                                                    class="form-control"
                                                    maxlength="100"
                                                    autofocus="true"
                                                    value="{{request()->get('name')  ?? session('name') ?? old('name')}}"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword" class="">
                                                <span class="text-danger">*</span> Email
                                            </label>
                                            <input
                                                name="email"
                                                id="email"
                                                placeholder="Enter your email"
                                                type="email"
                                                class="form-control"
                                                maxlength="100"
                                                value="{{request()->get('email')  ?? session('email') ?? old('email')}}"
                                            >
                                            <p id="email_error" class="help-block error"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword" class="">
                                                <span class="text-danger">*</span> Password
                                            </label>
                                            <div class="pRelative">
                                                <input
                                                        name="password"
                                                        id="password"
                                                        placeholder="••••••••••••••"
                                                        type="password"
                                                        class="form-control pwdField"
                                                        required
                                                        autocomplete="new-password"
                                                        minLength="6"
                                                        maxlength="20"
                                                >
                                                <span id="toggle_pwd" class="fas fa-fw fa-eye pwd-icon"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePasswordRep" class=""><span class="text-danger">*</span> Repeat Password</label>
                                            <div class="pRelative">
                                                <input
                                                        name="password_confirmation"
                                                        id="password_confirmation"
                                                        placeholder="••••••••••••••"
                                                        type="password"
                                                        class="form-control pwdField"
                                                        required
                                                        autocomplete="new-password"
                                                        minLength="6"
                                                        maxlength="20"
                                                >
                                                <span id="toggle_pwd" class="fas fa-fw fa-eye pwd-icon"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="position-relative form-check">
                                    <input name="check" id="exampleCheck" type="checkbox" class="form-check-input">
                                    <label for="exampleCheck" class="form-check-label">Keep me logged in</label>
                                </div>
                                <div class="divider row"></div>
                                <div class="d-flex align-items-center">
                                    <h6  class="mt-2">
                                        {{ __('Already have an account?') }} <a href="{{ route('login') }}" class="btn-lg btn btn-link"> {{ __('Sign In?') }}</a>
                                    </h6>
                                    <div class="ml-auto">
                                        <button class="btn btn-primary btn-lg" type="submit">{{ __('Create Account') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection