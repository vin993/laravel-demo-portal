@extends('layouts.app')
@section('title', 'Edit Profile')

@section('content')
@include('customer.partials.nav')
<div id="layoutSidenav">
    @include('customer.partials.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                    <h1 class="mb-0">Edit Profile</h1>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="row g-4">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-user-circle me-2"></i>Basic Information
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name', $user->name ?? '') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control bg-light">{{ $user->email }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                        value="{{ old('phone', $user->userDetail->phone ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information Card -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-building me-2"></i>Company Information
                            </div>
                            <div class="card-body">
                                <!-- <div class="mb-3">
                                    <label class="form-label">Primary Company</label>
                                    <input type="text" class="form-control" name="company_name"
                                        value="{{ old('company_name', $user->userDetail->company_name ?? '') }}"
                                        readonly>
                                </div> -->
                                <div class="mb-3">
                                    <label class="form-label">Industry Interests</label>
                                    <ul class="list-unstyled mb-0">
                                        @forelse($user->industries as $industry)
                                            <li><i class="fas fa-industry me-2"></i>{{ $industry->name }}</li>
                                        @empty
                                            <li class="text-muted">No industries assigned</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Associated Dealer Companies</label>
                                    <ul class="list-unstyled mb-0">
                                        @forelse($user->dealers as $dealer)
                                            <li><i class="fas fa-industry me-2"></i>{{ $dealer->name }}</li>
                                        @empty
                                            <li class="text-muted">No dealer companies assigned</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Manufacturers</label>
                                    <ul class="list-unstyled mb-0">
                                        @forelse($user->manufacturers as $manufacturer)
                                            <li><i class="fas fa-industry me-2"></i>{{ $manufacturer->name }}</li>
                                        @empty
                                            <li class="text-muted">No manufacturers assigned</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-map-marker-alt me-2"></i>Address Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ old('address', $user->userDetail->address ?? '') }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Country</label>
                                        <select class="form-control select2" name="country" id="country">
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State</label>
                                        <select class="form-control select2" name="state" id="state">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city"
                                            value="{{ old('city', $user->userDetail->city ?? '') }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" class="form-control" name="postal_code"
                                            value="{{ old('postal_code', $user->userDetail->postal_code ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
        @include('customer.partials.footer')
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize all Select2
            $('.select2').select2();
            $('.select2-multiple').select2({
                placeholder: $(this).data('placeholder')
            });

            // Phone mask
            $('input[name="phone"]').mask('(000) 000 0000');
            $('input[name="postal_code"]').mask('000000');

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
            }).on('change', function (e) {

                if (e.originalEvent) {
                    $('#state').val(null).trigger('change');
                    $('#city').val(null).trigger('change');

                    var countryId = $(this).val();
                    if (countryId) {
                        $('#state').select2('open');
                    }
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

            // Initialize manufacturer Select2
            $('select[name="manufacturer_id[]"]').select2({
                placeholder: "Select Manufacturers",
                allowClear: true,
                ajax: {
                    url: '/api/manufacturers/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1,
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
                }
            });


            @if($currentCountry)
                var countryOption = new Option('{{ $currentCountry->name }}', '{{ $currentCountry->id }}', true, true);
                $('#country').append(countryOption).trigger('change', [{ programmatic: true }]);
            @endif

                @if($currentState)
                    var stateOption = new Option('{{ $currentState->name }}', '{{ $currentState->id }}', true, true);
                    $('#state').append(stateOption).trigger('change', [{ programmatic: true }]);
                @endif

            });
    </script>
@endpush