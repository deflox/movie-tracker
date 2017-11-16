<!DOCTYPE html>
<body>
    <p>Hi</p>
    <p>
        You receive this E-Mail because someone (hopefully you) send a password reset request for the account associated
        with this E-Mail Address. Please click on the link below to change your password:
    </p>
    <p>
        <a href="{{ route('password.reset', ['token' => $token]) }}">{{ route('password.reset', ['token' => $token]) }}</a>
    </p>
    <p>
        Thanks,<br>
        The MovieTracker Team
    </p>
    <p>
        Please ignore this E-Mail if you did not initiate this request!
    </p>
</body>