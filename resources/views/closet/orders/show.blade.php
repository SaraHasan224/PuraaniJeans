@extends('layouts.admin')

@section('parentModuleTitle','User Management')
@section('parentModuleIcon','icon-breadcrumb')
@section('parentModule')
    <li><i class="@yield('parentModuleIcon')"></i> @yield('parentModuleTitle')</li>
@endsection

@section('childModuleTitle','User Details')
@section('childModuleIcon','icon-breadcrumb')
@section('childModule')
    <li class="active"><span><i class="@yield('childModuleIcon')"></i> @yield('childModuleTitle')</span></li>
@endsection

@section('currentModuleTitle','User Details')

@section('header_title_right')
@endsection

@section('content')
    <section class="content">
        <div class="box box-default">
            {{--<div class="box-header with-border">--}}
                {{--<h3 class="box-title"> @yield('childModuleTitle')</h3>--}}
            {{--</div>--}}
            <div class="box-body">
                <!-- /.row -->
                <section id="section1">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="lead">User Information</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {{ $user->name ?? '' }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                {{ $user->email ?? '' }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Mobile No:</strong>
                                +{{ $user->country_code ?? '' }}{{ $user->phone ?? '' }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Roles:</strong>
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $v)
                                        <label class="badge badge-success">{{ $v }}</label>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <!-- /.box-body -->
        </div>
    </section>
@endsection
