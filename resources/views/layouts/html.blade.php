<!doctype html>
<html lang="{{ app()->getLocale() }}">

@php
    $backgrounds = Illuminate\Support\Facades\Storage::disk('public')->files('login-backgrounds');
    $background = 'storage/' . $backgrounds[array_rand($backgrounds)];
@endphp


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
      window.Laravel = {csrfToken: '{{csrf_token()}}'}
    </script>

    <title>{{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css" rel="stylesheet"
          type="text/css">
    <style src="../node_modules/handsontable/dist/handsontable.full.css"></style>
    <link rel="stylesheet" href="https://unpkg.com/vue-nav-tabs/themes/vue-tabs.css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Oxygen:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic|Material+Icons">
{{--    <link rel="stylesheet" href="https://unpkg.com/vue-material/dist/vue-material.min.css">--}}
{{--    <link rel="stylesheet" href="https://unpkg.com/vue-material/dist/theme/default.css">--}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style type="text/css">
        body {
            background: url({{ $background }}) no-repeat;
        }
    </style>

</head>

<body class="{{ !empty($body_class) ? $body_class : '' }}">

{{--@includeWhen(Auth::user(), 'layouts.sidebar-nav')--}}
@yield('sidebar')

<div id="app" class="pusher">
    <div class="page__header">
{{--        @includeWhen(Auth::user(), 'layouts.main-nav')--}}
    </div>


    <div class="page__main {{ !empty($page_main_class) ? $page_main_class : '' }}">


        <div class="page__status">
{{--            <flash-message class="flash-message"></flash-message>--}}

{{--            @if($errors->any())--}}
{{--                {{ implode('', $errors->all('<div>:message</div>')) }}--}}
{{--            @endif--}}

        </div>

        <div class="{{ !empty($errors->bag) ? 'has-flash-message' : '' }} page__content  {{ !empty($page_content_class) ? $page_content_class : '' }}">
            @hasSection('title')
                <div class="page__content-heading">
                    <h1 class="title"> @yield('title') </h1>
                    @hasSection('title_body')
                        <blockquote class="blockquote body">
                            @yield('title_body')
                        </blockquote>
                    @endif
                </div>
            @endif

            @yield('content')
        </div>
    </div>


{{--    @if (Auth::check())--}}
{{--        <div class="page__footer">--}}
{{--            <span>Chat Horizon Copyright {{ now()->year }} </span>--}}
{{--        </div>--}}
{{--    @endif--}}

</div>

<script type="application/javascript" src="{{ asset('js/manifest.js') }}"></script>
<script type="application/javascript" src="{{ asset('js/vendor.js') }}"></script>
<script type="application/javascript" src="{{asset('js/app.js')}}"></script>

</body>
</html>
