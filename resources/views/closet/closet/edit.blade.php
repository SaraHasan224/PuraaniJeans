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
                {{--<div class="main-card mb-3 card">--}}
                <div class="card-header">
                    @if(!empty($data['closet']) && \App\Helpers\Helper::isImageValid($data['closet']['logo']))
                        <img class="card-closet-logo" src="{{$data['closet']->logo}}"/>
                    @else
                        <i class="header-icon lnr-license icon-gradient bg-plum-plate"> </i>
                    @endif
                        {{$data['closet']->closet_name}}
                        <div class="btn-actions-pane-right">
                        <div class="nav">
                            <a data-toggle="tab" href="#tab-eg2-0"
                               class="btn-pill btn-wide active btn btn-outline-alternate btn-sm">Closet</a>
                            <a data-toggle="tab" href="#tab-eg2-1" id="productInventory"
                               class="btn-pill btn-wide btn btn-outline-alternate btn-sm">Product Inventory</a>
                            <a data-toggle="tab" href="#tab-eg2-2" id="customers"
                               class="btn-pill btn-wide mr-1 ml-1  btn btn-outline-alternate btn-sm">Customers</a>
                            <a data-toggle="tab" href="#tab-eg2-3" id="orders"
                               class="btn-pill btn-wide  btn btn-outline-alternate btn-sm">Orders</a>
                        </div>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-eg2-0" role="tabpanel">
                        @include('closet.closet.tabs.closet')
                    </div>
                    <div class="tab-pane" id="tab-eg2-1" role="tabpanel">
                        @include('closet.closet.tabs.products', [
                            'closet_ref' => $data['closet']->closet_reference
                        ])
                    </div>
                    <div class="tab-pane" id="tab-eg2-2" role="tabpanel">
                        @include('closet.closet.tabs.customers')
                    </div>
                    <div class="tab-pane" id="tab-eg2-3" role="tabpanel">
                        @include('closet.closet.tabs.orders')
                    </div>
                </div>
                {{--</div>--}}
            </div>
        </div>
        <!-- /.box-body -->
    </section>
@endsection
@section('scripts')
    <script>
        const closet_ref = "<?php echo $data['closet']->closet_reference ?>";
        document.getElementById("productInventory").onclick = function () {
            App.Closet.initializeClosetProductsDataTable(closet_ref);
        }
        document.getElementById("customers").onclick = function () {
            App.Closet.initializeClosetCustomerDataTable(closet_ref);
        }
        document.getElementById("orders").onclick = function () {
            App.Closet.initializeClosetOrdersDataTable(closet_ref);
        }
    </script>
@endsection
