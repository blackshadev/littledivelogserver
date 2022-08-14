@extends('mail.base')

@section('title')
    Little divelog email verification
@endsection

@section('content')
    <p>
        Hi, {{ $name  }}!
    </p>
    <p>
        Welcome to out little divelog! To complete your registration process you need to verify your email address.
    </p>

    <p>
        To verify your email address click the button below.
        In case the button doesn't work for you we also added a plain link at the end of the email.
    </p>

    <a rel="noopener" target="_blank" href="{{ $verificationUrl }}" style="background-color: #6366f1; font-size: 18px; font-family: Helvetica, Arial, sans-serif; font-weight: bold; text-decoration: none; padding: 14px 20px; color: #ffffff; border-radius: 5px; display: inline-block; mso-padding-alt: 0;">
        <!--[if mso]>
        <i style="letter-spacing: 25px; mso-font-width: -100%; mso-text-raise: 30pt;">&nbsp;</i>
        <![endif]-->
        <span style="mso-text-raise: 15pt;">Verify email &rarr;</span>
        <!--[if mso]>
        <i style="letter-spacing: 25px; mso-font-width: -100%;">&nbsp;</i>
        <![endif]-->
    </a>

    <p style="font-size: 0.8rem;">{{ $verificationUrl  }}</p>
@endsection
