@extends ('layouts.html', [
  'body_class' => 'login-page with-background',
  'page_content_class' => 'no-sidebar',
])
@php
    $images = Illuminate\Support\Facades\Storage::disk('public')->files('login-advertisements');
    $duplicateUserMessage = request()->session()->pull('duplicate_user', '');
@endphp

@section('content')
    {{--    <form method="POST"--}}
    {{--          action="{{ route('login') }}"--}}
    {{--          class="form"--}}
    {{--          aria-label="{{ __('Login') }}">--}}
    {{--        @csrf--}}

    <login :images="{{ json_encode($images) }}">
    </login>
    {{--    </form>--}}

@endsection
