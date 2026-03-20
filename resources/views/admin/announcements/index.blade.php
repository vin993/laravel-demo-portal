@extends('layouts.app')
@section('title', 'Announcements')
@section('content')
@include('admin.partials.nav')
<div id="layoutSidenav">
	@include('admin.partials.sidenav')
	<div id="layoutSidenav_content">
		<main>
			<div class="container-fluid px-4">
				<div class="d-flex justify-content-between align-items-center mt-5">
					<h1>Announcements</h1>
					<button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#addModal">
						<i class="fas fa-plus me-2"></i>Add Announcement
					</button>
				</div>
				<div class="card mb-4">
					<div class="card-header tbl-head-crd-header d-flex justify-content-between align-items-center">
						<div>
							<i class="fas fa-bullhorn me-1"></i>
							Announcements List
						</div>
						<small class="text-muted">
							<i class="fas fa-info-circle me-1"></i>
							Drag the <i class="fas fa-grip-vertical mx-1"></i> handle to reorder announcements
						</small>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover align-middle" id="AnnouncementDataTable">
							<thead>
								<tr>
									<th>Sort</th>
									<th>Title</th>
									<th>Image</th>
									<th>Industries</th>
									<th>Dealers</th>
									<th>Manufacturers</th>
									<th>Status</th>
									<th>Published At</th>
									<th>Actions</th>
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
						<i class="fas fa-plus me-2"></i>Add Announcement
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label">Title</label>
						<input type="text" name="title" class="form-control" required>
					</div>
					<div class="form-group mb-3">
						<label class="form-label">Image</label>
						<input type="file" name="image" class="form-control" accept="image/*">
					</div>
					<div class="mb-3">
						<div class="alert alert-info">
							<i class="fas fa-info-circle me-2"></i>
							If no industries or dealer companies or manufacturers are selected, the announcement will be visible to all users.
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-6">
							<label class="form-label">Industries</label>
							<select name="industries[]" class="form-control" id="add_industries" multiple></select>
						</div>
						<div class="col-md-6">
							<label class="form-label">Dealer Companies</label>
							<select name="dealers[]" class="form-control" id="add_dealers" multiple></select>
						</div>
						<div class="col-md-6">
							<label class="form-label">Manufacturers</label>
							<select name="manufacturers[]" class="form-control" id="add_manufacturers" multiple></select>
						</div>
					</div>
					<div class="form-check mb-3">
						<input type="checkbox" name="status" class="form-check-input" checked>
						<label class="form-check-label">Active</label>
					</div>
					<div class="user-visibility-section mt-4">
						<h5 class="mb-3">
							<i class="fas fa-users me-2"></i>User Visibility
							<button type="button" class="btn btn-sm btn-outline-primary ms-2 refresh-users-btn">
								<i class="fas fa-sync-alt"></i> Refresh List
							</button>
						</h5>
						<div class="card">
							<div class="card-body p-0">
								<div class="table-responsive">
									<table class="table table-hover eligible-users-table mb-0">
										<thead class="table-light">
											<tr>
												<th>Name</th>
												<th>Email</th>
												<th>Access Type</th>
												<th>User Type</th>
											</tr>
										</thead>
										<tbody>
											<tr class="no-users-row">
												<td colspan="4" class="text-center text-muted py-3">
													Select industries or dealer companies or manufacturers to view eligible users
												</td>
											</tr>
										</tbody>
									</table>
								</div>
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
						<i class="fas fa-edit me-2"></i>Edit Announcement
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="form-group mb-3">
						<label class="form-label">Title</label>
						<input type="text" id="edit_title" name="title" class="form-control" required>
					</div>
					<div class="form-group mb-3">
						<label class="form-label">Current Image</label>
						<div id="current_image_container"></div>
					</div>
					<div class="form-group mb-3">
						<label class="form-label">New Image</label>
						<input type="file" name="image" class="form-control" accept="image/*">
					</div>
					<div class="row mb-3">
						<div class="mb-3">
							<div class="alert alert-info">
								<i class="fas fa-info-circle me-2"></i>
								If no industries or dealer companies or manufacturers are selected, the announcement will be visible to
								all users.
							</div>
						</div>
						<div class="col-md-6">
							<label class="form-label">Industries</label>
							<select id="edit_industries" name="industries[]" class="form-control" multiple></select>
						</div>
						<div class="col-md-6">
							<label class="form-label">Dealer Companies</label>
							<select id="edit_dealers" name="dealers[]" class="form-control" multiple></select>
						</div>
						<div class="col-md-6">
							<label class="form-label">Manufacturers</label>
							<select id="edit_manufacturers" name="manufacturers[]" class="form-control" multiple></select>
						</div>
					</div>
					<div class="form-check mb-3">
						<input type="checkbox" id="edit_status" name="status" class="form-check-input">
						<label class="form-check-label">Active</label>
					</div>
					<div class="user-visibility-section mt-4">
						<h5 class="mb-3">
							<i class="fas fa-users me-2"></i>User Visibility
							<button type="button" class="btn btn-sm btn-outline-primary ms-2 refresh-users-btn">
								<i class="fas fa-sync-alt"></i> Refresh List
							</button>
						</h5>
						<div class="card">
							<div class="card-body p-0">
								<div class="table-responsive">
									<table class="table table-hover eligible-users-table mb-0">
										<thead class="table-light">
											<tr>
												<th>Name</th>
												<th>Email</th>
												<th>Access Type</th>
												<th>User Type</th>
											</tr>
										</thead>
										<tbody>
											<tr class="no-users-row">
												<td colspan="4" class="text-center text-muted py-3">
													Select industries or dealer companies or manufacturers to view eligible users
												</td>
											</tr>
										</tbody>
									</table>
								</div>
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
<style>
.user-visibility-section {
	border-top: 1px solid #dee2e6;
	padding-top: 1.5rem;
}
.eligible-users-table {
	font-size: 0.9rem;
}
.eligible-users-table th {
	font-weight: 600;
}
.user-type-badge {
	font-size: 0.75rem;
	padding: 0.25rem 0.5rem;
	border-radius: 0.25rem;
}
.user-type-primary {
	background-color: #e3f2fd;
	color: #0d47a1;
	border: 1px solid #90caf9;
}
.user-type-secondary {
	background-color: #f5f5f5;
	color: #616161;
	border: 1px solid #e0e0e0;
}
.access-type {
	font-size: 0.85rem;
	color: #666;
}
.access-type i {
	margin-right: 0.25rem;
}
.reorder-handle {
	cursor: move;
	width: 20px;
	margin: 0 auto;
	color: #666;
	user-select: none;
}
.reorder-handle:hover {
	color: #333;
}
tr .reorder-handle i {
	pointer-events: none;
}
tr.dt-rowReorder-moving {
	background-color: #fafafa;
	outline: 2px solid #2196F3;
}
body.dt-rowReorder-noOverflow {
	overflow-x: hidden;
}
</style>
@push('scripts')
<script>
$(document).ready(function () {
	var csrfToken = $('meta[name="csrf-token"]').attr('content');
	$('#addModal, #editModal').on('hidden.bs.modal', function () {
		const modalId = $(this).attr('id');
		$(this).find('select').select2('destroy');
		$(this).find('form')[0].reset();
		$('#current_image_container').empty();
		$(`#${modalId} .eligible-users-table tbody`).html(`
			<tr class="no-users-row">
				<td colspan="4" class="text-center text-muted py-3">
					Select industries or dealer companies or manufacturers to view eligible users
				</td>
			</tr>
		`);
	});

	$('#addModal').on('shown.bs.modal', function () {
		$('#add_industries').select2({
			tags: false,
			width: '100%',
			placeholder: "Search and select industries",
			multiple: true,
			closeOnSelect: true,
			dropdownParent: $('#addModal .modal-content'),
			minimumInputLength: 0,
			minimumResultsForSearch: 0,
			allowClear: true,
			ajax: {
				url: '/api/industries/search',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term || '',
						selected: $(this).val() || []
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
			minimumInputLength: 0,
			minimumResultsForSearch: 0
		}).on('select2:clear select2:unselect', function (e) {
			updateEligibleUsers('addModal');
		});

		$('#add_dealers').select2({
			tags: false,
			width: '100%',
			placeholder: "Search and select dealer companies",
			multiple: true,
			closeOnSelect: true,
			dropdownParent: $('#addModal .modal-content'),
			minimumInputLength: 0,
			minimumResultsForSearch: 0,
			allowClear: true,
			ajax: {
				url: '/api/dealers/search',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term || '',
						selected: $(this).val() || []
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
			minimumInputLength: 0,
			minimumResultsForSearch: 0
		}).on('select2:clear select2:unselect', function (e) {
			updateEligibleUsers('addModal');
		});

		$('#add_manufacturers').select2({
			tags: false,
			width: '100%',
			placeholder: "Search and select manufacturers",
			multiple: true,
			closeOnSelect: true,
			dropdownParent: $('#addModal .modal-content'),
			minimumInputLength: 0,
			minimumResultsForSearch: 0,
			allowClear: true,
			ajax: {
				url: '/api/manufacturers/search',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term || '',
						selected: $(this).val() || []
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
			minimumInputLength: 0,
			minimumResultsForSearch: 0
		}).on('select2:clear select2:unselect', function (e) {
			updateEligibleUsers('addModal');
		});
	});

	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});

	$(document).on('select2:select', function (e) {
		$(e.target).siblings('.select2-container').find('.select2-search__field').focus();
	});

	$('#addModal, #editModal').on('change', 'select[name="industries[]"], select[name="dealers[]"], select[name="manufacturers[]"]', function (e) {
		e.preventDefault();
		const modalId = $(this).closest('.modal').attr('id');
		updateEligibleUsers(modalId);
	});

	$('.refresh-users-btn').click(function () {
		const modalId = $(this).closest('.modal').attr('id');
		updateEligibleUsers(modalId);
	});

	$('#addModal, #editModal').on('select2:clear', 'select[name="industries[]"], select[name="dealers[]"], select[name="manufacturers[]"]', function (e) {
		const modalId = $(this).closest('.modal').attr('id');
		updateEligibleUsers(modalId);
	});

	// Initialize DataTable
	var table = $('#AnnouncementDataTable').DataTable({
		rowReorder: {
			selector: '.reorder-handle',
			dataSrc: 'created_at'
		},
		ajax: {
			url: '/admin/announcements',
			type: 'GET',
			headers: {
				'X-CSRF-TOKEN': csrfToken
			}
		},
		columns: [
			{
				data: null,
				defaultContent: '<div class="reorder-handle"><i class="fas fa-grip-vertical"></i></div>',
				orderable: false,
				width: '40px'
			},
			{ data: 'title' },
			{
				data: 'image_path',
				render: function (data) {
					return data ? `<img src="/storage/${data}" height="50" class="img-thumbnail">` : 'No Image';
				}
			},
			{
            data: 'industries',
				render: function (data) {
					if (!data || data.length === 0) {
						return '<span class="text-muted">None</span>';
					}
					return data.map(i => `<span class="badge bg-info">${i.name}</span>`).join(' ');
				}
			},
			{
				data: 'dealers',
				render: function (data) {
					if (!data || data.length === 0) {
						return '<span class="text-muted">None</span>';
					}
					return data.map(d => `<span class="badge bg-primary">${d.name}</span>`).join(' ');
				}
			},
			{
				data: 'manufacturers',
				render: function (data) {
					if (!data || data.length === 0) {
						return '<span class="text-muted">None</span>';
					}
					return data.map(m => `<span class="badge bg-warning">${m.name}</span>`).join(' ');
				}
			},
			{
				data: 'status',
				render: function (data) {
					return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
				}
			},
			{
				data: 'created_at',
				render: function (data) {
					return moment(data).format('YYYY-MM-DD HH:mm:ss');
				}
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

	table.on('row-reorder', function (e, diff, edit) {
		let orders = [];
		diff.forEach(function (change) {
			let announcementId = table.row(change.node).data().id;
			let newOrder = moment().subtract(change.newPosition, 'seconds')
				.format('YYYY-MM-DD HH:mm:ss');
			orders.push({
				id: announcementId,
				order: newOrder
			});
		});
		if (orders.length > 0) {
			$.ajax({
				url: '/admin/announcements/reorder',
				type: 'POST',
				data: {
					orders: orders,
					_token: csrfToken
				},
				success: function (response) {
					toastr.success('Order updated successfully');
					table.ajax.reload();
				},
				error: function () {
					toastr.error('Failed to update order');
					table.ajax.reload();
				}
			});
		}
	});
	// Add Form Submission
	$('#addForm').submit(function (e) {
		e.preventDefault();
		var formData = new FormData(this);
		formData.set('status', $('input[name="status"]').is(':checked') ? '1' : '0');
		$.ajax({
			url: '/admin/announcements',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': csrfToken
			},
			success: function (response) {
				$('#addModal').modal('hide');
				$('#addForm')[0].reset();
				$('.select2').val(null).trigger('change');
				table.ajax.reload();
				toastr.success('Announcement added successfully');
			},
			error: function (xhr) {
				let errors = xhr.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					toastr.error(errors[key][0]);
				});
			}
		});
	});
	$(document).on('click', '.edit-btn', function () {
		let id = $(this).data('id');
		$.ajax({
			url: `/admin/announcements/${id}`,
			type: 'GET',
			success: function (response) {
				$('#edit_id').val(response.id);
				$('#edit_title').val(response.title);
				$('#edit_content').val(response.content);
				$('#edit_published_at').val(response.published_at ? response.published_at.slice(0, 16) : '');
				$('#edit_status').prop('checked', response.status);
				if (response.image_path) {
					$('#current_image_container').html(`
						<img src="/storage/${response.image_path}" height="100" class="img-thumbnail mb-2">
					`);
				} else {
					$('#current_image_container').html('No image uploaded');
				}
				$('#editModal').modal('show');
				$('#editModal').on('shown.bs.modal', function () {
					$('#edit_industries').select2({
						data: response.industries.map(industry => ({
							id: industry.id,
							text: industry.name,
							selected: true
						})),
						tags: false,
						width: '100%',
						placeholder: "Search and select industries",
						multiple: true,
						closeOnSelect: true,
						dropdownParent: $('#editModal .modal-content'),
						minimumInputLength: 0,
						minimumResultsForSearch: 0,
						allowClear: true,
						ajax: {
							url: '/api/industries/search',
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term || '',
									selected: $(this).val() || []
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
					}).val(response.industries.map(i => i.id)).trigger('change');
					$('#edit_dealers').select2({
						data: response.dealers.map(dealer => ({
							id: dealer.id,
							text: dealer.name,
							selected: true
						})),
						tags: false,
						width: '100%',
						placeholder: "Search and select dealer companies",
						multiple: true,
						closeOnSelect: true,
						dropdownParent: $('#editModal .modal-content'),
						minimumInputLength: 0,
						minimumResultsForSearch: 0,
						allowClear: true,
						ajax: {
							url: '/api/dealers/search',
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term || '',
									selected: $(this).val() || []
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
					}).val(response.dealers.map(d => d.id)).trigger('change');
					$('#edit_manufacturers').select2({
						data: response.manufacturers.map(manufacturer => ({
							id: manufacturer.id,
							text: manufacturer.name,
							selected: true
						})),
						tags: false,
						width: '100%',
						placeholder: "Search and select manufacturers",
						multiple: true,
						closeOnSelect: true,
						dropdownParent: $('#editModal .modal-content'),
						minimumInputLength: 0,
						minimumResultsForSearch: 0,
						allowClear: true,
						ajax: {
							url: '/api/manufacturers/search',
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term || '',
									selected: $(this).val() || []
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
					}).val(response.manufacturers.map(d => d.id)).trigger('change');
					// Update eligible users
					updateEligibleUsers('editModal');
				});
			}
		});
	});

	// Edit Form Submission
	$('#editForm').submit(function (e) {
		e.preventDefault();
		let id = $('#edit_id').val();
		var formData = new FormData(this);
		if (!$('#edit_industries').val()) {
			formData.append('industries[]', '');
		}
		if (!$('#edit_dealers').val()) {
			formData.append('dealers[]', '');
		}
		if (!$('#edit_manufacturers').val()) {
			formData.append('manufacturers[]', '');
		}
		formData.set('status', $('#edit_status').is(':checked') ? '1' : '0');
		$.ajax({
			url: `/admin/announcements/${id}`,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			headers: {
				'X-CSRF-TOKEN': csrfToken,
				'X-HTTP-Method-Override': 'PUT'
			},
			success: function (response) {
				$('#editModal').modal('hide');
				table.ajax.reload();
				toastr.success('Announcement updated successfully');
			},
			error: function (xhr) {
				let errors = xhr.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					toastr.error(errors[key][0]);
				});
			}
		});
	});

	// Delete Button Click
	$(document).on('click', '.delete-btn', function () {
		if (confirm('Are you sure you want to delete this announcement?')) {
			let id = $(this).data('id');
			$.ajax({
				url: `/admin/announcements/${id}`,
				type: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': csrfToken
				},
				success: function (response) {
					table.ajax.reload();
					toastr.success('Announcement deleted successfully');
				},
				error: function () {
					toastr.error('An error occurred while deleting the announcement');
				}
			});
		}
	});

	function updateEligibleUsers(modalId) {
		const industries = $(`#${modalId} select[name="industries[]"]`).val() || [];
		const dealers = $(`#${modalId} select[name="dealers[]"]`).val() || [];
		const manufacturers = $(`#${modalId} select[name="manufacturers[]"]`).val() || [];
		const tbody = $(`#${modalId} .eligible-users-table tbody`);
		if (industries.length === 0 && dealers.length === 0 && manufacturers.length === 0) {
			tbody.html(`
		<tr class="no-users-row">
			<td colspan="4" class="text-center text-muted py-3">
				<i class="fas fa-globe me-2"></i>
				This announcement will be visible to all users
			</td>
		</tr>
	`);
			return;
		}
		$.ajax({
			url: '/admin/announcements/eligible-users',
			type: 'POST',
			data: {
				industries: industries,
				dealers: dealers,
				manufacturers: manufacturers,
				_token: csrfToken
			},
			beforeSend: function () {
				tbody.html(`
			<tr>
				<td colspan="4" class="text-center">
					<div class="spinner-border spinner-border-sm text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
					Loading users...
				</td>
			</tr>
		`);
			},
			success: function (response) {
				tbody.empty();
				if (!response.users || !response.users.length) {
					tbody.html(`
				<tr class="no-users-row">
					<td colspan="4" class="text-center text-muted py-3">
						No eligible users found
					</td>
				</tr>
			`);
					return;
				}
				response.users.forEach(function (user) {
					let accessTypes = [];
					if (user.matched_industries && user.matched_industries.length) {
						accessTypes.push(`<div class="access-type">
					<i class="fas fa-industry"></i>${user.matched_industries.join(', ')}
				</div>`);
					}
					if (user.matched_dealers && user.matched_dealers.length) {
						accessTypes.push(`<div class="access-type">
					<i class="fas fa-store"></i>${user.matched_dealers.join(', ')}
				</div>`);
					}
					if (user.matched_manufacturers && user.matched_manufacturers.length) {
						accessTypes.push(`<div class="access-type">
					<i class="fas fa-store"></i>${user.matched_manufacturers.join(', ')}
				</div>`);
					}
					const userTypeBadge = user.is_primary
						? '<span class="user-type-badge user-type-primary">Primary</span>'
						: '<span class="user-type-badge user-type-secondary">Secondary</span>';
					tbody.append(`
				<tr>
					<td>${user.name}</td>
					<td>${user.email}</td>
					<td>${accessTypes.join('')}</td>
					<td>${userTypeBadge}</td>
				</tr>
			`);
				});
			},
			error: function (xhr, status, error) {
				console.error('Error:', error);
				tbody.html(`
			<tr>
				<td colspan="4" class="text-center text-danger">
					Error loading users. Please try again.
				</td>
			</tr>
		`);
				toastr.error('Error fetching eligible users');
			}
		});
	}
});
</script>
@endpush
