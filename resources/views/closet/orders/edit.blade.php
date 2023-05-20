@extends('layouts.admin')
@if($isAdmin)
    @section('parentModuleTitle','User Management')
    @section('parentModuleIcon','icon-breadcrumb')
    @section('parentModule')
        <li><a href="{{URL::to('users')}}"><i class="@yield('parentModuleIcon')"></i> <span>@yield('parentModuleTitle')</span></a></li>
    @endsection

    @section('childModuleTitle','Edit Users')
    @section('childModuleIcon','icon-breadcrumb')
    @section('childModule')
        <li class="active"><span><i class="@yield('childModuleIcon')"></i> @yield('childModuleTitle')</span></li>
    @endsection

    @section('currentModuleTitle','Edit Users')
@else
    @section('backButtonPlacement')
        <li>
            <a href="{{URL::to('users')}}" class="backButton">
                <i class="icon-back-arrow"></i>
            </a>
        </li>
    @endsection
    @section('parentModuleTitle','Global Settings')
    @section('parentModuleIcon','icon-breadcrumb')
    @section('parentModule')
        <li><a href="{{route('settings.view',$userId)}}"><i class="@yield('parentModuleIcon')"></i> <span>@yield('parentModuleTitle')</span></a></li>
    @endsection

    @section('childModuleTitle','Users')
    @section('childModuleIcon','icon-breadcrumb')
    @section('childModule')
        <li><a href="{{URL::to('users')}}"><i class="@yield('childModuleIcon')"></i> <span>@yield('childModuleTitle')</span></a></li>
    @endsection

    @section('secondChildModuleTitle','Edit User')
    @section('secondChildModuleIcon','icon-breadcrumb')
    @section('secondChildModule')
        <li class="active"><span><i class="@yield('secondChildModuleIcon')"></i> @yield('secondChildModuleTitle')</span></li>
    @endsection
@endif
@section('currentModuleTitle','Edit User')

@section('header_title_right')
@endsection
@section('content')
    <section class="content">
        <div class="box box-default">

            <!-- ALERTS STARTS HERE -->
            <section>
                <div class="row">
                    @include('common.alerts')
                </div>
            </section>
            <!-- ALERTS ENDS HERE -->

            <div class="box-body">

                <section id="section1">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            {!! Form::model($user, [ 'id'=> 'user_edit_form', 'class'=> 'newFormContainer' ]) !!}
                            <div class="row">
                                <div class="col-md-6 formFieldsWrap">
                                    <div class="form-group">
                                        <label>Name *</label>
                                        {!! Form::hidden('id', $user->id, array('class' => 'form-control')) !!}
                                        {!! Form::text('name', null, array('maxlength' => '30', 'placeholder' => 'Name','class' => 'form-control' ,'required' => 'required', $readOnly ?  'readonly' : '' )) !!}
                                    </div>

                                    <div class="form-group">
                                        <label>Email *</label>
                                        {!! Form::email('email', null, array('placeholder' => 'Email','maxLength' => '100', 'class' => 'form-control', 'required' => 'required', $readOnly ?  'readonly' : '')) !!}
                                    </div>

                                    <div class="form-group profileMobileNo">
                                        <label>Mobile no *</label>
                                        <input type="hidden" name="country_code"  value="{{$user->country_code}}" id="edit_country_code">
                                        <input class="form-control" value="{{$user->phone}}" type="tel" name="phone" oninput="App.Helpers.validatePhoneNumber(this)" required id="edit_phone" {{$readOnly ?  'readonly' : ''}}>
                                        <p id="mcc_code_error" class="help-block error"></p>
                                    </div>

                                    @foreach ($userRole as $key => $value)
                                        @if (in_array($value, $roles))
                                            <div class="form-group">
                                                <label>Role</label>
                                                <div class="filterSelect">
                                                    <div class="select2Wrap">
                                                        @if($readOnly)
                                                            <input type="hidden" name="roles" value="{{$key}}"  >
                                                        @endif
                                                        <select @if($readOnly) disabled="disabled" @endif name="roles" onchange="App.Users.isGlobalRole()" class="form-control select2" required="required" >
                                                            @if($readOnly)
                                                                <option selected="selected" value="{{$key}}" >{{$key}}</option>
                                                            @else
                                                                @foreach($roles as $key => $role)
                                                                    <option @if(isset($userRole[$key]) && $role == $userRole[$key]) selected="selected" @endif value="{{$key}}" is_global="{{$role}}">{{  str_replace( config('permission.merchant_prefix').($merchantId), '', $key) }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" name="roles" value="{{$value}}">
                                        @endif
                                    @endforeach

                                    <div class="form-group switchFromGrp">
                                        <span class="defaultLabel">Status</span>
                                        <div class="custom-control custom-switch product-purchase-checkbox">
                                            <input value="1"
                                                   type="checkbox"
                                                   @if($user->is_active)
                                                   checked="checked"
                                                   @endif
                                                   name="is_active"
                                                   class="custom-control-input"
                                                   id="chbox_is_active"
                                            />

                                            <label class="custom-control-label"
                                                   for="chbox_is_active"></label>
                                        </div>
                                    </div>

                                    @if($isMerchant)
                                        <div class="form-group switchFromGrp">
                                            <span class="defaultLabel">Select All Store</span>
                                            <div class="custom-control custom-switch product-purchase-checkbox">
                                                <input value="0"
                                                       type="checkbox"
                                                       @if($user->has_permission_to_all_stores == \App\Helpers\Constant::Yes)
                                                        checked="checked"
                                                       @endif
                                                       name="permission_to_all_stores_checkbox"
                                                       class="custom-control-input"
                                                       id="permission_to_all_stores_checkbox"
                                                       onclick="App.Users.permissionToAllStoreCheckbox(this)"
                                                />

                                                <label class="custom-control-label"
                                                       for="permission_to_all_stores_checkbox"></label>
                                            </div>
                                        </div>
                                        <input type="hidden" name="has_permission_to_all_stores" id="has_permission_to_all_stores" value="{{$user->has_permission_to_all_stores}}">


                                        <label>Select Store </label>
                                        <div class="select2-blue">
                                            <select class="select2" search="true" multiple="multiple" name="merchant_stores[]" id="merchant_stores"
                                                    data-placeholder="Select Store"
                                                    onchange="App.Users.merchantStoreSelection(this)"
                                                    style="width: 100%;">
                                                    @foreach($merchantStores as $key => $val)
                                                        <option value="{{$key}}" {{ in_array($key , $merchantUserStores) ? 'selected' : '' }} >{{$val}}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        
                                    @endif

                                    <div class="form-group">
                                        <input type="hidden" id="previous_url" value="{{URL::previous()}}">
                                        <div class="insideButtons">
                                            <button id="edit-user" type="button" class="btn btn-primary"><i class="icon-check-thin newMargin"></i>Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <!-- /.box-body -->
                </section>
            </div>
        </div>
    </section>
@endsection
@include('users.script')