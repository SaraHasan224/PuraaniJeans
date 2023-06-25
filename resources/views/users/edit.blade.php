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
@section('sub_child_module_breadcrumb_title','Edit')

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
                            <form id="user_edit_form" class="newFormContainer" method="post" autocomplete="off">
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
                                                    value="{{ !empty(old('name')) ? old('name') : (!empty($user) ? $user->name : '') }}"
                                                    required
                                            >
                                        </div>

                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input
                                                    type="email"
                                                    name="email"
                                                    maxlength="100"
                                                    placeholder="Email"
                                                    class="form-control"
                                                    value="{{ !empty(old('email')) ? old('email') : (!empty($user) ? $user->email : '') }}"
                                                    required
                                            >
                                        </div>

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
                                                    value="{{ !empty(old('phone')) ? old('phone') :  (!empty($user) ? $user->phone : '') }}"
                                                    required
                                                    id="create_phone"
                                            >
                                            <label id="mcc_code_error" class="help-block error"></label>
                                        </div>
                                        <div class="form-group switchFromGrp">
                                            <span class="defaultLabel">Status</span>
                                            <div class="custom-control custom-switch product-purchase-checkbox">
                                                <input value="{{ !empty(old('is_active')) ? old('is_active') :  (!empty($user) ? $user->status : '') }}"
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

                                        <div class="form-group">
                                            <div class="insideButtons">
                                                <button id="edit-user" type="button" class="btn btn-primary"><i class="icon-check-thin newMargin"></i>Save</button>
                                            </div>
                                        </div>

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
            var countryCode = "{{ $user->country_code }}";
            var phoneNumber = "{{ $user->phone_number }}";
            var userId = "{{ $user->id }}";
            $('#create_phone').val(phoneNumber);
            App.Users.editUserFormBinding(userId);
            App.Helpers.getPhoneInput('create_phone', 'create_country_code', true, countryCode, phoneNumber)
        })
    </script>
@endsection
