@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<div class="login-container">
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="/assets/img/logo.png" alt="Logo">
                </div>
                <h5 id="lgn-head">Reset Password</h5>
            </div>

            <div class="form-section">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <input class="form-control email-mask @error('email') is-invalid @enderror" type="email"
                            name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group text-center">
                        <div class="g-recaptcha d-inline-block"
                            data-sitekey="{{ config('services.recaptcha.site_key') }}">
                        </div>
                        @error('g-recaptcha-response')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-register">Send Reset Link</button>
                    </div>

                    <div class="signup-link">
                        <span>Remember your password?</span>
                        <a href="{{ route('login') }}">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection