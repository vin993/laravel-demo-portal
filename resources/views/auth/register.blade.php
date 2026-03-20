@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <div class="registration-container">
        <div class="container">
            <div class="registration-card">
                <div class="registration-header">
                    <div class="logo-container">
                        <img src="/assets/img/logo.png" alt="Logo">
                    </div>
                    <h4>Create Your Account</h4>
                </div>

                <div class="form-section">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name"
                                        name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email"
                                        name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input class="form-control @error('phone') is-invalid @enderror" type="text" id="phone"
                                        name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <select class="form-control @error('country') is-invalid @enderror" id="country"
                                        name="country">
                                        <option value="">Select Country</option>
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="state">State</label>
                                    <select class="form-control @error('state') is-invalid @enderror" id="state"
                                        name="state">
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

                                    <!-- <select class="form-control @error('city') is-invalid @enderror" id="city" name="city">
                                        <option value="">Select City</option>
                                    </select> -->
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input class="form-control @error('postal_code') is-invalid @enderror" type="text"
                                        id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                        name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label for="password">Password</label>
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" id="password" name="password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-6">
                                            <label for="password_confirmation">Confirm Password</label>
                                            <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                                type="password" id="password_confirmation" name="password_confirmation">
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="industry_interests">Industry Interests</label>
                                    <select
                                        class="form-control select2-multiple @error('industry_interests') is-invalid @enderror"
                                        id="industry_interests" name="industry_interests[]" multiple="multiple">
                                    </select>
                                    @error('industry_interests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="dealer_id">Select Dealer Companies</label>
                                    <select class="form-control select2-multiple @error('dealer_id') is-invalid @enderror"
                                        id="dealer_id" name="dealer_id[]" multiple="multiple">
                                    </select>
                                    @error('dealer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="manufacturer_id">Select Manufacturers</label>
                                    <select
                                        class="form-control select2-multiple @error('manufacturer_id') is-invalid @enderror"
                                        id="manufacturer_id" name="manufacturer_id[]" multiple="multiple">
                                    </select>
                                    @error('manufacturer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="user-agreement-checkbox">
                                <input type="checkbox" id="is_primary" name="is_primary"
                                    class="@error('is_primary') is-invalid @enderror" required>
                                <label for="is_primary">I will be the primary account manager for my company</label>
                                @error('is_primary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="user-agreement-checkbox">
                                <input type="checkbox" id="user_agreement" name="user_agreement"
                                    class="@error('user_agreement') is-invalid @enderror">
                                <label for="user_agreement">I have read and agree to the <a
                                        href="{{ route('user-agreement') }}" target="_blank">User Agreement</a></label>
                                @error('user_agreement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                            <button type="submit" class="btn btn-register">Create Account</button>
                        </div>
                        <div class="login-link">
                            <span>Already have an account?</span>
                            <a href="{{ route('login') }}">Login here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function () {

                $('input[name="phone"]').mask('(000) 000 0000');
                $('input[name="postal_code"]').mask('000000');

                // Industries Select2
                $('#industry_interests').select2({
                    tags: false,
                    placeholder: "Search and select existing industries",
                    multiple: true,
                    ajax: {
                        url: '/api/industries/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                selected: $(this).val()
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    createTag: function () {
                        return null;
                    }
                });

                // Dealers Select2
                $('#dealer_id').select2({
                    tags: false,
                    placeholder: "Search and select dealers",
                    multiple: false,
                    ajax: {
                        url: '/api/dealers/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                selected: $(this).val()
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    createTag: function () {
                        return null;
                    }
                });

                // Manufacturers Select2

                $('#manufacturer_id').select2({
                    tags: false,
                    placeholder: "Search and select manufacturers",
                    multiple: true,
                    ajax: {
                        url: '/api/manufacturers/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                selected: $(this).val() 
                            };
                        },
                        processResults: function (data) {

                            const selectedIds = $('#manufacturer_id').val() || [];

                            const filteredData = data.filter(item => !selectedIds.includes(item.id.toString()));

                            return {
                                results: filteredData.map(function (item) {
                                    return {
                                        id: item.id,
                                        text: item.name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    createTag: function () {
                        return null;
                    }
                });

                $('input[name="password"], input[name="password_confirmation"]').on('input', function () {
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




                // Country Select2
                $('#country').select2({
                    placeholder: "Select Country",
                    allowClear: true,
                    ajax: {
                        url: '/api/countries/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data.map(function (item) {
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


                // State Select2
                $('#state').select2({
                    placeholder: "Select State",
                    allowClear: true,
                    ajax: {
                        url: '/api/states/search',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                country_id: $('#country').val(),
                                page: params.page || 1
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        },
                        cache: true
                    }
                });

                // City Select2
                // $('#city').select2({
                //     placeholder: "Select City",
                //     allowClear: true,
                //     ajax: {
                //         url: '/api/cities/search',
                //         dataType: 'json',
                //         delay: 250,
                //         data: function (params) {
                //             return {
                //                 q: params.term || '',
                //                 state_id: $('#state').val(),
                //                 page: params.page || 1
                //             };
                //         },
                //         processResults: function (data) {
                //             return {
                //                 results: Object.entries(data).map(([id, name]) => ({
                //                     id: id,
                //                     text: name
                //                 }))
                //             };
                //         },
                //         cache: true
                //     }
                // }).on('select2:select', function (e) {
                //     const countryData = $('#country').select2('data')[0];
                //     const stateData = $('#state').select2('data')[0];
                //     const cityName = e.params.data.text;

                //     if (!countryData || !stateData || !cityName) {
                //         console.error('Missing required location data');
                //         return;
                //     }

                //     const countryCode = countryData.code.toLowerCase();
                //     const stateCode = stateData.state_code.toLowerCase();
                //     const formattedCity = cityName.toLowerCase().replace(/\s+/g, '%20');

                //     $('#postal_code').attr('disabled', true);

                //     $.ajax({
                //         url: `https://api.zippopotam.us/${countryCode}/${stateCode}/${formattedCity}`,
                //         method: 'GET',
                //         timeout: 5000,
                //         success: function (response) {
                //             if (response && response.places && response.places[0]) {
                //                 const postalCode = response.places[0]['post code'];
                //                 $('#postal_code').val(postalCode);
                //             }
                //         },
                //         error: function (xhr, status, error) {
                //             // console.error('Error fetching postal code:', error);
                //             $('#postal_code').val('');
                //         },
                //         complete: function () {
                //             $('#postal_code').attr('disabled', false);
                //         }
                //     });
                // });

                // Handle dependencies
                $('#country').on('change', function () {
                    $('#state').val(null).trigger('change');
                    // $('#city').val(null).trigger('change');
                });

                // $('#state').on('change', function () {
                //     $('#city').val(null).trigger('change');
                // });
            });
        </script>
    @endpush
@endsection