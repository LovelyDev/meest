@extends ('layouts.html', [
  'body_class' => 'login-page with-background',
  'page_content_class' => 'no-sidebar',
])

@section('content')
    <div class="login">
        <div class="login__inner-wrapper column">
            <img src="/storage/logo.png" class="ui image centered circular">
            <h3 class="login__header">Sign In</h3>

            <div class="login__body">
                <form method="POST"
                      action="{{ route('login') }}"
                      class="form"
                      aria-label="{{ __('Login') }}">
                    @csrf

                    <div class="login__email form__item">
                        <label for="email">{{ __('E-Mail Address') }}</label>

                        <input id="email"
                               type="email"
                               class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                               name="email" value="{{ old('email') }}" required autofocus>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif

                    </div>

                    <div class="login__password form__item">
                        <label for="password">{{ __('Password') }}</label>
                        <input id="password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                               name="password" required>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                        @endif
                    </div>

                    <div class="login__remember-me">
                        <div class="ui checkbox field">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>

                    <div class="login__buttons">
                        <a class="btn btn-link"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>

                        <button type="submit"
                                class="login__buttons button">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="footer">

            </div>
        </div>
    </div>
@endsection
