@extends('layouts.auth')

@section('title', 'Account Inactive')

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

                    <h5 id="lgn-head">Account Reactivation Request</h5>
                </div>

                <div class="form-section">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        Your account has been deactivated due to inactivity. Please submit this form to request reactivation.
                    </div>

                    <form method="POST" action="{{ route('account.reactivation.request') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}">
                        
                        <div class="form-group">
                            <input class="form-control @error('name') is-invalid @enderror" 
                                type="text" 
                                name="name" 
                                placeholder="Full Name"
                                value="{{ $user->name }}" 
                                required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <input class="form-control @error('phone') is-invalid @enderror" 
                                type="text" 
                                name="phone" 
                                placeholder="Phone Number"
                                value="{{ old('phone') }}" 
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                name="message" 
                                placeholder="Additional Message (Optional)"
                                rows="3">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-register">Request Reactivation</button>
                        </div>

                        <div class="signup-link">
                            <span>Need help?</span>
                            <a href="mailto:{{ config('mail.support_email', 'support@example.com') }}">Contact Support</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection