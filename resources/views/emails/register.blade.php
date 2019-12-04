@component('mail::message')
# Introduction

Dear {{ $username }}

Thanks for signing up for practice e-commerce!

Please verify your email address by clicking the button below.
{{-- 
@component('mail::button',['url'=>'http://localhost:8000/email-verify?email='.$email, 'method' => 'POST'])
Confirm
@endcomponent --}}

<form target="_blank" action="http://localhost:8000/email-verify" method="post">
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="submit" value="Verify">
</form>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
