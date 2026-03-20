@extends('layouts.auth')

@section('title', 'User Agreement')

@section('content')
<div class="login-container">
    <div class="container">
        <div class="user-agreement-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="/assets/img/logo.png" alt="Logo">
                </div>
                <h4>User Agreement</h4>
            </div>
            
            <div class="user-agreement-content">
                <div class="agreement-wrapper">
                    <div class="agreement-sections">
                        <section class="agreement-section">
                            <h2>1. Introduction</h2>
                            <p>Welcome to our platform. By using our services, you agree to these terms and conditions.</p>
                        </section>

                        <section class="agreement-section">
                            <h2>2. Account Registration</h2>
                            <p>Users must provide accurate and complete information when creating an account.</p>
                        </section>

                        <section class="agreement-section">
                            <h2>3. Privacy Policy</h2>
                            <p>We respect your privacy and protect your personal information as described in our Privacy Policy.</p>
                        </section>

                        <section class="agreement-section">
                            <h2>4. User Responsibilities</h2>
                            <p>Users are responsible for maintaining the confidentiality of their account credentials.</p>
                        </section>

                        <section class="agreement-section">
                            <h2>5. Terms of Service</h2>
                            <p>Our services are provided "as is" and we reserve the right to modify or terminate them at any time.</p>
                        </section>
                    </div>
                    
                    <div class="text-center">
                        <button onclick="window.close()" class="btn btn-register">Close Agreement</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection