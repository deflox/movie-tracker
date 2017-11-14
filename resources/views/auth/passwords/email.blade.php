@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-push-4 auth-box">
                <h1>Reset Password</h1>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <p>Please enter the E-Mail Address you've used to sign up with to reset your password.</p>
                <form method="post" action="{{ route('password.email') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="email">E-Mail Address</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        <span class="error">{{ $errors->first('email') }}</span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary pull-right">
                            Send Link
                        </button>
                        <a href="{{ route('login') }}">
                            Back to login?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
