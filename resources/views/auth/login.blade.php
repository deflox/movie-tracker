@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-push-4 auth-box">
                <h1>Sign In</h1>
                <p>Please sign in to access your movies or <a href="{{ route('register') }}">sign up</a> if you don't have an account yet.</p>
                <form method="post" action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="email">E-Mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        <span class="error">{{ $errors->first('email') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">
                            Sign In
                        </button>
                        <a href="{{ route('password.request') }}">
                            Forgot Your Password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection