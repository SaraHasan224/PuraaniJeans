<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @include('layouts.common.meta_tags')
    @yield('header_meta')
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ mix('css/master.css') }}">
    <script src="{{ mix('/js/master.js') }}"></script>
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

<div class="app-container app-theme-white body-tabs-shadow fixed-header fixed-sidebar">
    @include('layouts.common.header_navigation')
    <div class="app-main">
        @include('layouts.common.sidebar_navigation')
        <div class="app-main__outer">
            <div class="app-main__inner">
                @include('layouts.common.breadcrumb')
                @yield('content')
            </div>
            @include('layouts.common.footer')
        </div>
    </div>
</div>
</body>
</html>