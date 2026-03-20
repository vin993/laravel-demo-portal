@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="login-container">
        <div class="container">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo-container">
                        <a href="{{ env('WEB_URL') }}">
                            <img src="/assets/img/logo.png" alt="Logo">
                        </a>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h5 id="lgn-head">Login to your account</h5>
                </div>

                <div class="form-section">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <input class="form-control email-mask @error('email') is-invalid @enderror" type="email"
                                name="email" placeholder="Email Address" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input class="form-control @error('password') is-invalid @enderror" type="password"
                                name="password" placeholder="Password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group remember-forgot">
                            <div class="remember-me">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a>
                        </div>

                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}">
                            </div>
                            @error('g-recaptcha-response')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-register">Login</button>
                        </div>

                        <div class="signup-link">
                            <span>Don't have an account?</span>
                            <a href="{{ route('register') }}">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection