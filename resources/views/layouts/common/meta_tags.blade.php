<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Language" content="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>
    @hasSection('page_title')
        @yield('page_title')
    @else
        {{env('APP_NAME')}} - {{ env('APP_SUB_TITLE') }}
    @endif
</title>
<meta name="title" content="@hasSection('page_title')
@yield('page_title')
@else
{{env('APP_NAME')}} - {{ env('APP_SUB_TITLE') }}
@endif">
<meta name="description" content="">
<meta name="keywords"content=""/>

<meta property="og:title" content="@hasSection('page_title')
@yield('page_title')
@else
{{env('APP_NAME')}} - {{ env('APP_SUB_TITLE') }}
@endif"/>
<meta property="og:description" content=""/>
<meta property="og:image" content="https://uploads-ssl.webflow.com/5eb3d61588ad6f7406a7d827/5f4dfce886232cf2265d5212_Open%20Graph%20Image.jpg"/>

@yield('header_meta')