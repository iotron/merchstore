@component('mail::message')
# Welcome to JetPax.

Your registered email is {{ $otp['identifier']}}<br>

Your one time password is<br>

@component('mail::panel')
{{ $otp['code']}}<br>
@endcomponent

The above code is valid till {{ $otp['expires_at']}}.<br>
Kindly ignore this email if you have not requested for an otp.<br>

Thanks,<br>
Team {{ config('app.name') }}
@endcomponent
