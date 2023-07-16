@extends('layouts.master')

@section('page_title',env('APP_NAME').' - Closet Management')
@section('parent_module_breadcrumb_title','Closets')

@section('parent_module_icon','lnr-license')
@section('parent_module_title','Manage Website')

@section('has_child_breadcrumb_section', true)
{{--@section('has_child_breadcrumb_actions', true)--}}

@section('parent_module_breadcrumb_title','Application')
@section('child_module_icon','icon-breadcrumb')
@section('child_module_breadcrumb_title','Closet Management')
@section('sub_child_module_icon','icon-breadcrumb')
@section('sub_child_module_breadcrumb_title','Orders')

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
    @include('closet.closet.filters')
    {{--<!-- FILTERS VIEW ENDS HERE -->--}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <table style="width: 100%;" id="closet_table" class="table table-hover table-striped table-bordered">
                <thead>
                <tr>
                    <th>S#</th>
                    <th>Name</th>
                    {{--<th>Customer</th>--}}
                    <th>Ref</th>
                    <th>Logo</th>
                    <th>Banner</th>
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
    @include('closet.closet.script')
@endsection


