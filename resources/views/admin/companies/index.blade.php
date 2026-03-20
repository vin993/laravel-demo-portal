@extends('layouts.app')

@section('title', 'Companies')

@section('content')
    @include('admin.partials.nav')
    <div id="layoutSidenav">
        @include('admin.partials.sidenav')
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center mt-5">
                        <h1>Companies</h1>
                        <button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus me-2"></i>Add Company
                        </button>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header tbl-head-crd-header">
                            <i class="fas fa-building me-1"></i>
                            Companies List
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover align-middle" id="CompanyDataTable">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Legal Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th width="200">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            @include('admin.partials.footer')
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addForm" enctype="multipart/form-data">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            <i class="fas fa-plus me-2"></i>Add Company
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Name*</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Legal Name</label>
                                    <input type="text" name="legal_name" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Tax Number</label>
                                    <input type="text" name="tax_number" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" name="registration_number" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="country">Country</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="country" id="country">
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="state">State</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="state" id="state">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="city">City</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="city" id="city">
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Currency</label>
                                    <input type="text" name="currency" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Timezone</label>
                                    <input type="text" name="timezone" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">About</label>
                                    <textarea name="about" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editForm" enctype="multipart/form-data">
                    @method('PUT')
                    <input type="hidden" id="edit_id">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>Edit Company
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Name*</label>
                                    <input type="text" id="edit_name" name="name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Legal Name</label>
                                    <input type="text" id="edit_legal_name" name="legal_name" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Tax Number</label>
                                    <input type="text" id="edit_tax_number" name="tax_number" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Registration Number</label>
                                    <input type="text" id="edit_registration_number" name="registration_number"
                                        class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" id="edit_email" name="email" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" id="edit_phone" name="phone" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" id="edit_website" name="website" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <div id="current_logo" class="mt-2"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea id="edit_address" name="address" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_country">Country</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="country" id="edit_country">
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_state">State</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="state" id="edit_state">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="edit_city">City</label>
                                    <div class="select2-container">
                                        <select class="form-control select2" name="city" id="edit_city">
                                            <option value="">Select City</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" id="edit_postal_code" name="postal_code" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Currency</label>
                                    <input type="text" id="edit_currency" name="currency" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Timezone</label>
                                    <input type="text" id="edit_timezone" name="timezone" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">About</label>
                                    <textarea id="edit_about" name="about" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            initializeSelect2ForAdd();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $('#CompanyDataTable').DataTable({
                ajax: {
                    url: '/admin/companies',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                },
                columns: [{
                        data: 'logo',
                        render: function(data) {
                            if (data) {
                                return `<img src="/storage/companies/${data}" alt="Company Logo" style="height: 50px;">`;
                            }
                            return '<span class="text-muted">No logo</span>';
                        }
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'legal_name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'phone'
                    },
                    {
                        data: null,
                        render: function(data) {
                            let location = [];

                            if (data.address) {
                                location.push(data.address);
                            }

                            let regionInfo = [];
                            if (data.city && data.city.name) {
                                regionInfo.push(data.city.name);
                            }
                            if (data.state && data.state.name) {
                                regionInfo.push(data.state.name);
                            }
                            if (data.country && data.country.name) {
                                regionInfo.push(data.country.name);
                            }

                            if (regionInfo.length > 0) {
                                location.push(regionInfo.join(', '));
                            }

                            if (data.postal_code) {
                                location.push(data.postal_code);
                            }

                            return location.length > 0 ? location.join('<br>') :
                                '<span class="text-muted">No location data</span>';
                        }
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-warning edit-btn" data-id="${id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
                        }
                    }
                ]
            });

            $('#addForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: '/admin/companies',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        $('#addModal').modal('hide');
                        $('#addForm')[0].reset();
                        $('#CompanyDataTable').DataTable().ajax.reload();
                        toastr.success('Company added successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    }
                });
            });

            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.ajax({
                    url: `/admin/companies/${id}`,
                    type: 'GET',
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_legal_name').val(response.legal_name);
                        $('#edit_tax_number').val(response.tax_number);
                        $('#edit_registration_number').val(response.registration_number);
                        $('#edit_email').val(response.email);
                        $('#edit_phone').val(response.phone);
                        $('#edit_website').val(response.website);
                        $('#edit_address').val(response.address);
                        $('#edit_postal_code').val(response.postal_code);
                        $('#edit_currency').val(response.currency);
                        $('#edit_timezone').val(response.timezone);
                        $('#edit_about').val(response.about);

                        if (response.country) {
                            var countryOption = new Option(response.country.name, response
                                .country.id, true, true);
                            $('#edit_country').empty().append(countryOption).trigger('change');
                        }

                        // Initialize state
                        if (response.state) {
                            var stateOption = new Option(response.state.name, response.state.id,
                                true, true);
                            $('#edit_state').empty().append(stateOption).trigger('change');
                        }

                        // Initialize city
                        if (response.city) {
                            var cityOption = new Option(response.city.name, response.city.id,
                                true, true);
                            $('#edit_city').empty().append(cityOption).trigger('change');
                        }

                        // Show current logo if exists
                        if (response.logo) {
                            $('#current_logo').html(`
                            <img src="/storage/companies/${response.logo}" alt="Current Logo" style="height: 50px;">
                            <p class="text-muted mt-1">Current logo</p>
                        `);
                        } else {
                            $('#current_logo').empty();
                        }

                        $('#editModal').modal('show');
                    }
                });
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                var formData = new FormData(this);
                formData.append('_method', 'PUT');

                formData.set('country', $('#edit_country').val());
                formData.set('state', $('#edit_state').val());
                formData.set('city', $('#edit_city').val());

                $.ajax({
                    url: `/admin/companies/${id}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        $('#CompanyDataTable').DataTable().ajax.reload();
                        toastr.success('Company updated successfully');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error('An error occurred. Please try again.');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this company?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: `/admin/companies/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            $('#CompanyDataTable').DataTable().ajax.reload();
                            toastr.success('Company deleted successfully');
                        },
                        error: function() {
                            toastr.error('An error occurred. Please try again.');
                        }
                    });
                }
            });

            // Reset forms when modals are closed
            $('#addModal').on('hidden.bs.modal', function() {
                $('#addForm')[0].reset();
            });

            $('#editModal').on('show.bs.modal', function() {
                initializeSelect2ForEdit();
            });

            $('#addModal, #editModal').on('hidden.bs.modal', function() {
                $(this).find('select').select2('destroy');
            });
            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#current_logo').empty();
            });

        });

        function initializeSelect2ForAdd() {
            $('#country').select2({
                dropdownParent: $('#addModal'),
                placeholder: "Select Country",
                allowClear: true,
                ajax: {
                    url: '/api/countries/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '', // Send empty string to get all countries
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        console.log('Country search response:', data); // Debug log
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
                    error: function(xhr, status, error) { // Add error handling
                        console.error('Country search error:', error);
                        console.error('Status:', status);
                        console.error('Response:', xhr.responseText);
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
            // State Select2
            $('#state').select2({
                dropdownParent: $('#addModal'),
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

            // City Select2
            $('#city').select2({
                dropdownParent: $('#addModal'),
                placeholder: "Select City",
                allowClear: true,
                ajax: {
                    url: '/api/cities/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            state_id: $('#state').val(),
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: Object.entries(data).map(([id, name]) => ({
                                id: id,
                                text: name
                            }))
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                const countryData = $('#country').select2('data')[0];
                const stateData = $('#state').select2('data')[0];
                const cityName = e.params.data.text;

                if (!countryData || !stateData || !cityName) {
                    console.error('Missing required location data');
                    return;
                }

                const countryCode = countryData.code.toLowerCase();
                const stateCode = stateData.state_code.toLowerCase();
                const formattedCity = cityName.toLowerCase().replace(/\s+/g, '%20');

                $('#postal_code').attr('disabled', true);

                $.ajax({
                    url: `https://api.zippopotam.us/${countryCode}/${stateCode}/${formattedCity}`,
                    method: 'GET',
                    timeout: 5000,
                    success: function(response) {
                        if (response && response.places && response.places[0]) {
                            const postalCode = response.places[0]['post code'];
                            $('#postal_code').val(postalCode);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#postal_code').val('');
                    },
                    complete: function() {
                        $('#postal_code').attr('disabled', false);
                    }
                });
            });

            // Handle dependencies
            $('#country').on('change', function() {
                $('#state').val(null).trigger('change');
                $('#city').val(null).trigger('change');
            });

            $('#state').on('change', function() {
                $('#city').val(null).trigger('change');
            });
        }

        function initializeSelect2ForEdit() {
            // Country Select2
            $('#edit_country').select2({
                dropdownParent: $('#editModal'),
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
                },
                minimumInputLength: 0
            });

            // State Select2
            $('#edit_state').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select State",
                allowClear: true,
                ajax: {
                    url: '/api/states/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            country_id: $('#edit_country').val(),
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

            // City Select2
            $('#edit_city').select2({
                dropdownParent: $('#editModal'),
                placeholder: "Select City",
                allowClear: true,
                ajax: {
                    url: '/api/cities/search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            state_id: $('#edit_state').val(),
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: Object.entries(data).map(([id, name]) => ({
                                id: id,
                                text: name
                            }))
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                const countryData = $('#edit_country').select2('data')[0];
                const stateData = $('#edit_state').select2('data')[0];
                const cityName = e.params.data.text;

                if (!countryData || !stateData || !cityName) {
                    console.error('Missing required location data');
                    return;
                }

                const countryCode = countryData.code.toLowerCase();
                const stateCode = stateData.state_code.toLowerCase();
                const formattedCity = cityName.toLowerCase().replace(/\s+/g, '%20');

                $('#edit_postal_code').attr('disabled', true);

                $.ajax({
                    url: `https://api.zippopotam.us/${countryCode}/${stateCode}/${formattedCity}`,
                    method: 'GET',
                    timeout: 5000,
                    success: function(response) {
                        if (response && response.places && response.places[0]) {
                            const postalCode = response.places[0]['post code'];
                            $('#edit_postal_code').val(postalCode);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#edit_postal_code').val('');
                    },
                    complete: function() {
                        $('#edit_postal_code').attr('disabled', false);
                    }
                });
            });

            // Handle dependencies
            $('#edit_country').on('change', function() {
                $('#edit_state').val(null).trigger('change');
                $('#edit_city').val(null).trigger('change');
            });

            $('#edit_state').on('change', function() {
                $('#edit_city').val(null).trigger('change');
            });
        }
    </script>
@endpush
