@extends('layouts.master')
@section('page_title',env('APP_NAME').' - User Management')
@section('parent_module_breadcrumb_title','User Management')

@section('parent_module_icon','lnr-users')
@section('parent_module_title','Account Management')

@section('has_child_breadcrumb_section', true)
{{--@section('has_child_breadcrumb_actions', true)--}}

@section('child_module_icon','icon-breadcrumb')
@section('child_module_breadcrumb_title','Users')
@section('sub_child_module_icon','icon-breadcrumb')
@section('sub_child_module_breadcrumb_title','Create')

@section('has_child_breadcrumb_actions')
@endsection

@section('content')
    <section class="content">
        <div class="box box-default">
            <!-- ALERTS STARTS HERE -->
            <section>
                <div class="row">
                    {{--@include('common.alerts')--}}
                </div>
            </section>
            <!-- ALERTS ENDS HERE -->
            <div class="box-body">
                <!-- /.row -->
                <section id="section1">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form id="user_create_form" class="newFormContainer" method="post" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 formFieldsWrap">

                                        <div class="form-group">
                                            <label>Name *</label>
                                            <input
                                                type="text"
                                                name="name"
                                                maxlength="30"
                                                placeholder="Name"
                                                class="form-control"
                                                value="{{ !empty(old('name')) ? old('name') : (!empty($user->name) ? $user->name : '') }}"
                                                required
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input
                                                    type="email"
                                                    name="email"
                                                    maxlength="100"
                                                    placeholder="Email"
                                                    class="form-control"
                                                    value="{{ !empty(old('email')) ? old('email') : (!empty($user->email) ? $user->email : '') }}"
                                                    required
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                        <div class="form-group">
                                            <label>Password *</label>
                                            <input
                                                    type="password"
                                                    name="password"
                                                    maxlength="100"
                                                    placeholder="*****"
                                                    class="form-control"
                                                    value="{{ !empty(old('password')) ? old('password') : '' }}"
                                                    required
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                        <div class="form-group profileMobileNo">
                                            <label class="col-12">Mobile no *</label>
                                            <input
                                                    type="hidden"
                                                    name="country_code"
                                                    id="create_country_code"
                                            >
                                            <input
                                                    type="tel"
                                                    name="phone"
                                                    oninput="App.Helpers.validatePhoneNumber(this)"
                                                    class="form-control col-12"
                                                    value="{{ !empty(old('phone')) ? old('phone') :  (!empty($user->phone) ? $user->phone : '') }}"
                                                    required
                                                    id="create_phone"
                                            >
                                            <label id="mcc_code_error" class="help-block error"></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                        <div class="form-group switchFromGrp">
                                            <span class="defaultLabel">Status</span>
                                            <div class="custom-control custom-switch product-purchase-checkbox">
                                                <input value="{{ !empty(old('is_active')) ? old('is_active') :  (!empty($user->status) ? $user->status : 1) }}"
                                                       type="checkbox"
                                                       checked="checked"
                                                       name="is_active"
                                                       class="custom-control-input"
                                                       id="chbox_is_active"
                                                />

                                                <label class="custom-control-label"
                                                       for="chbox_is_active"></label>
                                            </div>
                                        </div>
                                    </div>
                                    {{--<div class="col-md-6 form-group d-none">--}}
                                        {{--<div class="form-group switchFromGrp ">--}}
                                            {{--<span class="defaultLabel">Change Password</span>--}}
                                            {{--<div class="custom-control custom-switch product-purchase-checkbox">--}}
                                                {{--<input value="1"--}}
                                                       {{--type="checkbox"--}}
                                                       {{--name="change_password"--}}
                                                       {{--class="custom-control-input"--}}
                                                       {{--id="change_password_checkbox"--}}
                                                       {{--onchange="App.Auth.showPasswordFields()"--}}
                                                {{--/>--}}

                                                {{--<label class="custom-control-label"--}}
                                                       {{--for="change_password_checkbox"></label>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                        {{--<div id="password_section" style="display:none;">--}}
                                            {{--<i class="fas fa-info-circle notice-text text-secondary">&nbsp;You will be logged out of your account after the password has been changed.</i>--}}

                                            {{--<div id="previous_password" class="form-group">--}}
                                                {{--<label>Previous Password *</label>--}}
                                                {{--<input--}}
                                                       {{--type="password"--}}
                                                       {{--name="previous_password"--}}
                                                       {{--class="form-control"--}}
                                                       {{--id="previous_password"--}}
                                                        {{--minLength=6--}}
                                                        {{--maxlength=20--}}
                                                       {{--required--}}
                                                       {{--autocomplete="previous_password"--}}
                                                        {{--placeholder='Enter Previous Password'--}}
                                                {{--/>--}}
                                                {{--<p id="previous_password_error" class="help-block error"></p>--}}
                                            {{--</div>--}}

                                            {{--<div id="password" class="form-group">--}}
                                                {{--<label>Password *<h5 style="padding-left: 10px;padding-top: 10px;">--}}
                                                        {{--<i data-toggle="tooltip"--}}
                                                           {{--data-placement="right"--}}
                                                           {{--title="Your password must be more than 6 characters long, should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 special character."--}}
                                                           {{--class="fas fa-info-circle">--}}
                                                        {{--</i>--}}
                                                    {{--</h5></label>--}}
                                                {{--<input--}}
                                                        {{--type="password"--}}
                                                        {{--name="password"--}}
                                                        {{--class="form-control"--}}
                                                        {{--id="password"--}}
                                                        {{--minLength=6--}}
                                                        {{--maxlength=20--}}
                                                        {{--required--}}
                                                        {{--autocomplete="password"--}}
                                                        {{--placeholder='Enter Password'--}}
                                                {{--/>--}}
                                                {{--<p id="password_error" class="help-block error"></p>--}}
                                            {{--</div>--}}

                                            {{--<div id="password_confirmation" class="form-group">--}}
                                                {{--<label>Confirm Password *</label>--}}
                                                {{--<input--}}
                                                        {{--type="password"--}}
                                                        {{--name="password_confirmation"--}}
                                                        {{--class="form-control"--}}
                                                        {{--id="password_confirmation"--}}
                                                        {{--minLength=6--}}
                                                        {{--maxlength=20--}}
                                                        {{--required--}}
                                                        {{--autocomplete="password_confirmation"--}}
                                                        {{--placeholder='Re Enter Password'--}}
                                                {{--/>--}}
                                                {{--<p id="password_confirmation_error" class="help-block error"></p>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}

                                    <div class="col-md-12 form-group">
                                        <div class="insideButtons">
                                            <button id="create-user" type="button" class="btn btn-primary text-right"><i class="icon-check-thin newMargin"></i>Save</button>
                                        </div>
                                        {{--onclick="App.Auth.saveProfileForm()"--}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <!-- /.box-body -->
    </section>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            App.Users.initializeValidations();
            App.Users.createUserFormBinding();
            App.Helpers.getPhoneInput('create_phone', 'create_country_code', true)
        })
    </script>
@endsection
