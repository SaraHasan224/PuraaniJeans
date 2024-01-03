@extends('layouts.master')

@section('page_title',env('APP_NAME').' - Account Management')
@section('parent_module_breadcrumb_title','My Profile')

@section('parent_module_icon','lnr-users')
@section('parent_module_title','My Profile')

{{--@section('has_child_breadcrumb_section', true)--}}
{{--@section('has_child_breadcrumb_actions', true)--}}

{{--@section('child_module_icon','icon-breadcrumb')--}}
{{--@section('child_module_breadcrumb_title','Users')--}}
{{--@section('sub_child_module_icon','icon-breadcrumb')--}}
{{--@section('sub_child_module_breadcrumb_title','Users')--}}

@section('content')
    <div class="app-inner-layout">
        <div class="app-inner-layout__wrapper">
            <div class="app-inner-layout__content main-card mb-3  card">
                @include('users.profile.common.profile')
            </div>
            <div class="app-inner-layout__sidebar card">
                @include('users.profile.common.sidebar')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            App.Helpers.getPhoneInput('profile_phone', 'profile_country_code', true)
        });
    </script>
@endsection