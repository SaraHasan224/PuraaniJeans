<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @include('layouts.common.meta_tags')
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('assets/logo/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('assets/logo/favicon.png') }}">
    <link rel="stylesheet" href="{{ mix('css/init.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.0.0/sweetalert.min.js"></script>
    <script src="{{ mix('js/admin.js') }}"></script>
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    @yield('css')
</head>
<body>
<div class="loading">
    <div class="spinner"></div>
</div>
<div class="app-container app-theme-white body-tabs-shadow @hasSection('auth_layout') @else fixed-header fixed-sidebar @endif">
    @hasSection('auth_layout')
        @yield('content')
    @else
        @include('layouts.common.header_navigation')
        <div class="app-main">
            @include('layouts.common.sidebar_navigation')
            <div class="app-main__outer">
                <div class="app-main__inner">
                    @include('layouts.common.breadcrumb')
                    @include('layouts.common.alert')
                    @yield('content')
                </div>
                @include('layouts.common.footer')
            </div>
        </div>
    @endif
</div>
@include('common.model.index')
@include('layouts.common.config_mapping')
@yield('scripts')
</body>
</html>