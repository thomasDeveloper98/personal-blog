{{-- Title --}}
<title>{{ config('app.name') }} - @yield('page_title') </title>

{{-- Meta --}}
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@yield('site_meta')

{{-- App stylesheets --}}
<link rel="stylesheet" href="{{ url('/css/app.css') }}">
@yield('head_styles')

{{-- App JS --}}
@yield('head_scripts')