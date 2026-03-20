@extends('layouts.app')

@section('title', 'Industry Interests')

@section('content')
@include('admin.partials.nav')
<div id="layoutSidenav">
    @include('admin.partials.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <h1>Industry Interests</h1>
                    <button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-2"></i>Add Interest
                    </button>
                </div>

                <div class="card mb-4">
                    <div class="card-header tbl-head-crd-header">
                        <i class="fas fa-building me-1"></i>
                        Added Interests
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover align-middle" id="IndustrydataTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
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
                        <i class="fas fa-plus me-2"></i>Add Industry Interest
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-header">
                    <h5>Edit Industry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="edit_description" name="description" class="form-control"></textarea>
                    </div>
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

            $('#IndustrydataTable').DataTable({
                ajax: {
                    url: '/admin/industries',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                },
                columns: [{
                    data: 'name'
                },
                {
                    data: 'description'
                },
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
                    url: '/admin/industries',
                    type: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        $('#addModal').modal('hide');
                        $('#addForm')[0].reset();
                        $('#IndustrydataTable').DataTable().ajax.reload();
                        toastr.success('Industry added successfully');
                    },
                    error: function () {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            });

            $(document).on('click', '.edit-btn', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: `/admin/industries/${id}`,
                    type: 'GET',
                    success: function (response) {
                        $('#edit_id').val(response.id);
                        $('#edit_name').val(response.name);
                        $('#edit_description').val(response.description);
                        $('#editModal').modal('show');
                    }
                });
            });
            $('#editForm').submit(function (e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                $.ajax({
                    url: `/admin/industries/${id}`,
                    type: 'PUT',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        $('#editModal').modal('hide');
                        $('#IndustrydataTable').DataTable().ajax.reload();
                        toastr.success('Industry updated successfully');
                    },
                    error: function () {
                        toastr.error('An error occurred. Please try again.');
                    }
                });
            });

            $(document).on('click', '.delete-btn', function () {
                if (confirm('Are you sure you want to delete this industry?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: `/admin/industries/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function (response) {
                            $('#IndustrydataTable').DataTable().ajax.reload();
                            toastr.success('Industry deleted successfully');
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