@extends('layouts.master')

@section('page_title',env('APP_NAME').' - Customer Management')
@section('parent_module_breadcrumb_title','Customers')

@section('parent_module_icon','lnr-license')
@section('parent_module_title','Customer Management')

@section('has_child_breadcrumb_section', true)
{{--@section('has_child_breadcrumb_actions', true)--}}

@section('child_module_icon','icon-breadcrumb')
@section('child_module_breadcrumb_title','Customers')
{{--@section('sub_child_module_icon','icon-breadcrumb')--}}
{{--@section('sub_child_module_breadcrumb_title','Users')--}}

@section('has_child_breadcrumb_actions')
    <div class="page-title-actions">
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
                       onClick="App.Customer.initializeBulkDelete();"
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
    @include('customers.filters')
    {{--<!-- FILTERS VIEW ENDS HERE -->--}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="customers_table" class="table table-hover table-striped table-bordered">
                <thead>
                <tr>
                    <th>S#</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Subscription Status</th>
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
    @include('customers.script')
@endsection


