@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-push-4 auth-box">
                <h1>Reset Password</h1>
                <p>Enter your E-Mail Address and your new password.</p>
                <form method="post" action="{{ route('password.request') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label for="email">E-Mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        <span class="error">{{ $errors->first('email') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                        <span class="error">{{ $errors->first('password') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="password-confirm">Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        <span class="error">{{ $errors->first('password-confirm') }}</span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
