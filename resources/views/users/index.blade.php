@extends('layouts.master')

@section('page_title',env('APP_NAME').' - User Management')
@section('parent_module_breadcrumb_title','User Management')

@section('parent_module_icon','lnr-users')
@section('parent_module_title','Account Management')

@section('has_child_breadcrumb_section', true)
{{--@section('has_child_breadcrumb_actions', true)--}}

@section('child_module_icon','icon-breadcrumb')
@section('child_module_breadcrumb_title','Users')
{{--@section('sub_child_module_icon','icon-breadcrumb')--}}
{{--@section('sub_child_module_breadcrumb_title','Users')--}}

@section('has_child_breadcrumb_actions')
    <div class="page-title-actions">
        <div class="d-inline-block pr-3">
            <button class="btn btn-primary fright listing-btns-wrap clear-pagination-state" type="button"
                    onclick="location.href='{{ URL::to('/users-create') }}'">
                <i class="icon-add"></i>
                <span>Create User</span>
            </button>
        </div>
        <div class="d-inline-block pr-3 actionBtnWrap">
            <div class="dropdown">
                <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    Actions
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item"
                       data-backdrop="static"
                       data-keyboard="false"
                       onClick="App.Users.initializeBulkDelete();"
                       href="#">
                        <i class="fas fa-tags"></i>
                        <span>Delete Selected</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
    {{--<!-- FILTERS VIEW STARTS HERE -->--}}
    @include('users.filters')
    {{--<!-- FILTERS VIEW ENDS HERE -->--}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="users_table" class="table table-hover table-striped table-bordered">
                <thead>
                <tr>
                    <th>S#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>User Type</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @include('users.script')
@endsection


