@extends('layouts.app')

@section('title', 'Saved Links')

@section('content')
@if(auth()->user()->isAdmin())
@include('admin.partials.nav')
<div id="layoutSidenav">
@include('admin.partials.sidenav')
<div id="layoutSidenav_content">
@else
@include('customer.partials.nav')
<div id="layoutSidenav">
@include('customer.partials.sidenav')
<div id="layoutSidenav_content">
@endif
<main>
<div class="container-fluid px-4">
<div class="d-flex justify-content-between align-items-center mt-5">
<h1>Saved Links</h1>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
    <i class="fas fa-plus me-2"></i>Add Link
</button>
</div>

<div class="card mb-4">
<div class="card-header">
    <i class="fas fa-bookmark me-1"></i>
    My Saved Links
</div>
<div class="card-body">
    <table class="table table-bordered table-hover" id="LinksDataTable">
        <thead>
            <tr>
                <th>Title</th>
                <th>URL</th>
                <th>Description</th>
                <th width="150">Actions</th>
            </tr>
        </thead>
    </table>
</div>
</div>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
    <form id="addForm">
        <div class="modal-header">
            <h5 class="modal-title">Add New Link</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label class="form-label">URL</label>
                <input type="url" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
</div>
</div>

<!-- Edit Modal -->

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Saved Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" name="url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


let table = $('#LinksDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/saved-links",
            type: "GET"
        },
        columns: [
            { data: 'title', name: 'title' },
            { data: 'url', name: 'url' },
            { data: 'description', name: 'description' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: 1,
                render: function (data) {
                    return '<a href="' + data + '" target="_blank">' + data + '</a>';
                }
            },
            {
                targets: -1,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    $('#addModal, #editModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    });

// Add new link
$('#addForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();

        $.ajax({
            url: '/saved-links',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#addModal').modal('hide');
                    $('#addForm')[0].reset();
                    table.ajax.reload();
                    toastr.success('Link added successfully');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('An error occurred while saving the link');
                }
            }
        });
    });


// Edit link
$(document).on('click', '.edit-btn', function () {
        let id = $(this).data('id');
        $.ajax({
            url: `/saved-links/${id}`,
            type: 'GET',
            success: function (response) {
                $('#edit_id').val(response.id);
                $('#editForm input[name="title"]').val(response.title);
                $('#editForm input[name="url"]').val(response.url);
                $('#editForm textarea[name="description"]').val(response.description);
                $('#editModal').modal('show');
            },
            error: function () {
                toastr.error('Error loading link details');
            }
        });
    });

    $('#editForm').submit(function (e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let formData = $(this).serialize();

        $.ajax({
            url: `/saved-links/${id}`,
            type: 'PUT',
            data: formData,
            success: function (response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    table.ajax.reload();
                    toastr.success('Link updated successfully');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('An error occurred while updating the link');
                }
            }
        });
    });

// Delete link
$(document).on('click', '.delete-btn', function () {
        if (confirm('Are you sure you want to delete this link?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `/saved-links/${id}`,
                type: 'DELETE',
                success: function (response) {
                    if (response.success) {
                        table.ajax.reload();
                        toastr.success('Link deleted successfully');
                    }
                },
                error: function () {
                    toastr.error('An error occurred while deleting the link');
                }
            });
        }
    });
});
</script>
@endpush