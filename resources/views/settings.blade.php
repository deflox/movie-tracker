@extends('layouts.app')

@section('content')
    @include('partials.navigation')
    <div class="container">
        <h1>Settings</h1>
        @include('partials.flash')
        <p>Below you have the possibility to change your E-Mail Address or your password.</p>
        <h2>Change your E-Mail Address</h2>
        <p>Please enter your current password and your new E-Mail Address to change it.</p>
        <form method="post" action="{{ route('settings.change.email') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="current-password-email-change">Current password:</label>
                <input id="current-password-email-change" type="password" class="form-control" name="current-password-email-change" required>
                <span class="error">{{ $errors->first('current-password-email-change') }}</span>
            </div>
            <div class="form-group">
                <label for="email">New E-Mail Address:</label>
                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                <span class="error">{{ $errors->first('email') }}</span>
            </div>
            <div class="form-group clearfix">
                <button class="btn btn-primary pull-right">
                    Save
                </button>
            </div>
        </form>
        <h2>Change your password</h2>
        <p>Please enter your current password and your new password to change it.</p>
        <form method="post" action="{{ route('settings.change.password') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="current-password-password-change">Current password:</label>
                <input id="current-password-password-change" type="password" class="form-control" name="current-password-password-change" required>
                <span class="error">{{ $errors->first('current-password-password-change') }}</span>
            </div>
            <div class="form-group">
                <label for="new-password">New password:</label>
                <input id="new-password" type="password" class="form-control" name="new-password" required>
                <span class="error">{{ $errors->first('new-password') }}</span>
            </div>
            <div class="form-group">
                <label for="new-password_confirmation">Confirm new password:</label>
                <input id="new-password_confirmation" type="password" class="form-control" name="new-password_confirmation" required>
                <span class="error">{{ $errors->first('new-password_confirmation') }}</span>
            </div>
            <div class="form-group clearfix">
                <button class="btn btn-primary pull-right">
                    Save
                </button>
            </div>
        </form>
    </div>
@endsection