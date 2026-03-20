@extends('layouts.auth')

@section('title', 'Complete Registration')

@section('content')
<div class="registration-container">
    <div class="container">
        <div class="registration-card">
            <div class="registration-header">
                <div class="logo-container">
                    <img src="/assets/img/logo.png" alt="Logo">
                </div>
                <h4>Complete Your Registration</h4>
                <p class="text-center" style="color:white"><strong>You've been invited by {{ $primaryUser->name }} from Demo App.</strong></p>
            </div>

            <div class="form-section">
                <form method="POST" action="{{ route('register.secondary.complete') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="row g-3">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- <div class="form-group">
                                <label>Company Name</label>
                                <input class="form-control" type="text" value="{{ $primaryUser->company_name }}" disabled>
                            </div> -->

                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="form-control" type="email" value="{{ $email }}" disabled>
                                <input type="hidden" name="email" value="{{ $email }}">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input class="form-control @error('phone') is-invalid @enderror" type="text" id="phone"
                                    name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label for="password">Password</label>
                                        <input class="form-control @error('password') is-invalid @enderror"
                                            type="password" id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input class="form-control" type="password" 
                                            id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select class="form-control @error('country') is-invalid @enderror" id="country"
                                    name="country" required>
                                    <option value="">Select Country</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="state">State</label>
                                <select class="form-control @error('state') is-invalid @enderror" id="state"
                                    name="state" required>
                                    <option value="">Select State</option>
                                </select>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="city">City</label>
                                <input class="form-control @error('city') is-invalid @enderror" type="text"
                                    id="city" name="city" value="{{ old('city') }}">
                                <!-- <select class="form-control @error('city') is-invalid @enderror" id="city" 
                                    name="city" required>
                                    <option value="">Select City</option>
                                </select> -->
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="postal_code">Postal Code</label>
                                <input class="form-control @error('postal_code') is-invalid @enderror" type="text"
                                    id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                    name="address" rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="user-agreement-checkbox">
                            <input type="checkbox" id="user_agreement" name="user_agreement" required
                                class="@error('user_agreement') is-invalid @enderror">
                            <label for="user_agreement">I have read and agree to the 
                                    <a href="{{ route('user-agreement') }}" target="_blank">User Agreement</a></label>
                            @error('user_agreement')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-register">Complete Registration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Phone mask
        $('input[name="phone"]').mask('(000) 000 0000');
        $('input[name="postal_code"]').mask('000000');

        // Password validation
        $('input[name="password"], input[name="password_confirmation"]').on('input', function() {
            const password = $('input[name="password"]').val();
            const confirmation = $('input[name="password_confirmation"]').val();
            const passwordInput = $('input[name="password"]');
            const confirmInput = $('input[name="password_confirmation"]');

            if (password.length < 8) {
                passwordInput.addClass('is-invalid').removeClass('is-valid');
            } else {
                passwordInput.removeClass('is-invalid').addClass('is-valid');
            }
            if (confirmation && password !== confirmation) {
                confirmInput.addClass('is-invalid').removeClass('is-valid');
            } else if (confirmation) {
                confirmInput.removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Location selects configuration
        $('#country').select2({
            placeholder: "Select Country",
            allowClear: true,
            ajax: {
                url: '/api/countries/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name,
                                code: item.code
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#state').select2({
            placeholder: "Select State",
            allowClear: true,
            ajax: {
                url: '/api/states/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        country_id: $('#country').val(),
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // $('#city').select2({
        //     placeholder: "Select City",
        //     allowClear: true,
        //     ajax: {
        //         url: '/api/cities/search',
        //         dataType: 'json',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 q: params.term || '',
        //                 state_id: $('#state').val(),
        //                 page: params.page || 1
        //             };
        //         },
        //         processResults: function(data) {
        //             return {
        //                 results: Object.entries(data).map(([id, name]) => ({
        //                     id: id,
        //                     text: name
        //                 }))
        //             };
        //         },
        //         cache: true
        //     }
        // });

        // Handle dependencies
        $('#country').on('change', function() {
            $('#state').val(null).trigger('change');
            // $('#city').val(null).trigger('change');
        });

        // $('#state').on('change', function() {
            // $('#city').val(null).trigger('change');
        // });
    });
</script>
@endpush
@endsection
