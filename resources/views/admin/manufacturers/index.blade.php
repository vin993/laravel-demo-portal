@extends('layouts.app')

@section('title', 'Manufacturers')

@section('content')
@include('admin.partials.nav')
<div id="layoutSidenav">
    @include('admin.partials.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <h1>Manufacturers</h1>
                    <button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-2"></i>Add Manufacturer
                    </button>
                </div>

                <div class="card mb-4">
                    <div class="card-header tbl-head-crd-header">
                        <i class="fas fa-industry me-1"></i>
                        Manufacturers List
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover align-middle" id="ManufacturerDataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <!-- <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th> -->
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addForm">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add Manufacturer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <!-- <div class="form-group mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div> -->
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-header">
                    <h5>Edit Manufacturer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <!-- <div class="form-group mb-3">
                        <label>Contact Person</label>
                        <input type="text" id="edit_contact_person" name="contact_person" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Phone</label>
                        <input type="text" id="edit_phone" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea id="edit_address" name="address" class="form-control"></textarea>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $('#ManufacturerDataTable').DataTable({
            ajax: {
                url: '/admin/manufacturers',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            },
            columns: [
                { data: 'name' },
                // { data: 'contact_person' },
                // { data: 'email' },
                // { data: 'phone' },
                // { data: 'address' },
                {
                    data: 'id',
                    render: function (id) {
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

        $('#addForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '/admin/manufacturers',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    $('#addModal').modal('hide');
                    $('#addForm')[0].reset();
                    $('#ManufacturerDataTable').DataTable().ajax.reload();
                    toastr.success('Manufacturer added successfully');
                },
                error: function () {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

        $(document).on('click', '.edit-btn', function () {
            let id = $(this).data('id');
            $.ajax({
                url: `/admin/manufacturers/${id}`,
                type: 'GET',
                success: function (response) {
                    $('#edit_id').val(response.id);
                    $('#edit_name').val(response.name);
                    // $('#edit_contact_person').val(response.contact_person);
                    // $('#edit_email').val(response.email);
                    // $('#edit_phone').val(response.phone);
                    // $('#edit_address').val(response.address);
                    $('#editModal').modal('show');
                }
            });
        });

        $('#editForm').submit(function (e) {
            e.preventDefault();
            let id = $('#edit_id').val();
            $.ajax({
                url: `/admin/manufacturers/${id}`,
                type: 'PUT',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    $('#editModal').modal('hide');
                    $('#ManufacturerDataTable').DataTable().ajax.reload();
                    toastr.success('Manufacturer updated successfully');
                },
                error: function () {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

        $(document).on('click', '.delete-btn', function () {
            if (confirm('Are you sure you want to delete this manufacturer?')) {
                let id = $(this).data('id');
                $.ajax({
                    url: `/admin/manufacturers/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        $('#ManufacturerDataTable').DataTable().ajax.reload();
                        toastr.success('Manufacturer deleted successfully');
                    },
                    error: function () {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            }
        });
    });
</script>
@endpush