@extends('layouts.app')
@section('title', 'Marketing Materials Management')
@section('content')
@include('admin.partials.nav')
<div id="layoutSidenav">
	@include('admin.partials.sidenav')
	<div id="layoutSidenav_content">
		<main>
			<div class="container-fluid px-4">
				<div class="d-flex justify-content-between align-items-center mt-5">
					<h1>Marketing Materials</h1>
					<div class="d-flex gap-2">
						<div id="defaultButtons">
							<button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#uploadModal">
								<i class="fas fa-upload me-2"></i>Add Materials
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
				<div class="marketing-materials-grid-container">
					<div class="loading-overlay" style="display: none;">
						<div class="text-center">
							<div class="spinner-border text-primary" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							<div class="mt-2">Loading marketing materials...</div>
						</div>
					</div>
					<div class="row" id="materialsGrid"></div>
				</div>
			</div>
		</main>
	</div>
</div>

<!-- Upload Modal -->
@include('admin.marketing-materials.partials.upload-modal')

<!-- Edit Modal -->
@include('admin.marketing-materials.partials.edit-modal')

<!-- Preview Modal -->
@include('admin.marketing-materials.partials.preview-modal')

@endsection
@push('styles')
<style>
.grid-view .col-md-3 {
	margin-bottom: 1.5rem;
}
.list-view .card {
	margin-bottom: 0.5rem;
}
.preview-container {
	cursor: pointer;
	position: relative;
}
.preview-container:hover .overlay {
	opacity: 1 !important;
}
.document-thumbnail {
	background-color: #f8f9fa;
	border-radius: 4px;
}
.loading-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(255, 255, 255, 0.8);
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 1000;
}
.card {
	transition: transform 0.2s;
}
.card:hover {
	transform: translateY(-2px);
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.btn-filter {
	background-color: #f8f9fa;
	border: 1px solid #dee2e6;
}
.btn-filter.active {
	background-color: #0d6efd;
	color: white;
	border-color: #0d6efd;
}
.assigned-users-list {
	max-height: 200px;
	overflow-y: auto;
}
.engagement-section {
	background-color: #f8f9fa;
}
.comment-list {
	max-height: 300px;
	overflow-y: auto;
}
.dropzone {
	border: 2px dashed #0087F7;
	border-radius: 5px;
	background: white;
	min-height: 150px;
	padding: 20px;
	position: relative;
}
.select2-container {
	width: 100% !important;
}
.material-tags .badge,
.industry-tags .badge,
.dealer-tags .badge,
.manufacturer-tags .badge {
	margin-right: 5px;
	margin-bottom: 5px;
}
.engagement-stats .btn {
	transition: all 0.3s ease;
}
.engagement-stats .btn:hover {
	background-color: #e9ecef;
}
.preview-content-wrapper {
	max-height: 70vh;
	overflow-y: auto;
}
.document-preview {
	background: #f8f9fa;
	padding: 20px;
	border-radius: 4px;
}
</style>
@endpush

@push('scripts')
<script>
Dropzone.autoDiscover = false;
let removedFileIds = [];
let currentView = 'grid';
let isGroupSelected = false;
function showLoading() {
	$('.loading-overlay').css({
		'display': 'flex',
		'opacity': '1'
	});
}
function hideLoading() {
	$('.loading-overlay').fadeOut(300);
}
function loadMaterials(type = 'all') {
	showLoading();
	const group = $('#groupFilter').val();
	const tag = $('#tagFilter').val().trim();
	$.ajax({
		url: '/admin/marketing-materials/files',
		type: 'GET',
		data: { type, group, tag },
		success: function (response) {
			const grid = $('#materialsGrid');
			grid.empty();
			if (!response.data || response.data.length === 0) {
				grid.html(`
				<div class="col-12 text-center py-5">
					<i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
					<h4 class="text-muted">No materials found</h4>
					<p class="text-muted">No files match the selected filters</p>
				</div>
			`);
			} else {
				response.data.forEach(file => {
					grid.append(createMaterialCard(file));
				});
			}
			hideLoading();
		},
		error: function (xhr) {
			hideLoading();
			toastr.error('Error loading materials');
		}
	});
}

function loadGroups() {
	$.ajax({
		url: '/admin/marketing-materials/groups',
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
				$('#groupFilter, #upload_group').html(groupOptions.join(''));
			}
		},
		error: function (xhr) {
			toastr.error('Failed to load groups');
		}
	});
}

function createMaterialCard(file, viewType = 'grid') {
	let fileIcon, fileTypeClass;
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


//new code
function populateEditModal(group) {
    console.log("Populating edit modal with data:", group);
    

    $("#editForm")[0].reset();
    

    $("#edit_material_id").val(group.id || '');
    $("#edit_title").val(group.title || '');
    $("#edit_description").val(group.description || '');
    $("#edit_group_name").val(group.name || '');
    $("#edit_tags").val(Array.isArray(group.tags) ? group.tags.join(", ") : '');
    
    console.log("Set group name to:", group.name);
    

    $("#edit_industry_interests, #edit_dealer_id, #edit_manufacturer_id").val(null).trigger('change');
    

    if (Array.isArray(group.industries)) {
        let $industries = $("#edit_industry_interests");
        $industries.empty();
        group.industries.forEach(industry => {
            let option = new Option(industry.name, industry.id, true, true);
            $industries.append(option);
        });
    }
    

    if (Array.isArray(group.dealers)) {
        let $dealers = $("#edit_dealer_id");
        $dealers.empty();
        group.dealers.forEach(dealer => {
            let option = new Option(dealer.name, dealer.id, true, true);
            $dealers.append(option);
        });
    }
    

    if (Array.isArray(group.manufacturers)) {
        let $manufacturers = $("#edit_manufacturer_id");
        $manufacturers.empty();
        group.manufacturers.forEach(manufacturer => {
            let option = new Option(manufacturer.name, manufacturer.id, true, true);
            $manufacturers.append(option);
        });
    }
    

    $("#edit_is_featured").prop("checked", !!group.is_featured);
    
    reinitializeSelect2();
    
   
    if (editDropzone && Array.isArray(group.files)) {
        console.log("Populating dropzone with files:", group.files);
        populateDropzone(group.files);
    }
}

function populateDropzone(files) {
	if (!editDropzone) {
		console.error("editDropzone is not initialized! Files cannot be added.");
		return;
	}
	console.log("Populating Dropzone with files...");
	editDropzone.removeAllFiles(true);
	files.forEach(file => {
		let mockFile = {
			name: file.name || file.title || "Unknown File",
			size: file.size || 0,
			type: file.mime_type,
			id: file.id,
			url: file.url
		};
		editDropzone.emit("addedfile", mockFile);
		if (file.file_type === "image") {
			editDropzone.emit("thumbnail", mockFile, file.url);
		} else {
			editDropzone.emit("thumbnail", mockFile, "/path/to/document-icon.png");
		}
		editDropzone.emit("complete", mockFile);
		editDropzone.files.push(mockFile);
	});
	console.log("Files added to Dropzone.");
}

function reinitializeSelect2() {
	$("#edit_industry_interests, #edit_dealer_id, #edit_manufacturer_id").each(function () {
		$(this).select2('destroy').select2({
			placeholder: "Search and select",
			multiple: true,
			allowClear: true,
			dropdownParent: $("#editModal"),
			width: '100%'
		});
	});
}

let editDropzone = new Dropzone("#editMaterialDropzone", {
	url: "/admin/marketing-materials/bulk-update",
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
		let dropzone = this;
		this.on("removedfile", function (file) {
			if (file.id) {
				removedFileIds.push(file.id);
				console.log("File removed:", file);
			}
		});
	}
});
console.log("editDropzone initialized");
//new code

$(document).ready(function () {
	$('#materialsGrid').addClass('grid-view');
	let uploadDropzone = new Dropzone("#materialDropzone", {
		url: "/admin/marketing-materials",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		paramName: "files",
		maxFilesize: 500,
		uploadMultiple: true,
		parallelUploads: 10,
		acceptedFiles: "image/*,video/mp4,application/pdf,.docx,.csv,.xlsx,.xls",
		autoProcessQueue: false,
		addRemoveLinks: true,
		init: function () {
			let dropzone = this;
			$("#uploadForm").on("submit", function (e) {
				e.preventDefault();
				e.stopPropagation();
				if (dropzone.files.length === 0) {
					toastr.error('Please upload at least one file');
					return false;
				}
				let formData = new FormData(this);
				dropzone.files.forEach(function (file) {
					file.formData = formData;
				});
				dropzone.processQueue();
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
				if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
					toastr.success('Files uploaded successfully');
					$('#uploadModal').modal('hide');
					loadMaterials();
					loadGroups();
					this.removeAllFiles();
				}
			});

			this.on("error", function (file, errorMessage) {
				console.error("Upload error:", errorMessage);
				toastr.error(errorMessage);
			});
		}
	});

	// let editDropzone = new Dropzone("#editMaterialDropzone", {
	// 	url: "/admin/marketing-materials/bulk-update",
	// 	headers: {
	// 		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 	},
	// 	paramName: "files",
	// 	maxFilesize: 500,
	// 	uploadMultiple: true,
	// 	parallelUploads: 10,
	// 	acceptedFiles: "image/*,video/mp4,application/pdf,.docx,.csv,.xlsx,.xls",
	// 	autoProcessQueue: false,
	// 	addRemoveLinks: true,
	// 	init: function () {
	// 		let dropzone = this;
	// 		$("#editForm").on("submit", function (e) {
	// 			e.preventDefault();
	// 			showLoading();
	// 			let formData = new FormData(this);
	// 			const remainingFileIds = dropzone.files
	// 				.filter(file => file.id)
	// 				.map(file => file.id);
	// 			remainingFileIds.forEach(id => {
	// 				formData.append('material_ids[]', id);
	// 			});
	// 			$.ajax({
	// 				url: '/admin/marketing-materials/bulk-update',
	// 				type: 'POST',
	// 				data: formData,
	// 				processData: false,
	// 				contentType: false,
	// 				success: function (response) {
	// 					hideLoading();
	// 					toastr.success('Files updated successfully');
	// 					$('#editModal').modal('hide');
	// 					loadMaterials();
	// 					loadGroups();
	// 				},
	// 				error: function (xhr) {
	// 					hideLoading();
	// 					toastr.error('Error updating files: ' + (xhr.responseJSON?.message || 'Unknown error'));
	// 				}
	// 			});
	// 		});
	// 	}
	// });

	$('.view-toggle').click(function () {
		const viewType = $(this).data('view');
		if (currentView !== viewType) {
			currentView = viewType;
			$('.view-toggle').removeClass('active');
			$(this).addClass('active');
			$('#materialsGrid').removeClass('grid-view list-view').addClass(`${viewType}-view`);
			loadMaterials();
		}
	});

	$('.btn-filter').click(function () {
		$('.btn-filter').removeClass('active');
		$(this).addClass('active');
		loadMaterials($(this).data('filter'));
	});

	$('#groupFilter').change(function () {
		const selected = $(this).val();
		isGroupSelected = !!selected;
		$('#defaultButtons').toggle(!selected);
		$('#groupButtons').toggle(!!selected);
		loadMaterials();
	});

	let tagFilterTimeout;
	$('#tagFilter').on('input', function () {
		clearTimeout(tagFilterTimeout);
		tagFilterTimeout = setTimeout(() => {
			loadMaterials();
		}, 500);
	});

	loadMaterials();
	loadGroups();

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

	// Add this in your document.ready function
	$(document).on('click', '.preview-container', function (e) {
		const file = $(this).data('file');
		const modal = $('#previewModal');
		modal.find('.modal-title').text(file.title);
		const content = modal.find('#previewContent');
		content.empty();
		modal.data('material-id', file.id);
		// Set download button
		const downloadBtn = modal.find('.download-btn');
		downloadBtn.attr('href', file.url);
		const filename = file.title + '.' + file.url.split('.').pop();
		downloadBtn.attr('download', filename);
		// Handle file preview based on type
		if (file.file_type === 'image') {
			content.html(`
		<div class="image-preview-container d-flex align-items-center justify-content-center w-100 h-100">
			<img src="${file.url}" class="img-fluid" style="max-height: calc(100vh - 56px); object-fit: contain; max-width: 100%;">
		</div>
	`);
		} else if (file.file_type === 'video') {
			content.html(`
		<video controls class="w-100">
			<source src="${file.url}" type="${file.mime_type}">
			Your browser does not support the video tag.
		</video>
	`);
		} else {
			content.html(`
		<div class="text-center py-5">
			<i class="fas fa-file-alt fa-4x mb-3"></i>
			<h5>Preview not available</h5>
			<p class="mb-4">This file type cannot be previewed directly.</p>
			<a href="${file.url}" class="btn btn-primary" target="_blank" download>
				<i class="fas fa-download me-2"></i>Download File
			</a>
		</div>
	`);
		}
		// Populate tags and metadata
		modal.find('.material-title').text(file.title);
		modal.find('.material-date').text(moment(file.created_at).format('MMMM D, YYYY'));
		// Populate industries
		const industriesHtml = file.industries.map(industry =>
			`<span class="badge bg-primary me-1">${industry.name}</span>`
		).join('');
		modal.find('.industry-tags').html(industriesHtml || '<span class="text-muted">No industries assigned</span>');
		// Populate dealers
		const dealersHtml = file.dealers.map(dealer =>
			`<span class="badge bg-warning me-1">${dealer.name}</span>`
		).join('');
		modal.find('.dealer-tags').html(dealersHtml || '<span class="text-muted">No dealers assigned</span>');
		const manufacturersHtml = file.manufacturers.map(manufacturer =>
			`<span class="badge bg-warning me-1">${manufacturer.name}</span>`
		).join('');
		modal.find('.manufacturer-tags').html(manufacturersHtml || '<span class="text-muted">No manufacturers assigned</span>');
		// Populate tags
		const tagsHtml = file.tags.map(tag =>
			`<span class="badge bg-secondary me-1">${tag}</span>`
		).join('');
		modal.find('.material-tags').html(tagsHtml || '<span class="text-muted">No tags assigned</span>');
		// Load engagement data
		loadMaterialEngagement(file.id);
		modal.modal('show');
	});

	// Add engagement loading function
	function loadMaterialEngagement(materialId) {
		$.ajax({
			url: `/admin/marketing-materials/${materialId}/engagement`,
			type: 'GET',
			success: function (response) {
				$('.likes-count').text(response.likes_count || 0);
				$('.views-count').text(response.views_count || 0);

				const likeBtn = $('.btn-like');
				if (response.is_liked) {
					likeBtn.find('i').removeClass('far').addClass('fas text-danger');
				} else {
					likeBtn.find('i').removeClass('fas text-danger').addClass('far');
				}
			},
			error: function (xhr) {
				console.error('Error loading engagement data:', xhr);
				toastr.error('Failed to load engagement data');
			}
		});
		// Record view
		$.post(`/admin/marketing-materials/${materialId}/view`);
	}

	// Add like functionality
	$(document).on('click', '.btn-like', function () {
		const materialId = $('#previewModal').data('material-id');
		$.post(`/admin/marketing-materials/${materialId}/toggle-like`, function (response) {
			$('.likes-count').text(response.likes_count);
			const icon = $('.btn-like i');
			icon.toggleClass('far fas');
			icon.toggleClass('text-danger', icon.hasClass('fas'));
		});
	});
	// Add edit handler
	$(document).on('click', '.edit-material', function () {
		const materialId = $('#previewModal').data('material-id');
		$('#previewModal').modal('hide');
		$.ajax({
			url: `/admin/marketing-materials/${materialId}`,
			type: 'GET',
			success: function (response) {
				const material = response.data;
				populateEditModal(material);
				$('#editModal').modal('show');
			},
			error: function (xhr) {
				toastr.error('Error loading material details');
			}
		});
	});

	// Add delete handler
	$(document).on('click', '.delete-material', function () {
		const materialId = $('#previewModal').data('material-id');
		if (confirm('Are you sure you want to delete this material?')) {
			$.ajax({
				url: `/admin/marketing-materials/${materialId}`,
				type: 'DELETE',
				data: {
					_token: $('meta[name="csrf-token"]').attr('content')
				},
				success: function () {
					$('#previewModal').modal('hide');
					toastr.success('Material deleted successfully');
					loadMaterials();
					loadGroups();
				},
				error: function () {
					toastr.error('Error deleting material');
				}
			});
		}
	});

	// Helper function to populate edit modal
	// function populateEditModal(material) {
	// 	$('#edit_material_id').val(material.id);
	// 	$('#edit_title').val(material.title);
	// 	$('#edit_description').val(material.description);
	// 	$('#edit_group').val(material.group?.name || '');
	// 	$('#edit_tags').val(material.tags.join(', '));
	// 	$('#edit_is_featured').prop('checked', material.is_featured);
	// 	// Clear and populate industries
	// 	$('#edit_industry_interests').empty();

	// 	material.industries.forEach(industry => {
	// 		const option = new Option(industry.name, industry.id, true, true);
	// 		$('#edit_industry_interests').append(option);
	// 	});
	// 	$('#edit_industry_interests').trigger('change');
	// 	// Clear and populate dealers
	// 	$('#edit_dealer_id').empty();
	// 	material.dealers.forEach(dealer => {
	// 		const option = new Option(dealer.name, dealer.id, true, true);
	// 		$('#edit_dealer_id').append(option);
	// 	});
	// 	$('#edit_dealer_id').trigger('change');
	// 	$('#edit_manufacturer_id').empty();
	// 	material.manufacturers.forEach(manufacturer => {
	// 		const option = new Option(manufacturer.name, manufacturer.id, true, true);
	// 		$('#edit_manufacturer_id').append(option);
	// 	});
	// 	$('#edit_manufacturer_id').trigger('change');
	// }
});

//new code
$(document).ready(function () {
	function initSelect2(selector, url) {
		$(selector).select2({
			placeholder: "Search and select",
			multiple: true,
			allowClear: true,
			dropdownParent: $("#editModal"),
			ajax: {
				url: url,
				dataType: "json",
				delay: 250,
				data: function (params) {
					return { q: params.term };
				},
				processResults: function (data) {
					if (!data || !Array.isArray(data)) {
						console.error("Error: No data received for", selector);
						return { results: [] };
					}
					return { results: data.map(item => ({ id: item.id, text: item.name })) };
				},
				error: function (xhr) {
					console.error(selector, "API Error:", xhr.responseText);
				}
			}
		});
	}
	initSelect2("#edit_industry_interests", "/api/industries/search");
	initSelect2("#edit_dealer_id", "/api/dealers/search");
	initSelect2("#edit_manufacturer_id", "/api/manufacturers/search");
});

$(document).on("click", ".bulk-edit", function () {
	let groupName = $("#groupFilter").val();
	if (!groupName) {
		toastr.error("Please select a group first.");
		return;
	}
	$.ajax({
		url: "/admin/marketing-materials/group-info",
		type: "POST",
		data: { group_name: groupName },
		headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
		success: function (response) {
			if (response.status === "success") {
				populateEditModal(response.data);
				$("#editModal").on("shown.bs.modal", function () {
					if (response.data.files && response.data.files.length > 0) {
						populateDropzone(response.data.files);
					}
				});
				$("#editModal").modal("show");
			} else {
				toastr.error("Failed to fetch group information.");
			}
		},
		error: function (xhr) {
			console.error("Error fetching group details:", xhr.responseText);
			toastr.error("An error occurred while fetching group details.");
		}
	});
});
$('#editModal').on('shown.bs.modal', function () {
	reinitializeSelect2();
});

$("#editForm").on("submit", function (e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    let groupName = $("#edit_group_name").val();
    
    // Add existing file IDs
    let existingFileIds = editDropzone.files
        .filter(file => file.id)
        .map(file => file.id);
    formData.set("existing_file_ids", JSON.stringify(existingFileIds));
    
    // Add removed file IDs if any
    if (removedFileIds.length) {
        removedFileIds.forEach(id => formData.append("removed_file_ids[]", id));
    }

    $.ajax({
        url: `/admin/marketing-materials/groups/${encodeURIComponent(groupName)}`,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "X-HTTP-Method-Override": "PUT"
        },
        success: function(response) {
            if (response.status === "success") {
                toastr.success("Group updated successfully");
                $("#editModal").modal("hide");
                loadGroups();
                loadMaterials();
            }
        },
        error: function(xhr) {
            toastr.error("Error updating group: " + (xhr.responseJSON?.message || "Unknown error"));
        }
    });
});
$(document).on("select2:open", function (e) {
	let selectId = e.target.id;
});
//new code
</script>
@endpush
