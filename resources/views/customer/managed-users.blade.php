@extends('layouts.app')
@section('title', 'Manage Secondary Users')
@section('content')
	@include('customer.partials.nav')
	<div id="layoutSidenav">
		@include('customer.partials.sidenav')
		<div id="layoutSidenav_content">
			<main>
				<div class="container-fluid px-4">
					<div class="d-flex justify-content-between align-items-center mt-5">
						<h1>Secondary Users Management</h1>
						<button type="button" class="btn btn-primary" data-bs-toggle="modal"
							data-bs-target="#inviteUserModal">
							<i class="fas fa-user-plus me-1"></i>
							Invite User
						</button>
					</div>
					<div class="card mb-4 mt-4">
						<div class="card-header tbl-head-crd-header">
							<i class="fas fa-users me-1"></i>
							Secondary Users
						</div>
						<div class="card-body">
							<table class="table table-bordered table-hover align-middle" id="managedUsersdataTable">
								<thead class="table-light">
									<tr>
										<th width="50">Sr No.</th>
										<th>User Details</th>
										<th>Location</th>
										<th>Status</th>
										<th width="120">Actions</th>
									</tr>
								</thead>
								<tbody>
									@forelse($managedUsers as $index => $user)
										<tr>
											<td>{{ $index + 1 }}</td>
											<td>
												<div class="d-flex align-items-center">
													<div class="ms-2">
														<h6 class="mb-0">{{ $user->name }}</h6>
														<small class="text-muted">
															<i class="fas fa-envelope fa-sm me-1"></i>{{ $user->email }}
														</small>
														@if ($user->userDetail?->phone)
															<small class="text-muted d-block">
																<i
																	class="fas fa-phone fa-sm me-1"></i>{{ $user->userDetail->phone }}
															</small>
														@endif
													</div>
												</div>
											</td>
											<td>
												@if ($user->userDetail)
													<small class="d-block">
														{{ $user->userDetail->city()->first()->name ?? $user->userDetail->city }},
														{{ $user->userDetail->state()->first()->name ?? $user->userDetail->state }}
													</small>
													<small class="text-muted">
														{{ $user->userDetail->country()->first()->name ?? $user->userDetail->country }}
													</small>
												@else
													<span class="text-muted">No location data</span>
												@endif
											</td>
											<td>
												<div class="d-flex flex-column align-items-start">
													<span
														class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }} mb-1">
														{{ $user->is_approved ? 'Active' : 'Inactive' }}
													</span>
													<small class="text-muted">
														<i class="fas fa-clock fa-sm me-1"></i>
														Joined: {{ $user->created_at->diffForHumans() }}
													</small>
													@if ($user->last_login_at)
														<small class="text-muted">
															<i class="fas fa-sign-in-alt fa-sm me-1"></i>
															Last Login:
															{{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
														</small>
													@endif
												</div>
											</td>
											<td>
												<div class="btn-group">
													<button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
														data-bs-target="#userModal{{ $user->id }}">
														<i class="fas fa-edit"></i>
													</button>
													<button type="button"
														class="btn btn-sm {{ $user->is_approved ? 'btn-warning' : 'btn-success' }} toggle-status"
														data-user-id="{{ $user->id }}" data-status="{{ $user->is_approved }}">
														<i class="fas fa-{{ $user->is_approved ? 'ban' : 'check' }}"></i>
													</button>
													<button type="button" class="btn btn-sm btn-danger delete-user"
														data-user-id="{{ $user->id }}">
														<i class="fas fa-trash"></i>
													</button>
												</div>
											</td>

										</tr>
									@empty
										<tr class="no-data-tr">
											<td class="p-0"></td>
											<td class="p-0"></td>
											<td class="p-0"></td>
											<td class="p-0"></td>
											<td class="p-0"></td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</main>
			@include('admin.partials.footer')
		</div>
	</div>


	<!-- User Details Modal -->
	@foreach($managedUsers as $user)
		<div class="modal fade userModal" id="userModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-light">
						<h5 class="modal-title">
							<i class="fas fa-user me-2"></i>
							Secondary User Details
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="editUserForm{{ $user->id }}" class="row g-4">
							@csrf
							@method('POST')

							<!-- Basic Information Card -->
							<div class="col-md-6">
								<div class="card h-100">
									<div class="card-header tbl-head-crd-header">
										<i class="fas fa-user-circle me-2"></i>Basic Information
									</div>
									<div class="card-body">
										<div class="mb-3">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" value="{{ $user->name }}">
										</div>
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input type="email" class="form-control" name="email" value="{{ $user->email }}"
												readonly>
										</div>
										<div class="mb-3">
											<label class="form-label">Phone</label>
											<input type="text" class="form-control" name="phone"
												value="{{ $user->userDetail->phone ?? '' }}">
										</div>
									</div>
								</div>
							</div>

							<!-- Company & Interests Information -->
							<div class="col-md-6">
								<div class="card h-100">
									<div class="card-header tbl-head-crd-header">
										<i class="fas fa-building me-2"></i>Company & Interests
									</div>
									<div class="card-body">
										<div class="mb-3">
											<label class="form-label">Company Name</label>
											<input type="text" class="form-control" name="company_name"
												value="{{ $user->userDetail?->company_name ?? '' }}" readonly>
										</div>
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
											<label class="form-label">Dealer Companies</label>
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

							<!-- Address Information -->
							<div class="col-12">
								<div class="card">
									<div class="card-header tbl-head-crd-header">
										<i class="fas fa-map-marker-alt me-2"></i>Address Information
									</div>
									<div class="card-body">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label">Address</label>
												<input type="text" class="form-control" name="address"
													value="{{ $user->userDetail?->address ?? '' }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Country</label>
												<select class="form-control select2" name="country"
													id="country_{{ $user->id }}">
													<option value="">Select Country</option>
													@if($user->userDetail?->country)
														<option value="{{ $user->userDetail->country }}" selected>
															{{ $user->userDetail->country()->first()->name ?? '' }}
														</option>
													@endif
												</select>
											</div>
											<div class="col-md-6">
												<label class="form-label">State</label>
												<select class="form-control select2" name="state" id="state_{{ $user->id }}">
													<option value="">Select State</option>
													@if($user->userDetail?->state)
														<option value="{{ $user->userDetail->state }}" selected>
															{{ $user->userDetail->state()->first()->name ?? '' }}
														</option>
													@endif
												</select>
											</div>
											<div class="col-md-6">
												<label class="form-label">City</label>
												<input type="text" class="form-control" name="city"
													value="{{ $user->userDetail?->city ?? '' }}">
											</div>
											<div class="col-md-6">
												<label class="form-label">Postal Code</label>
												<input type="text" class="form-control" name="postal_code"
													value="{{ $user->userDetail?->postal_code ?? '' }}">
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer bg-light">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="fas fa-times me-2"></i>Close
						</button>
						<button type="button" class="btn btn-primary save-user-details" data-user-id="{{ $user->id }}">
							<i class="fas fa-save me-2"></i>Save Changes
						</button>
					</div>
				</div>
			</div>
		</div>
	@endforeach

	<!-- Delete Confirmation Modal -->
	<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Confirm Delete</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>Are you sure you want to delete this secondary user? This action cannot be undone.</p>
				</div>
				<div class="modal-footer">
					<form id="deleteUserForm" method="POST">
						@csrf
						@method('DELETE')
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-danger">Delete User</button>
					</form>
				</div>
			</div>
		</div>
	</div>


	<!-- Invite User Modal -->
	<div class="modal fade" id="inviteUserModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">
						<i class="fas fa-user-plus me-2"></i>Invite Secondary User(s)
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form id="inviteUserForm" method="POST" action="{{ route('customer.invite-secondary') }}">
					@csrf
					<div class="modal-body">
						<div class="alert alert-info">
							<i class="fas fa-info-circle me-2"></i>
							Secondary users will be associated with your company and inherit your industry interests.
						</div>
						<div class="form-group mb-3">
							<label class="form-label">Email Address(es)</label>
							<div id="email-inputs-container">
								<div class="input-group mb-2">
									<input type="email" class="form-control email-input mb-3" name="emails[]" required
										placeholder="Enter email address">
									<button type="button" class="btn btn-success add-email-field">
										<i class="fas fa-plus"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer bg-light">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
							<i class="fas fa-times me-2"></i>Cancel
						</button>
						<button type="submit" class="btn btn-primary">
							<i class="fas fa-paper-plane me-2"></i>Send Invitation
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection
@push('scripts')
	<script>
		$(document).ready(function () {

			var table = $('#managedUsersdataTable').DataTable({
				responsive: true,
				order: [[0, 'asc']],
				pageLength: 25,
				language: {
					search: "Search users: ",
					lengthMenu: "Show _MENU_ users per page",
					info: "Showing _START_ to _END_ of _TOTAL_ users"
				},
				columnDefs: [
					{ orderable: false, targets: [4] },
					{
						targets: 0,
						orderable: false,
						render: function (data, type, row, meta) {
							return meta.row + meta.settings._iDisplayStart + 1;
						}
					}
				]
			});


			const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			tooltipTriggerList.map(function (tooltipTriggerEl) {
				return new bootstrap.Tooltip(tooltipTriggerEl);
			});


			$('.add-email-field').on('click', function () {
				const newField = `
								<div class="input-group mb-2">
									<input type="email" class="form-control email-input mb-3" name="emails[]" required
										placeholder="Enter email address">
									<button type="button" class="btn btn-danger remove-email-field">
										<i class="fas fa-minus"></i>
									</button>
								</div>
							`;
				$('#email-inputs-container').append(newField);
			});
			$(document).on('click', '.remove-email-field', function () {
				$(this).closest('.input-group').remove();
			});
			$('#inviteUserForm').on('submit', function (e) {
				e.preventDefault();
				const emails = $('.email-input').map(function () {
					return $(this).val();
				}).get();
				$.ajax({
					url: $(this).attr('action'),
					method: 'POST',
					data: {
						_token: '{{ csrf_token() }}',
						emails: emails
					},
					success: function (response) {
						$('#inviteUserModal').modal('hide');
						toastr.success(response.message);
						setTimeout(() => {
							window.location.reload();
						}, 1500);
					},
					error: function (xhr) {
						if (xhr.responseJSON && xhr.responseJSON.errors) {
							Object.values(xhr.responseJSON.errors).forEach(error => {
								toastr.error(error[0]);
							});
						} else {
							toastr.error(xhr.responseJSON.message || 'Error sending invitations');
						}
					}
				});
			});

			$('.save-user-details').on('click', function () {
				const userId = $(this).data('user-id');
				const form = $(`#editUserForm${userId}`);
				const formData = form.serialize();

				$.ajax({
					url: `/customer/managed-users/${userId}`,
					method: 'PUT',
					data: formData,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.success) {
							toastr.success(response.message);
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						}
					},
					error: function (xhr) {
						// if (xhr.responseJSON && xhr.responseJSON.errors) {
						// 	Object.values(xhr.responseJSON.errors).forEach(error => {
						// 		toastr.error(error[0]);
						// 	});
						// } else {
						toastr.error(xhr.responseJSON.message || 'Error updating user');
						// }
					}
				});
			});


			$('.toggle-status').on('click', function () {
				const userId = $(this).data('user-id');
				const button = $(this);

				$.ajax({
					url: `/customer/managed-users/${userId}/toggle-status`,
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.success) {
							toastr.success(response.message);
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						}
					},
					error: function (xhr) {
						toastr.error(xhr.responseJSON.message || 'Error updating user status');
					}
				});
			});


			$('.delete-user').on('click', function () {
				const userId = $(this).data('user-id');
				$('#deleteUserForm').attr('action', `/customer/managed-users/${userId}`);
				$('#deleteUserModal').modal('show');
			});


			$('#deleteUserForm').on('submit', function (e) {
				e.preventDefault();
				const form = $(this);

				$.ajax({
					url: form.attr('action'),
					method: 'DELETE',
					data: form.serialize(),
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.success) {
							$('#deleteUserModal').modal('hide');
							toastr.success(response.message);
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						}
					},
					error: function (xhr) {
						toastr.error(xhr.responseJSON.message || 'Error deleting user');
					}
				});
			});


			table.on('draw', function () {

				table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
					cell.innerHTML = i + 1;
				});
			});


			if ($('#managedUsersdataTable tbody tr').hasClass('no-data-tr')) {
				$('#managedUsersdataTable_wrapper').after(`
							<div class="text-center text-muted mt-3">
								<i class="fas fa-users fa-2x mb-2"></i>
								<p class="mb-0">No secondary users found</p>
							</div>
						`);
			}

			// Add this to your existing script section
			$('.userModal').on('shown.bs.modal', function () {
				const modalId = $(this).attr('id');
				const userId = modalId.replace('userModal', '');

				// Initialize Select2 for multiple selects
				$(`#${modalId} .select2-multiple`).select2({
					dropdownParent: $(`#${modalId}`),
					width: '100%'
				});

				// Initialize country select2
				$(`#country_${userId}`).select2({
					dropdownParent: $(`#${modalId}`),
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
										text: item.name
									};
								})
							};
						},
						cache: true
					}
				});

				// Initialize state select2
				$(`#state_${userId}`).select2({
					dropdownParent: $(`#${modalId}`),
					placeholder: "Select State",
					allowClear: true,
					ajax: {
						url: '/api/states/search',
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term || '',
								country_id: $(`#country_${userId}`).val(),
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

				// Handle country change
				$(`#country_${userId}`).on('change', function () {
					$(`#state_${userId}`).val(null).trigger('change');
				});
			});


		});
	</script>
@endpush