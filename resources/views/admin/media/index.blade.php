@extends('layouts.app')
@section('title', 'Media Management')
@section('content')
	@include('admin.partials.nav')
	<div id="layoutSidenav">
		@include('admin.partials.sidenav')
		<div id="layoutSidenav_content">
			<main>
				<div class="container-fluid px-4">
					<div class="d-flex justify-content-between align-items-center mt-5">
						<h1>Media Files</h1>
						<div class="d-flex gap-2">
							<div id="defaultButtons">
								<button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
									<i class="fas fa-upload me-2"></i>Add Files
								</button>
							</div>
							<div id="groupButtons" style="display: none;">
								<button class="btn cust-dashb-btn bulk-edit">
									<i class="fas fa-edit me-2"></i>Edit Group
								</button>
								<button class="btn btn-danger bulk-delete">
									<i class="fas fa-minus-circle me-2"></i>Remove Group
								</button>
							</div>
						</div>
					</div>
					<div class="row mb-4">
						<div class="col-md-8">
							<div class="btn-group">
								<button class="btn btn-filter active" data-filter="all">All</button>
								<button class="btn btn-filter" data-filter="document">Documents</button>
								<button class="btn btn-filter" data-filter="video">Videos</button>
								<button class="btn btn-filter" data-filter="image">Images</button>
							</div>
							<div class="btn-group ms-2">
								<button class="btn btn-outline-secondary view-toggle active" data-view="grid">
									<i class="fas fa-th-large"></i>
								</button>
								<button class="btn btn-outline-secondary view-toggle" data-view="list">
									<i class="fas fa-list"></i>
								</button>
							</div>
						</div>
						<div class="col-md-4">
							<div class="input-group">
								<select class="form-control" id="groupFilter">
									<option value="">All Groups</option>
								</select>
								<div class="group-actions ms-2" style="display: none;">
									<button class="btn btn-outline-primary btn-sm" id="addToGroup">
										<i class="fas fa-plus me-1"></i>Add Files
									</button>
									<button class="btn btn-outline-danger btn-sm" id="removeFromGroup">
										<i class="fas fa-minus me-1"></i>Remove Files
									</button>
									<button class="btn btn-outline-secondary btn-sm" id="editGroupFiles">
										<i class="fas fa-edit me-1"></i>Edit Files
									</button>
								</div>
							</div>
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md-12">
							<input type="text" class="form-control" id="tagFilter" placeholder="Filter by tag...">
						</div>
					</div>
					<div class="media-grid-container">
						<div class="loading-overlay" style="display: none;">
							<div class="text-center">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
								<div class="mt-2">Loading media files...</div>
							</div>
						</div>
						<div class="row" id="mediaGrid">
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>

	<!-- Upload Modal -->
	<div class="modal fade" id="uploadModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form id="uploadForm" enctype="multipart/form-data">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title">Upload Media</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group mb-3">
									<label>Title</label>
									<input type="text" name="title" class="form-control" required>
								</div>
								<div class="form-group mb-3">
									<label>Description</label>
									<textarea name="description" class="form-control" rows="3"></textarea>
								</div>
								<div class="form-group mb-3">
									<label>Group *</label>
									<input type="text" class="form-control" name="group_name" id="upload_group" required
										placeholder="Enter group name">
									<div class="invalid-feedback">Please enter a group name</div>
								</div>
								<div class="form-group mb-3">
									<label>Tags</label>
									<input type="text" name="tags" class="form-control"
										placeholder="Enter tags separated by commas">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group mb-3">
									<label>Industries</label>
									<select class="form-control select2-multiple" id="upload_industry_interests"
										name="industry_ids[]" multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label>Dealer Companies</label>
									<select class="form-control select2-multiple" id="upload_dealer_id" name="dealer_ids[]"
										multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label>Manufacturers</label>
									<select class="form-control select2-multiple" id="upload_manufacturer_id"
										name="manufacturer_ids[]" multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label class="d-flex justify-content-between align-items-center">
										<span>Assigned Users <span
												class="badge bg-secondary ms-2 assigned-users-count">0</span></span>
									</label>
									<div class="assigned-users-list p-2 border rounded bg-light"
										style="max-height: 200px; overflow-y: auto;">
										<div class="text-muted small">
											<i class="fas fa-info-circle me-1"></i>
											Select industries and dealers to see assigned users
										</div>
									</div>
									<small class="text-muted">
										<i class="fas fa-user-shield me-1"></i>
										Only approved users with matching industries and dealers will have access
									</small>
								</div>
								<div class="form-check mb-3">
									<input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
									<label class="form-check-label" for="is_featured">Featured Media</label>
								</div>
							</div>
							<div class="col-md-12">
								<div id="mediaDropzone" class="dropzone"></div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Upload</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<form id="editForm">
					@csrf
					<input type="hidden" name="media_id" id="edit_media_id">
					<div class="modal-header">
						<h5 class="modal-title">Edit Media</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group mb-3">
									<label>Title</label>
									<input type="text" name="title" id="edit_title" class="form-control" required>
								</div>
								<div class="form-group mb-3">
									<label>Description</label>
									<textarea name="description" id="edit_description" class="form-control"
										rows="3"></textarea>
								</div>
								<div class="form-group mb-3">
									<label>Group</label>
									<input type="text" class="form-control" name="group_name" id="edit_group" required
										placeholder="Enter group name">
								</div>
								<div class="form-group mb-3">
									<label>Tags</label>
									<input type="text" name="tags" id="edit_tags" class="form-control"
										placeholder="Enter tags separated by commas">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group mb-3">
									<label>Industries</label>
									<select class="form-control select2-multiple" id="edit_industry_interests"
										name="industry_ids[]" multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label>Dealer Companies</label>
									<select class="form-control select2-multiple" id="edit_dealer_id" name="dealer_ids[]"
										multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label>Manufacturers</label>
									<select class="form-control select2-multiple" id="edit_manufacturer_id"
										name="manufacturer_ids[]" multiple="multiple">
									</select>
								</div>
								<div class="form-group mb-3">
									<label class="d-flex justify-content-between align-items-center">
										<span>Assigned Users <span
												class="badge bg-secondary ms-2 assigned-users-count">0</span></span>
									</label>
									<div class="assigned-users-list p-2 border rounded bg-light"
										style="max-height: 200px; overflow-y: auto;">
										<div class="text-muted small">
											<i class="fas fa-info-circle me-1"></i>
											Select industries and dealers to see assigned users
										</div>
									</div>
									<small class="text-muted">
										<i class="fas fa-user-shield me-1"></i>
										Only approved users with matching industries and dealers will have access
									</small>
								</div>
								<div class="form-check mb-3">
									<input type="checkbox" class="form-check-input" id="edit_is_featured"
										name="is_featured">
									<label class="form-check-label" for="edit_is_featured">Featured Media</label>
								</div>
							</div>
							<div class="col-md-12">
								<label>Current Files</label>
								<div id="editMediaDropzone" class="dropzone"></div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Preview Modal -->

	<!-- Replace the Preview Modal HTML with: -->
	<div class="modal fade" id="previewModal">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header border-0">
					<h5 class="modal-title"></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<!-- Add engagement section at the top -->
				<div class="engagement-section p-3 border-bottom">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<h6 class="media-title mb-0"></h6>
						<small class="text-muted media-date"></small>
					</div>
					<div class="engagement-stats d-flex gap-2">
						<button class="btn btn-light flex-grow-1">
							<i class="far fa-heart"></i>
							<span class="likes-count ms-1">0</span> likes
						</button>
						<button class="btn btn-light flex-grow-1">
							<i class="far fa-eye"></i>
							<span class="views-count ms-1">0</span> views
						</button>
						<a href="#" class="btn btn-light flex-grow-1 download-btn" download>
							<i class="fas fa-download"></i>
							<span class="ms-1">Download</span>
						</a>
					</div>
				</div>
				<div class="modal-body p-0">
					<div id="previewContent" class="d-flex align-items-center justify-content-center"
						style="min-height: 500px;">
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		Dropzone.autoDiscover = false;
		let currentView = 'grid';
		$(document).ready(function () {
			$('#mediaGrid').addClass('grid-view');
			let isGroupSelected = false;
			$('.preview-container').attr('role', 'button');
			$('.preview-container').attr('tabindex', '0');
			$('.preview-container').attr('aria-label', function () {
				const file = $(this).data('file');
				return `Preview ${file.title}`;
			});
			$('.view-toggle').click(function () {
				const viewType = $(this).data('view');
				if (currentView !== viewType) {
					currentView = viewType;
					$('.view-toggle').removeClass('active');
					$(this).addClass('active');
					$('#mediaGrid').removeClass('grid-view list-view').addClass(`${viewType}-view`);
					const currentGroup = $('#groupFilter').val();
					const currentType = $('.btn-filter.active').data('filter') || 'all';
					const currentTag = $('#tagFilter').val();
					loadMedia(currentType, currentGroup, currentTag);
				}
			});
			let myDropzone = new Dropzone("#mediaDropzone", {
				url: "/admin/media",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				paramName: "files",
				maxFilesize: 500,
				uploadMultiple: true,
				parallelUploads: 10,
				acceptedFiles: "image/*,video/mp4,application/pdf,.docx,.csv,.xlsx,.xls",
				dictInvalidFileType: "This file type is not allowed. Please upload only images, videos (MP4), PDF, DOCX, CSV, or Excel files.",
				autoProcessQueue: false,
				addRemoveLinks: true,
				init: function () {
					let myDropzone = this;
					$("#uploadForm").on("submit", function (e) {
						e.preventDefault();
						e.stopPropagation();
						if (myDropzone.files.length === 0) {
							toastr.error('Please upload at least one file');
							return false;
						}
						let formData = new FormData(this);
						myDropzone.files.forEach(function (file) {
							file.formData = formData;
						});
						myDropzone.processQueue();
					});
					this.on("sending", function (file, xhr, formData) {
						let uploadFormData = file.formData;
						if (uploadFormData) {
							for (let pair of uploadFormData.entries()) {
								formData.append(pair[0], pair[1]);
							}
						}
					});
					this.on("queuecomplete", function () {
						if (this.getUploadingFiles().length === 0 && this.getQueuedFiles()
							.length === 0) {
							toastr.success('Files uploaded successfully');
							$('#uploadModal').modal('hide');
							loadMedia();
							loadGroups();
							this.removeAllFiles();
						}
					});
					this.on("success", function (file, response) {

					});
					this.on("error", function (file, errorMessage) {
						console.error("Upload error:", errorMessage);
						toastr.error(errorMessage);
					});
					this.on("uploadprogress", function (file, progress, bytesSent) {

					});
				}
			});
			let editDropzone = new Dropzone("#editMediaDropzone", {
				url: "/admin/media/bulk-update",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				paramName: "files",
				maxFilesize: 500,
				uploadMultiple: true,
				parallelUploads: 10,
				acceptedFiles: "image/*,video/mp4,application/pdf,.docx,.csv,.xlsx,.xls",
				dictInvalidFileType: "This file type is not allowed. Please upload only images, videos (MP4), PDF, DOCX, CSV, or Excel files.",
				autoProcessQueue: false,
				addRemoveLinks: true,
				init: function () {
					let editDropzone = this;
					$("#editForm").on("submit", function (e) {
						e.preventDefault();
						e.stopPropagation();
						showLoading();
						if (editDropzone.files.length === 0) {
							hideLoading();
							toastr.error('Please upload at least one file');
							return false;
						}
						let formData = new FormData(this);
						const remainingFileIds = editDropzone.files
							.filter(file => file.id)
							.map(file => file.id);

						if (remainingFileIds.length > 0) {
							remainingFileIds.forEach(id => {
								formData.append('media_ids[]', id);
							});
						} else {
							formData.append('media_ids[]', '');
						}
						const newFiles = editDropzone.files.filter(file => !file.id);
						if (newFiles.length > 0) {
							newFiles.forEach(file => {
								formData.append('files[]', file);
							});
						}
						$.ajax({
							url: '/admin/media/bulk-update',
							type: 'POST',
							data: formData,
							processData: false,
							contentType: false,
							success: function (response) {
								hideLoading();
								toastr.success('Files updated successfully');
								$('#editModal').modal('hide');
								const currentGroup = $('#groupFilter').val();
								const currentType = $('.btn-filter.active').data('filter') || 'all';
								const currentTag = $('#tagFilter').val();
								loadGroups();
								loadMedia(currentType, currentGroup, currentTag);
							},
							error: function (xhr) {
								hideLoading();
								const errorMessage = xhr.responseJSON?.message || xhr.responseJSON?.error || 'Unknown error';
								toastr.error('Error updating files: ' + errorMessage);
								console.error('Update error:', xhr.responseText);
							}
						});
					});
					this.on("removedfile", function (file) {

					});
				}
			});

			function submitEditForm(formData) {
				$.ajax({
					url: '/admin/media/bulk-update',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					success: function (response) {
						hideLoading();
						toastr.success('Files updated successfully');
						$('#editModal').modal('hide');
						loadMedia();
						loadGroups();
						editDropzone.removeAllFiles();
					},
					error: function (xhr) {
						hideLoading();
						if (xhr.responseJSON && xhr.responseJSON.errors) {
							Object.values(xhr.responseJSON.errors).forEach(errorMessages => {
								errorMessages.forEach(message => toastr.error(message));
							});
						} else {
							toastr.error('Error updating files: ' + (xhr.responseJSON?.error ||
								'Unknown error'));
						}
						console.error('Update error:', xhr.responseText);
					}
				});
			}
			$('#uploadForm').on('submit', function (e) {
				e.preventDefault();
				e.stopPropagation();
			});
			myDropzone.on("sending", function (file, xhr, formData) {
				if (file.formData) {
					for (let pair of file.formData.entries()) {
						formData.append(pair[0], pair[1]);
					}
				}
			});
			myDropzone.on("success", function () {
				if (myDropzone.getQueuedFiles().length === 0) {
					toastr.success('Files uploaded successfully');
					$('#uploadModal').modal('hide');
					(loadMedia)();
					loadGroups();
				}
			});
			myDropzone.on("complete", function () {
				if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
					loadMedia($('.btn-filter.active').data('filter'));
					loadGroups();
				}
			});
			myDropzone.on("error", function (file, message) {
				toastr.error('Upload failed: ' + message);
			});

			$('#uploadModal').on('hidden.bs.modal', function () {
				$('#upload_group')
					.val('')
					.prop('disabled', false);
			});

			function loadMedia(type = 'all', group = '', tag = '') {
				showLoading();
				$.ajax({
					url: '/admin/media/files',
					type: 'GET',
					data: {
						type,
						group,
						tag
					},
					success: function (response) {
						const grid = $('#mediaGrid');
						grid.empty();


						grid.removeClass('grid-view list-view').addClass(`${currentView}-view`);

						if (!response.data || response.data.length === 0) {
							grid.html(
								`
							<div class="col-12 text-center py-5">
								<i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
								<h4 class="text-muted">No media files found</h4>
								<p class="text-muted">No files match the selected filters</p>
							</div>
							`
							);
						} else {
							response.data.forEach(file => {
								grid.append(createMediaCard(file, currentView));
							});
						}
						hideLoading();
					},
					error: function (xhr) {
						hideLoading();
						toastr.error('Error loading media files');
					}
				});
			}
			let selectedMediaIds = [];

			function createMediaCard(file, viewType = 'grid') {
				let fileIcon, fileTypeClass;
				// Determine file icon and class
				if (file.file_type === 'image') {
					fileIcon = 'fa-image';
					fileTypeClass = 'text-info';
				} else if (file.file_type === 'video') {
					fileIcon = 'fa-video';
					fileTypeClass = 'text-danger';
				} else {
					if (file.mime_type) {
						if (file.mime_type.includes('pdf')) {
							fileIcon = 'fa-file-pdf';
							fileTypeClass = 'text-danger';
						} else if (file.mime_type.includes('word')) {
							fileIcon = 'fa-file-word';
							fileTypeClass = 'text-primary';
						} else if (file.mime_type.includes('excel')) {
							fileIcon = 'fa-file-excel';
							fileTypeClass = 'text-success';
						} else if (file.mime_type.includes('powerpoint')) {
							fileIcon = 'fa-file-powerpoint';
							fileTypeClass = 'text-warning';
						} else if (file.mime_type === 'application/msword' || file.mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
							fileIcon = 'fa-file-word';
							fileTypeClass = 'text-primary';
						} else {
							fileIcon = 'fa-file-alt';
							fileTypeClass = 'text-secondary';
						}
					} else {
						fileIcon = 'fa-file-alt';
						fileTypeClass = 'text-secondary';
					}
				}
				const checkboxHtml = isGroupSelected ? `
							<div class="form-check me-2">
								<input type="checkbox" class="form-check-input media-checkbox"
									data-id="${file.id}"
									style="cursor: pointer;">
							</div>` : '';

				if (viewType === 'list') {
					return `
								<div class="col-12 mb-2">
									<div class="card ${file.is_featured ? 'border-primary' : ''} h-100">
										<div class="card-body p-3">
											<div class="row align-items-center">
												${isGroupSelected ? `
													<div class="col-auto">
														${checkboxHtml}
													</div>` : ''}
												<div class="col-auto preview-container" data-file='${JSON.stringify(file)}'>
													${file.file_type === 'image' ? `
														<img src="${file.thumbnail_url}" class="img-thumbnail" style="height: 60px; width: 60px; object-fit: cover;" loading="lazy" data-full-url="${file.url}">
													` : `
														<div class="document-thumbnail d-flex align-items-center justify-content-center"
															style="height: 60px; width: 60px; background-color: #f8f9fa;">
															<i class="fas ${fileIcon} ${fileTypeClass} fa-2x"></i>
														</div>
													`}
													<div class="overlay position-absolute top-0 start-0 w-100 h-100"
														style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s;">
														<div class="d-flex align-items-center justify-content-center h-100">
															<i class="fas fa-eye text-white"></i>
														</div>
													</div>
												</div>
												<div class="col">
													<div class="d-flex justify-content-between align-items-center">
														<h5 class="mb-0 text-truncate" title="${file.title}">${file.title}</h5>
														${file.is_featured ? '<i class="fas fa-star text-warning"></i>' : ''}
													</div>
													<p class="mb-0 small text-muted text-truncate">${file.description || ''}</p>

													<!-- List View Accordion -->
													<div class="accordion accordion-flush mt-2" id="mediaListAccordion${file.id}">
														<div class="row">
															<div class="col-md-6">
																<div class="accordion-item">
																	<h2 class="accordion-header">
																		<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
																			data-bs-target="#industriesListCollapse${file.id}">
																			Industries (${Array.isArray(file.industries) ? file.industries.length : 0})
																		</button>
																	</h2>
																	<div id="industriesListCollapse${file.id}" class="accordion-collapse collapse"
																		data-bs-parent="#mediaListAccordion${file.id}">
																		<div class="accordion-body p-2">
																			${Array.isArray(file.industries) && file.industries.length > 0
							? file.industries.map(industry =>
								`<span class="badge bg-primary me-1 mb-1">${industry.name}</span>`
							).join('')
							: '<span class="text-muted small">No industries assigned</span>'
						}
																		</div>
																	</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="accordion-item">
																	<h2 class="accordion-header">
																		<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
																			data-bs-target="#dealersListCollapse${file.id}">
																			Dealers (${Array.isArray(file.dealers) ? file.dealers.length : 0})
																		</button>
																	</h2>
																	<div id="dealersListCollapse${file.id}" class="accordion-collapse collapse"
																		data-bs-parent="#mediaListAccordion${file.id}">
																		<div class="accordion-body p-2">
																			${Array.isArray(file.dealers) && file.dealers.length > 0
							? file.dealers.map(dealer =>
								`<span class="badge bg-warning me-1 mb-1" style="color:black">${dealer.name}</span>`
							).join('')
							: '<span class="text-muted small">No dealers assigned</span>'
						}
																		</div>
																	</div>
																</div>
															</div>
															<div class="col-md-6">
																<div class="accordion-item">
																	<h2 class="accordion-header">
																		<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
																			data-bs-target="#manufacturersListCollapse${file.id}">
																			Manufacturers (${Array.isArray(file.manufacturers) ? file.manufacturers.length : 0})
																		</button>
																	</h2>
																	<div id="manufacturersListCollapse${file.id}" class="accordion-collapse collapse"
																		data-bs-parent="#mediaListAccordion${file.id}">
																		<div class="accordion-body p-2">
																			${Array.isArray(file.manufacturers) && file.manufacturers.length > 0
							? file.manufacturers.map(manufacturer =>
								`<span class="badge bg-warning me-1 mb-1" style="color:black">${manufacturer.name}</span>`
							).join('')
							: '<span class="text-muted small">No manufacturers assigned</span>'
						}
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="col-auto">
													${file.group ? `
														<span class="badge bg-light text-dark">
															<i class="fas fa-folder me-1"></i>${file.group.name}
														</span>` : ''}
												</div>
											</div>
										</div>
									</div>
								</div>`;
				} else {
					// Grid view
					return `
								<div class="col-md-3 mb-4">
									<div class="card h-100 ${file.is_featured ? 'border-primary' : ''}" style="height: 100%;">
										${file.is_featured ? '<span class="position-absolute top-0 end-0 p-2"><i class="fas fa-star text-warning"></i></span>' : ''}
										${isGroupSelected ? `
											<div class="position-absolute top-0 start-0 m-2" style="z-index: 2;">
												${checkboxHtml}
											</div>` : ''}
										<div class="preview-container position-relative" data-file='${JSON.stringify(file)}'>
											${file.file_type === 'image' ? `
												<div style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
													<img src="${file.thumbnail_url}" class="card-img-top" style="object-fit: cover; width: 100%; height: 100%;"
														onerror="this.onerror=null; this.src='/path/to/fallback-image.jpg';" loading="lazy" data-full-url="${file.url}">
												</div>` :
							file.file_type === 'video' ? `
												<div class="video-preview" style="height: 200px; background-color: #f8f9fa;">
													<video class="plyr" playsinline controls style="width: 100%; height: 100%; object-fit: cover;">
														<source src="${file.url}" type="${file.mime_type}">
													</video>
												</div>` : `
												<div class="document-thumbnail d-flex align-items-center justify-content-center"
													style="height: 200px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
													<div class="text-center">
														<i class="fas ${fileIcon} fa-4x mb-2" style="color: ${fileTypeClass === 'text-info' ? '#0dcaf0' :
								fileTypeClass === 'text-danger' ? '#dc3545' :
									fileTypeClass === 'text-primary' ? '#0d6efd' :
										fileTypeClass === 'text-success' ? '#198754' :
											fileTypeClass === 'text-warning' ? '#ffc107' : '#6c757d'};"></i>
														<div class="small text-muted mt-2">${file.mime_type ? file.mime_type.split('/').pop().toUpperCase() : 'UNKNOWN'}</div>
													</div>
												</div>`
						}
											<div class="overlay position-absolute top-0 start-0 w-100 h-100"
												style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s; cursor: pointer;">
												<div class="d-flex align-items-center justify-content-center h-100">
													<i class="fas fa-eye text-white fa-2x"></i>
												</div>
											</div>
										</div>
										<div class="card-body">
											<h5 class="card-title text-truncate" title="${file.title}">${file.title}</h5>
											<p class="card-text small text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
												${file.description || ''}
											</p>

											<!-- Grid View Accordion -->
											<div class="accordion accordion-flush mt-2" id="mediaAccordion${file.id}">
												<div class="accordion-item">
													<h2 class="accordion-header">
														<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
															data-bs-target="#industriesCollapse${file.id}">
															Industries (${Array.isArray(file.industries) ? file.industries.length : 0})
														</button>
													</h2>
													<div id="industriesCollapse${file.id}" class="accordion-collapse collapse"
														data-bs-parent="#mediaAccordion${file.id}">
														<div class="accordion-body p-2">
															${Array.isArray(file.industries) && file.industries.length > 0
							? file.industries.map(industry =>
								`<span class="badge bg-primary me-1 mb-1">${industry.name}</span>`
							).join('')
							: '<span class="text-muted small">No industries assigned</span>'
						}
														</div>
													</div>
												</div>

												<div class="accordion-item">
													<h2 class="accordion-header">
														<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
															data-bs-target="#dealersCollapse${file.id}">
															Dealers (${Array.isArray(file.dealers) ? file.dealers.length : 0})
														</button>
													</h2>
													<div id="dealersCollapse${file.id}" class="accordion-collapse collapse"
														data-bs-parent="#mediaAccordion${file.id}">
														<div class="accordion-body p-2">
															${Array.isArray(file.dealers) && file.dealers.length > 0
							? file.dealers.map(dealer =>
								`<span class="badge bg-warning me-1 mb-1" style="color:black">${dealer.name}</span>`
							).join('')
							: '<span class="text-muted small">No dealers assigned</span>'
						}
														</div>
													</div>
												</div>
												<div class="accordion-item">
													<h2 class="accordion-header">
														<button class="accordion-button collapsed p-2" type="button" data-bs-toggle="collapse"
															data-bs-target="#manufacturersCollapse${file.id}">
															Manufacturers (${Array.isArray(file.manufacturers) ? file.manufacturers.length : 0})
														</button>
													</h2>
													<div id="manufacturersCollapse${file.id}" class="accordion-collapse collapse"
														data-bs-parent="#mediaAccordion${file.id}">
														<div class="accordion-body p-2">
															${Array.isArray(file.manufacturers) && file.manufacturers.length > 0
							? file.manufacturers.map(manufacturer =>
								`<span class="badge bg-warning me-1 mb-1" style="color:black">${manufacturer.name}</span>`
							).join('')
							: '<span class="text-muted small">No manufacturers assigned</span>'
						}
														</div>
													</div>
												</div>
											</div>
										</div>
										${file.group ? `
											<div class="card-footer bg-transparent">
												<small class="text-muted">
													<i class="fas fa-folder"></i> ${file.group.name}
												</small>
											</div>
										` : ''}
									</div>
								</div>`;
				}
			}
			$(document).on('change', '.media-checkbox', function () {
				if (!isGroupSelected) {
					$(this).prop('checked', false);
					return;
				}

				selectedMediaIds = $('.media-checkbox:checked').map(function () {
					return $(this).data('id');
				}).get();

				const count = selectedMediaIds.length;
				$('.selected-count').text(count ? `${count} item${count > 1 ? 's' : ''} selected` : '');
				$('.bulk-actions').toggle(count > 0 && isGroupSelected);
				updateBulkActionButtons();
			});

			$('.bulk-edit').click(function () {
				const selectedGroup = $('#groupFilter').val();
				if (!selectedGroup) {
					toastr.warning('Please select a group first');
					return;
				}
				showLoading();
				$.ajax({
					url: '/admin/media/group-info',
					type: 'POST',
					data: {
						_token: $('meta[name="csrf-token"]').attr('content'),
						group_name: selectedGroup
					},
					success: function (response) {
						if (response && response.data) {
							editDropzone.removeAllFiles();
							const fileIds = response.data.files.map(file => file.id);
							$('#edit_media_id').val(fileIds.join(','));
							$('#edit_title').val(response.data.title || '');
							$('#edit_description').val(response.data.description || '');
							$('#edit_group').val(selectedGroup);
							$('#edit_tags').val(Array.isArray(response.data.tags) ? response.data.tags.join(', ') : '');
							$('#edit_is_featured').prop('checked', !!response.data.is_featured);
							if (response.data.files && Array.isArray(response.data.files)) {
								response.data.files.forEach(file => {
									const mockFile = {
										name: file.title || 'Untitled',
										size: file.size || 0,
										type: file.mime_type,
										id: file.id,
										status: "success",
										accepted: true,
										url: file.url
									};
									editDropzone.emit("addedfile", mockFile);
									if (file.file_type === 'image') {
										editDropzone.emit("thumbnail", mockFile, file
											.url);
									}
									editDropzone.emit("complete", mockFile);
									editDropzone.files.push(mockFile);
								});
							}

							if (response.data.industries) {
								const industryOptions = response.data.industries.map(industry =>
								({
									id: industry.id,
									text: industry.name
								}));
								$('#edit_industry_interests').empty();
								industryOptions.forEach(option => {
									const newOption = new Option(option.text, option.id, true, true);
									$('#edit_industry_interests').append(newOption);
								});
								$('#edit_industry_interests').trigger('change');
							}

							if (response.data.dealers) {
								const dealerOptions = response.data.dealers.map(dealer => ({
									id: dealer.id,
									text: dealer.name
								}));
								$('#edit_dealer_id').empty();
								dealerOptions.forEach(option => {
									const newOption = new Option(option.text, option.id, true, true);
									$('#edit_dealer_id').append(newOption);
								});
								$('#edit_dealer_id').trigger('change');
							}

							if (response.data.manufacturers) {
								const manufacturerOptions = response.data.manufacturers.map(manufacturer => ({
									id: manufacturer.id,
									text: manufacturer.name
								}));
								$('#edit_manufacturer_id').empty();
								manufacturerOptions.forEach(option => {
									const newOption = new Option(option.text, option.id, true, true);
									$('#edit_manufacturer_id').append(newOption);
								});
								$('#edit_manufacturer_id').trigger('change');
							}
							$('#editModal').modal('show');
						}
						hideLoading();
					},
					error: function (xhr) {
						hideLoading();
						toastr.error('Error loading group information');
						console.error('Error:', xhr.responseText);
					}
				});
			});

			$('.bulk-delete').click(function () {
				const selectedGroup = $('#groupFilter').val();
				if (!selectedGroup) {
					toastr.warning('Please select a group first');
					return;
				}
				if (confirm(
					'Are you sure you want to delete this group? This will permanently delete all files in this group.'
				)) {
					showLoading();
					$.ajax({
						url: `/admin/media/groups/${selectedGroup}`,
						type: 'DELETE',
						data: {
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: function () {
							toastr.success('Group and associated files deleted successfully');
							$('#groupFilter').val('').trigger('change');
							loadMedia();
							loadGroups();
						},
						error: function (xhr) {
							toastr.error(xhr.responseJSON?.message || 'Error deleting group');
						},
						complete: function () {
							hideLoading();
						}
					});
				}
			});

			function loadGroups() {
				showLoading();
				$.ajax({
					url: '/admin/media/groups',
					method: 'GET',
					success: function (response) {
						if (response.data) {
							const groups = response.data;
							const groupOptions = ['<option value="">Select group</option>'];
							groups.forEach(group => {
								groupOptions.push(
									`<option value="${group.name}">${group.name} (${group.files_count || 0})</option>`
								);
							});
							const currentSelections = {
								groupFilter: $('#groupFilter').val(),
							};
							$('#groupFilter, #upload_group').each(function () {
								const elementId = $(this).attr('id');
								const currentVal = currentSelections[elementId];
								$(this).html(groupOptions.join(''));
								if (currentVal) {
									$(this).val(currentVal);
								}
								if ($(this).hasClass('select2-hidden-accessible')) {
									$(this).trigger('change');
								}
							});
						}
					},
					error: function (xhr, status, error) {
						console.error('Error loading groups:', error);
						console.error('Response:', xhr.responseJSON);
						toastr.error('Failed to load groups: ' + (xhr.responseJSON?.message || error));
					},
					complete: function () {
						hideLoading();
					}
				});
			}

			let tagFilterTimeout;
			$('#tagFilter').on('input', function () {
				clearTimeout(tagFilterTimeout);
				const tagValue = $(this).val().trim();

				tagFilterTimeout = setTimeout(() => {
					showLoading();
					const currentGroup = $('#groupFilter').val();
					const currentType = $('.btn-filter.active').data('filter') || 'all';
					loadMedia(currentType, currentGroup, tagValue);
				}, 500);
			});

			$('#upload_industry_interests').select2({
				tags: false,
				placeholder: "Search and select industries",
				multiple: true,
				dropdownParent: $('#uploadModal'),
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
							results: data.map(item => ({
								id: item.id,
								text: item.name
							}))
						};
					},
					cache: true
				}
			});

			$('#upload_dealer_id').select2({
				tags: false,
				placeholder: "Search and select dealers",
				multiple: true,
				dropdownParent: $('#uploadModal'),
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
							results: data.map(item => ({
								id: item.id,
								text: item.name
							}))
						};
					},
					cache: true
				}
			});

			$('#upload_manufacturer_id').select2({
				tags: false,
				placeholder: "Search and select manufacturers",
				multiple: true,
				dropdownParent: $('#uploadModal'),
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
						return {
							results: data.map(item => ({
								id: item.id,
								text: item.name
							}))
						};
					},
					cache: true
				}
			});

			$('#previewModal').on('keydown', function (e) {
				if (e.key === 'Escape') {
					$(this).modal('hide');
				}
				if (e.key === 'Tab') {
					const focusableElements = $(this).find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
					const firstFocusableElement = focusableElements[0];
					const lastFocusableElement = focusableElements[focusableElements.length - 1];
					if (e.shiftKey) {
						if (document.activeElement === firstFocusableElement) {
							lastFocusableElement.focus();
							e.preventDefault();
						}
					} else {
						if (document.activeElement === lastFocusableElement) {
							firstFocusableElement.focus();
							e.preventDefault();
						}
					}
				}
			});
			$('.preview-container').hover(
				function () {
					$(this).find('.overlay').css('opacity', '1');
				},
				function () {
					$(this).find('.overlay').css('opacity', '0');
				}
			);

			$(document).on('click', '.preview-container', function (e) {
				const file = $(this).data('file');
				const modal = $('#previewModal');
				modal.find('.modal-title').text(file.title);
				const content = modal.find('#previewContent');
				content.empty();
				modal.data('media-id', file.id);
				const downloadBtn = modal.find('.download-btn');
				downloadBtn.attr('href', file.url);
				const filename = file.title + '.' + file.url.split('.').pop();
				downloadBtn.attr('download', filename);
				if (file.file_type === 'image') {
					downloadBtn.off('click').on('click', function (e) {
						e.preventDefault();
						const tempLink = document.createElement('a');
						tempLink.href = file.url;
						tempLink.download = filename;
						document.body.appendChild(tempLink);
						tempLink.click();
						document.body.removeChild(tempLink);
					});
				} else {
					downloadBtn.off('click');
				}
				if (file.file_type === 'image') {
					content.html(`
							<div class="image-preview-container d-flex align-items-center justify-content-center w-100 h-100">
								<img src="${file.url}" class="img-fluid" style="max-height: calc(100vh - 56px); object-fit: contain; max-width: 100%;">
							</div>
						`);
				} else if (file.file_type === 'video') {
					content.html(
						`
										<video controls class="w-100">
											<source src="${file.url}">
											Your browser does not support the video tag.
										</video>
										`
					);
				} else if (file.file_type === 'document') {
					const extension = file.url.split('.').pop().toLowerCase();
					switch (extension) {
						case 'pdf':
							content.html(
								`
												<div class="pdf-container" style="width: 100%; height: 600px;">
													<object data="${file.url}" type="application/pdf" width="100%" height="100%">
														<embed src="${file.url}" type="application/pdf" width="100%" height="100%">
															<p>This browser does not support PDFs. Please download the PDF to view it:
																<a href="${file.url}" download>Download PDF</a>
															</p>
														</embed>
													</object>
												</div>
												`
							);
							break;
						case 'csv':
							content.html(`<div class="loader">Loading CSV...</div>`);
							content.removeClass('bg-black').addClass('bg-white');
							Papa.parse(file.url, {
								download: true,
								complete: function (results) {
									let html =
										'<div class="table-responsive bg-white" style="max-height: 600px; overflow-y: auto;">';
									html +=
										'<table class="table table-bordered table-hover table-striped">';
									results.data.forEach((row, index) => {
										html += '<tr>';
										if (index === 0) {
											row.forEach(cell => html +=
												`<th class="bg-light">${cell}</th>`);
										} else {
											row.forEach(cell => html +=
												`<td>${cell}</td>`);
										}
										html += '</tr>';
									});
									html += '</table></div>';
									content.html(html);
								},
								error: function (error) {
									content.html(
										`
														<div class="alert alert-danger">
															Error loading CSV file. <a href="${file.url}" download>Download instead</a>
														</div>
														`
									);
								}
							});
							break;
						case 'xlsx':
						case 'xls':
							content.html(`<div class="loader">Loading Excel file...</div>`);
							content.removeClass('bg-black').addClass('bg-white');
							fetch(file.url)
								.then(response => response.arrayBuffer())
								.then(data => {
									const workbook = XLSX.read(new Uint8Array(data), { type: 'array' });
									let html = '<div class="excel-preview bg-white" style="max-height: 600px; overflow-y: auto;">';

									workbook.SheetNames.forEach(sheetName => {
										const worksheet = workbook.Sheets[sheetName];
										const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });


										const columnCount = Math.max(...jsonData.map(row => row.length));
										const tableClass = columnCount <= 4 ? 'table table-bordered table-hover table-striped columns-few'
											: 'table table-bordered table-hover table-striped';

										html += `
										<div class="sheet-container bg-white p-3">
											<h5 class="text-dark mb-3">Sheet: ${sheetName}</h5>
											<div class="table-responsive">
												<table class="${tableClass}">
									`;
										jsonData.forEach((row, index) => {
											html += '<tr>';
											if (index === 0) {

												if (columnCount <= 4) {
													row.forEach(cell => html += `<th class="bg-light">${cell || ''}</th>`);

													for (let i = row.length; i < 4; i++) {
														html += '<th class="bg-light"></th>';
													}
												} else {
													row.forEach(cell => html += `<th class="bg-light">${cell || ''}</th>`);
												}
											} else {
												if (columnCount <= 4) {
													row.forEach(cell => html += `<td>${cell || ''}</td>`);

													for (let i = row.length; i < 4; i++) {
														html += '<td></td>';
													}
												} else {
													row.forEach(cell => html += `<td>${cell || ''}</td>`);
												}
											}
											html += '</tr>';
										});

										html += '</table></div></div>';
									});
									html += '</div>';
									content.html(html);
								})
								.catch(error => {
									content.html(
										`
														<div class="alert alert-danger">
															Error loading Excel file. <a href="${file.url}" download>Download instead</a>
														</div>
														`
									);
								});
							break;
						case 'doc':
						case 'docx':
							content.html(`<div class="loader">Loading Word document...</div>`);
							content.removeClass('bg-black').addClass('bg-white');

							const extension = file.url.split('.').pop().toLowerCase();

							if (extension === 'docx') {

								fetch(file.url)
									.then(response => response.arrayBuffer())
									.then(arrayBuffer => {
										if (arrayBuffer.byteLength === 0) {
											throw new Error('File is empty');
										}
										return mammoth.convertToHtml({
											arrayBuffer
										});
									})
									.then(result => {
										if (result.value.trim() === '') {
											throw new Error('No content could be extracted');
										}
										content.html(
											`
															<div class="word-preview p-4 bg-white" style="max-height: 600px; overflow-y: auto;">
																${result.value}
															</div>
															`
										);
									})
									.catch(error => {
										showPreviewError();
									});
							} else {
								var defaultClient = CloudmersiveConvertApiClient.ApiClient.instance;
								var Apikey = defaultClient.authentications['Apikey'];
								Apikey.apiKey = 'YOUR-API-KEY';
								var apiInstance = new CloudmersiveConvertApiClient.ConvertDocumentApi();
								fetch(file.url)
									.then(response => response.blob())
									.then(blob => {
										return new Promise((resolve, reject) => {
											apiInstance.convertDocumentDocToHtml(blob, (error,
												data) => {
												if (error) {
													reject(error);
												} else {
													resolve(data);
												}
											});
										});
									})
									.then(htmlContent => {
										content.html(
											`
															<div class="word-preview p-4 bg-white" style="max-height: 600px; overflow-y: auto;">
																${htmlContent}
															</div>
															`
										);
									})
									.catch(error => {
										showPreviewError();
									});
							}
							break;
						default:
							content.html(
								`
											<div class="text-center py-5">
												<i class="fas fa-file-alt fa-4x mb-3"></i>
												<h5>Preview not available</h5>
												<p class="mb-4">This file type cannot be previewed directly.</p>
												<a href="${file.url}" class="btn btn-primary" target="_blank" download>
													<i class="fas fa-download me-2"></i>Download File
												</a>
											</div>
											`
							);
					}
				}
				loadMediaEngagement(file.id);
				modal.modal('show');
			});

			$('.btn-filter').click(function () {
				$('.btn-filter').removeClass('active');
				$(this).addClass('active');
				const currentTag = $('#tagFilter').val().trim();
				const selectedGroup = $('#groupFilter').val();
				const mediaType = $(this).data('filter');
				if (mediaType === 'all') {
					$('#groupFilter').val('').trigger('change');
					loadMedia('all', '', currentTag);
				} else {
					loadMedia(mediaType, selectedGroup, currentTag);
				}
			});
			loadMedia();
			loadGroups();

			function showPreviewError() {
				content.html(
					`
									<div class="text-center py-5 bg-white">
										<i class="fas fa-file-word fa-4x mb-3 text-primary"></i>
										<h5>Preview Error</h5>
										<p class="text-muted mb-4">There was an error previewing this document.</p>
										<div class="d-flex justify-content-center gap-2">
											<a href="${file.url}" class="btn btn-primary" download>
												<i class="fas fa-download me-2"></i>Download Document
											</a>
											<button class="btn btn-outline-secondary convert-to-pdf">
												<i class="fas fa-file-pdf me-2"></i>Convert to PDF
											</button>
										</div>
									</div>
									`
				);
			}
			$(document).on('click', '.edit-media', function (e) {
				e.stopPropagation();
				const mediaId = $(this).data('id');
				showLoading();
				$.ajax({
					url: `/admin/media/${mediaId}`,
					type: 'GET',
					success: function (response) {
						const media = response.data;
						editDropzone.removeAllFiles(true);
						$('#edit_industry_interests').empty();
						$('#edit_dealer_id').empty();
						$('#edit_manufacturer_id').empty();
						$('#edit_media_id').val(media.id);
						$('#edit_title').val(media.title || '');
						$('#edit_description').val(media.description || '');
						$('#edit_is_featured').prop('checked', !!media.is_featured);
						if (media.group && media.group.name) {
							$('#edit_group').val(media.group.name);
						} else {
							$('#edit_group').val('');
						}
						if (media.tags && Array.isArray(media.tags)) {
							$('#edit_tags').val(media.tags.map(tag =>
								typeof tag === 'string' ? tag : tag.name
							).join(', '));
						}
						$('#edit_industry_interests').select2({
							tags: false,
							placeholder: "Search and select industries",
							multiple: true,
							allowClear: true,
							dropdownParent: $('#editModal'),
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
										results: data.map(item => ({
											id: item.id,
											text: item.name
										}))
									};
								},
								cache: true
							}
						});

						$('#edit_dealer_id').select2({
							tags: false,
							placeholder: "Search and select dealers",
							multiple: true,
							allowClear: false,
							dropdownParent: $('#editModal'),
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
										results: data.map(item => ({
											id: item.id,
											text: item.name
										}))
									};
								},
								cache: true
							}
						});

						$('#edit_manufacturer_id').select2({
							tags: false,
							placeholder: "Search and select manufacturers",
							multiple: true,
							allowClear: false,
							dropdownParent: $('#editModal'),
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
									return {
										results: data.map(item => ({
											id: item.id,
											text: item.name
										}))
									};
								},
								cache: true
							}
						});

						if (media.industries && media.industries.length > 0) {
							media.industries.forEach(industry => {
								const newOption = new Option(industry.name, industry.id,
									true, true);
								$('#edit_industry_interests').append(newOption);
							});
							$('#edit_industry_interests').trigger('change');
						}

						if (media.dealers && media.dealers.length > 0) {
							media.dealers.forEach(dealer => {
								const newOption = new Option(dealer.name, dealer.id,
									true, true);
								$('#edit_dealer_id').append(newOption);
							});
							$('#edit_dealer_id').trigger('change');
						}

						if (media.manufacturers && media.manufacturers.length > 0) {
							media.manufacturers.forEach(manufacturer => {
								const newOption = new Option(manufacturer.name, manufacturer.id,
									true, true);
								$('#edit_manufacturer_id').append(newOption);
							});
							$('#edit_manufacturer_id').trigger('change');
						}

						if (media.file_path) {
							const mockFile = {
								name: media.title || 'Untitled',
								size: media.size || 0,
								type: media.mime_type,
								id: media.id,
								status: "success",
								accepted: true,
								url: media.url || (media.file_path ? asset('storage/' +
									media.file_path) : null)
							};

							if (!editDropzone.files.some(f => f.id === mockFile.id)) {
								editDropzone.emit("addedfile", mockFile);
								if (media.file_type === 'image') {
									editDropzone.emit("thumbnail", mockFile, mockFile.url);
								} else if (media.file_type === 'video') {
									editDropzone.emit("thumbnail", mockFile,
										'/path/to/video-icon.png');
								} else {
									editDropzone.emit("thumbnail", mockFile,
										'/path/to/document-icon.png');
								}
								editDropzone.emit("complete", mockFile);
								editDropzone.files.push(mockFile);
								$(mockFile.previewElement).find('.dz-remove').remove();
							}
						}

						hideLoading();
						$('#editModal').modal('show');
					},
					error: function (xhr) {
						hideLoading();
						console.error('Error loading media:', xhr.responseJSON);
						toastr.error('Error loading media details: ' + (xhr.responseJSON
							?.message || 'Unknown error'));
					}
				});
			});
			$('#editModal').on('show.bs.modal', function () {
				if ($('#edit_industry_interests').data('select2')) {
					$('#edit_industry_interests').select2('destroy');
				}
				if ($('#edit_dealer_id').data('select2')) {
					$('#edit_dealer_id').select2('destroy');
				}
				if ($('#edit_manufacturer_id').data('select2')) {
					$('#edit_manufacturer_id').select2('destroy');
				}
				$('#edit_industry_interests, #edit_dealer_id, #edit_manufacturer_id').select2({
					tags: false,
					placeholder: "Search and select",
					multiple: true,
					allowClear: false,
					dropdownParent: $('#editModal'),
					ajax: {
						url: function () {
							if (this[0].id === 'edit_industry_interests') {
								return '/api/industries/search';
							} else if (this[0].id === 'edit_dealer_id') {
								return '/api/dealers/search';
							} else if (this[0].id === 'edit_manufacturer_id') {
								return '/api/manufacturers/search';
							}
						},
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
								results: data.map(item => ({
									id: item.id,
									text: item.name
								}))
							};
						},
						cache: true
					}
				}).on('select2:clear', function (e) {
					$(this).val(null).trigger('change');
				});
			});

			$('.bulk-add').click(function () {
				myDropzone.removeAllFiles();
				$('#uploadModal').modal('show');
				$.ajax({
					url: '/admin/media/bulk-info',
					type: 'GET',
					data: {
						ids: selectedMediaIds
					},
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response && response.common) {
							$('#upload_title').val(response.common.title || '');
							$('#upload_description').val(response.common.description || '');
							$('#upload_group').val(response.common.group_name || '');
							if (response.common.tags && Array.isArray(response.common.tags)) {
								$('#upload_tags').val(response.common.tags.join(', '));
							}
							if (response.common.industry_ids && Array.isArray(response.common
								.industry_ids)) {
								$('#upload_industry_interests').val(response.common
									.industry_ids).trigger('change');
							} else {
								$('#upload_industry_interests').val(null).trigger('change');
							}
							if (response.common.dealer_ids && Array.isArray(response.common
								.dealer_ids)) {
								$('#upload_dealer_id').val(response.common.dealer_ids).trigger(
									'change');
							} else {
								$('#upload_dealer_id').val(null).trigger('change');
							}
							if (response.common.manufacturer_ids && Array.isArray(response.common
								.manufacturer_ids)) {
								$('#upload_manufacturer_id').val(response.common.manufacturer_ids).trigger(
									'change');
							} else {
								$('#upload_manufacturer_id').val(null).trigger('change');
							}
							$('#is_featured').prop('checked', response.common.is_featured ||
								false);
						}
					},
					error: function (xhr, status, error) {
						console.error('Error fetching bulk info:', error);
						toastr.error('Error loading media information');
					}
				});
			});
			$('#editModal').on('hidden.bs.modal', function () {
				$('#editForm')[0].reset();
				$('#edit_industry_interests').empty().val(null).trigger('change');
				$('#edit_dealer_id').empty().val(null).trigger('change');
				$('#edit_manufacturer_id').empty().val(null).trigger('change');
				editDropzone.removeAllFiles(true);
			});

			$(document).on('click', '.delete-media', function (e) {
				e.stopPropagation();
				const mediaId = $(this).data('id');
				if (confirm('Are you sure you want to delete this media?')) {
					$.ajax({
						url: `/admin/media/${mediaId}`,
						type: 'DELETE',
						data: {
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: function () {
							toastr.success('Media deleted successfully');
							loadMedia($('.btn-filter.active').data('filter'));
						},
						error: function () {
							toastr.error('Error deleting media');
						}
					});
				}
			});

			$('#groupFilter').change(function () {
				const selected = $(this).val();
				$('#defaultButtons').toggle(!selected);
				$('#groupButtons').toggle(!!selected);
				const currentTag = $('#tagFilter').val().trim();
				const currentType = $('.btn-filter.active').data('filter') || 'all';
				loadMedia(currentType, selected, currentTag);
			});

			$('#addToGroup').click(function () {
				const selectedGroup = $('#groupFilter').val();
				if (!selectedGroup) return;
				$('#uploadModal').modal('show');
				$('#upload_group').val(selectedGroup).trigger('change');
			});

			$('#removeFromGroup').click(function () {
				const selectedGroup = $('#groupFilter').val();
				const selectedFiles = $('.media-checkbox:checked').map(function () {
					return $(this).data('id');
				}).get();
				if (!selectedFiles.length) {
					toastr.warning('Please select files to remove from group');
					return;
				}
				if (confirm('Are you sure you want to remove selected files from this group?')) {
					$.ajax({
						url: '/admin/media/remove-from-group',
						type: 'POST',
						data: {
							_token: $('meta[name="csrf-token"]').attr('content'),
							group_name: selectedGroup,
							media_ids: selectedFiles
						},
						success: function () {
							toastr.success('Files removed from group successfully');
							loadMedia();
							loadGroups();
						},
						error: function () {
							toastr.error('Error removing files from group');
						}
					});
				}
			});

			$('#editGroupFiles').click(function () {
				const selectedFiles = $('.media-checkbox:checked').map(function () {
					return $(this).data('id');
				}).get();
				if (!selectedFiles.length) {
					toastr.warning('Please select files to edit');
					return;
				}
				$.ajax({
					url: '/admin/media/bulk-info',
					type: 'POST',
					data: {
						_token: $('meta[name="csrf-token"]').attr('content'),
						ids: selectedFiles
					},
					success: function (response) {
						if (response && response.common) {
							$('#edit_media_id').val(selectedFiles.join(','));
							$('#edit_title').val(response.common.title || '');
							$('#edit_description').val(response.common.description || '');
							$('#edit_group').val(response.common.group_name || '');
							$('#edit_tags').val(response.common.tags ? response.common.tags.join(', ') : '');
							$('#edit_is_featured').prop('checked', response.common.is_featured || false);
							if (response.data) {
								$('#edit_industry_interests').val(response.data.industries.map(
									i => i.id)).trigger('change');
								$('#edit_dealer_id').val(response.data.dealers.map(d => d.id))
									.trigger('change');
								$('#edit_manufacturer_id').val(response.data.manufacturers.map(d => d.id))
									.trigger('change');
							}
							$('#editModal').modal('show');
						}
					},
					error: function () {
						toastr.error('Error loading file information');
					}
				});
			});

			$('[data-bs-target="#uploadModal"]').click(function () {
				myDropzone.removeAllFiles();
				$('#uploadForm')[0].reset();
				$('#upload_industry_interests, #upload_dealer_id, #upload_manufacturer_id').val(null).trigger('change');
			});

			function showLoading() {
				$('.loading-overlay').css({
					'display': 'flex',
					'opacity': '1'
				});
			}

			function hideLoading() {
				$('.loading-overlay').fadeOut(300);
			}

			function loadMedia(type = 'all', group = '', tag = '') {
				showLoading();
				$.ajax({
					url: '/admin/media/files',
					type: 'GET',
					data: {
						type,
						group,
						tag
					},
					success: function (response) {
						const grid = $('#mediaGrid');
						grid.empty();

						// Clear both classes then add the current view
						grid.removeClass('grid-view list-view').addClass(`${currentView}-view`);

						if (!response.data || response.data.length === 0) {
							grid.html(
								`
								<div class="col-12 text-center py-5">
									<i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
									<h4 class="text-muted">No media files found</h4>
									<p class="text-muted">No files match the selected filters</p>
								</div>
								`
							);
						} else {
							response.data.forEach(file => {
								grid.append(createMediaCard(file, currentView));
							});
						}
						hideLoading();
					},
					error: function (xhr) {
						hideLoading();
						toastr.error('Error loading media files');
					}
				});
			}

			function updateBulkActionButtons() {
				const hasSelection = selectedMediaIds.length > 0;
				const selectedGroup = $('#groupFilter').val();
				$('.bulk-edit, .bulk-delete').prop('disabled', !hasSelection || !selectedGroup);
			}

			function initializeEngagement(mediaId) {
				$.post(`/admin/media/${mediaId}/view`);
				$('.like-btn').off('click').on('click', function () {
					const btn = $(this);
					$.post(`/admin/media/${mediaId}/toggle-like`, function (response) {
						const icon = btn.find('i');
						icon.toggleClass('far fas');
						btn.find('.likes-count').text(response.likes_count);
					});
				});

				// $('.comment-form').off('submit').on('submit', function (e) {
				//     e.preventDefault();
				//     const form = $(this);
				//     const input = form.find('input');
				//     $.post(`/admin/media/${mediaId}/comment`, {
				//         comment: input.val(),
				//         _token: $('meta[name="csrf-token"]').attr('content')
				//     }, function (response) {
				//         input.val('');
				//         loadComments(mediaId);
				//     });
				// });
			}

			function loadComments(mediaId) {
				$.get(`/admin/media/${mediaId}`, function (response) {
					const commentList = $('.comment-list');
					commentList.empty();
					response.data.comments.forEach(comment => {
						commentList.append(
							`
											<div class="comment mb-2">
												<strong>${comment.user.name}</strong>
												<span class="text-muted ms-2">${moment(comment.created_at).fromNow()}</span>
												<p class="mb-1">${comment.comment}</p>
											</div>
											`
						);
					});
					$('.comments-count').text(response.data.comments.length);
					$('.likes-count').text(response.data.likes_count);
					$('.views-count').text(response.data.views_count);
				});
			}

			function loadMediaEngagement(mediaId) {
				$.ajax({
					url: `/admin/media/${mediaId}/engagement`,
					type: 'GET',
					beforeSend: function () {
						$('.likes-count, .views-count').text('...');
						$('.comment-list').html(
							`
											<div class="text-center py-4">
												<div class="spinner-border text-primary" role="status">
													<span class="visually-hidden">Loading comments...</span>
												</div>
											</div>
											`
						);
					},
					success: function (response) {
						$('.likes-count').text(response.likes_count || 0);
						$('.views-count').text(response.views_count || 0);
						const likeBtn = $('.btn-like');
						if (response.is_liked) {
							likeBtn.find('i').removeClass('far').addClass('fas text-danger');
						} else {
							likeBtn.find('i').removeClass('fas text-danger').addClass('far');
						}
						const commentList = $('.comment-list');
						commentList.empty();
						if (!response.comments || response.comments.length === 0) {
							commentList.html(
								`
												<div class="text-center text-muted py-4">
													<i class="far fa-comments fa-2x mb-2"></i>
													<p class="mb-0">No comments yet</p>
													<small>Be the first to comment</small>
												</div>
												`
							);
						} else {
							response.comments.forEach(comment => {
								commentList.append(
									`
												<div class="comment rounded p-3 mb-2">
													<div class="d-flex">
														<img src="/assets/img/avatar.png" class="rounded-circle me-2"
															width="32" height="32" alt="${comment.user.name}">
														<div class="flex-grow-1">
															<div class="bg-light rounded p-2">
																<div class="d-flex justify-content-between align-items-center">
																	<strong class="text-primary">${comment.user.name}</strong>
																	<div>
																		<small class="text-muted">${formatTimestamp(comment.created_at)}</small>
																		<button class="btn btn-sm text-danger delete-comment ms-2"
																			data-comment-id="${comment.id}">
																			<i class="fas fa-trash"></i>
																		</button>
																	</div>
																</div>
																<div class="mt-1">${comment.comment}</div>
															</div>
														</div>
													</div>
												</div>
												`
								);
							});
						}

						$('.comments-count').text(response.comments?.length || 0);
						if (response.likes && response.likes.length > 0) {
							const likesList = response.likes.map(like => like.user_name).join('\n');
							$('.likes-count')
								.attr('title', `Liked by:\n${likesList}`)
								.tooltip('dispose')
								.tooltip();
						}
					},
					error: function (xhr, status, error) {
						console.error('Error loading engagement data:', error);
						$('.comment-list').html(
							`
											<div class="text-center text-muted py-4">
												<i class="fas fa-exclamation-circle fa-2x mb-2 text-danger"></i>
												<p class="mb-0">Failed to load engagement data</p>
												<small>Please try again later</small>
											</div>
											`
						);
						$('.likes-count, .views-count, .comments-count').text('0');
						toastr.error('Failed to load engagement data');
					}
				});
			}

			function formatTimestamp(dateString) {
				const date = new Date(dateString);
				const now = new Date();
				const diffTime = Math.abs(now - date);
				const diffMinutes = Math.floor(diffTime / (1000 * 60));
				const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
				const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
				if (diffMinutes < 1) return 'Just now';
				if (diffMinutes < 60) return `${diffMinutes}m ago`;
				if (diffHours < 24) return `${diffHours}h ago`;
				if (diffDays < 7) return `${diffDays}d ago`;
				return date.toLocaleDateString();
			}

			$(document).on('click', '.delete-comment', function (e) {
				e.preventDefault();
				e.stopPropagation();
				const commentId = $(this).data('comment-id');
				const mediaId = $('#previewModal').data('media-id');
				if (confirm('Are you sure you want to delete this comment?')) {
					$.ajax({
						url: `/admin/media/comments/${commentId}`,
						type: 'DELETE',
						headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
						},
						success: function () {
							toastr.success('Comment deleted successfully');
							loadMediaEngagement(mediaId);
						},
						error: function () {
							toastr.error('Error deleting comment');
						}
					});
				}
			});

			function updateAssignedUsers(industryIds, dealerIds, manufacturerIds, targetElement) {
				const container = $(targetElement);
				const modalElement = container.closest('.modal');
				const countBadge = modalElement.find('.assigned-users-count');
				container.html(`
									<div class="text-center py-3">
										<div class="spinner-border spinner-border-sm text-primary" role="status"></div>
										<div class="small mt-2">Loading assigned users...</div>
									</div>
								`);

				if (!industryIds?.length && !dealerIds?.length && !manufacturerIds?.length) {
					container.html(`
									<div class="text-center py-3">
										<i class="fas fa-users-slash fa-2x text-muted mb-2"></i>
										<div class="text-muted small">Select at least one industry or dealer or manufacturer</div>
									</div>
									`);
					countBadge.text('0');
					return;
				}

				$.ajax({
					url: '/admin/media/assigned-users',
					method: 'POST',
					data: {
						industry_ids: industryIds,
						dealer_ids: dealerIds,
						manufacturer_ids: manufacturerIds,
						_token: $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						container.empty();
						countBadge.text(response.users.length);
						if (!response.users.length) {
							container.html(`
												<div class="text-center py-3">
													<i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
													<div class="text-muted small">No users match the selected criteria</div>
												</div>
											`);
							return;
						}
						const usersByCompany = response.users.reduce((acc, user) => {
							const company = user.company || 'Other';
							if (!acc[company]) acc[company] = [];
							acc[company].push(user);
							return acc;
						}, {});
						Object.entries(usersByCompany).forEach(([company, users]) => {
							const companySection = $(`
												<div class="company-section mb-3">

													<div class="users-list ps-3"></div>
												</div>
											`);
							users.forEach(user => {
								companySection.find('.users-list').append(`
													<div class="user-item mb-1 small">
														<i class="fas fa-user text-muted me-1"></i>
														${user.name}
														<span class="text-muted">(${user.email})</span>
													</div>
												`);
							});
							container.append(companySection);
						});
					},
					error: function (xhr) {
						container.html(`
											<div class="text-center py-3">
												<i class="fas fa-exclamation-triangle text-danger fa-2x mb-2"></i>
												<div class="text-danger small">Error loading assigned users</div>
											</div>
										`);
						countBadge.text('0');
						console.error('Error loading assigned users:', xhr.responseText);
					}
				});
			}

			function setupAssignedUsersHandlers(modalId) {
				const modal = $(`#${modalId}`);
				const industrySelect = modal.find('[id$="_industry_interests"]');
				const dealerSelect = modal.find('[id$="_dealer_id"]');
				const manufacturerselect = modal.find('[id$="_manufacturer_id"]');
				const usersList = modal.find('.assigned-users-list');
				function updateUsers() {
					updateAssignedUsers(
						industrySelect.val(),
						dealerSelect.val(),
						manufacturerselect.val(),
						usersList
					);
				}
				industrySelect.on('change', updateUsers);
				dealerSelect.on('change', updateUsers);
				manufacturerselect.on('change', updateUsers);
				modal.on('shown.bs.modal', updateUsers);
			}

			$(document).ready(function () {
				setupAssignedUsersHandlers('uploadModal');
				setupAssignedUsersHandlers('editModal');
			});
		});
	</script>
@endpush