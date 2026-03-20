@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="login-container">
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="/assets/img/logo.png" alt="Logo">
                </div>
                <h5 id="lgn-head">Set New Password</h5>
            </div>

            <div class="form-section">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <input class="form-control email-mask @error('email') is-invalid @enderror" type="email"
                            name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input class="form-control @error('password') is-invalid @enderror" type="password"
                            name="password" placeholder="New Password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input class="form-control" type="password" name="password_confirmation"
                            placeholder="Confirm Password" required>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-register">Reset Password</button>
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