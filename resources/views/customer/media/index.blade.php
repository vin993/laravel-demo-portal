@extends('layouts.app')
@section('title', 'Media Files')
@section('content')
@include('customer.partials.nav')
<div id="layoutSidenav">
    @include('customer.partials.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mt-5 mb-4">
                    <h1>Media Files</h1>
                    <button class="btn cust-dashb-btn" data-bs-toggle="modal" data-bs-target="#fileRequestModal">
                        <i class="fas fa-plus me-2"></i>File Request
                    </button>
                </div>
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="btn-group">
                            <button class="btn btn-filter active" data-filter="all">All</button>
                            <button class="btn btn-filter" data-filter="document">Documents</button>
                            <button class="btn btn-filter" data-filter="video">Videos</button>
                            <button class="btn btn-filter" data-filter="image">Images</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="tagFilter" placeholder="Filter by tag...">
                    </div>
                </div>

                <div class="row" id="mediaGrid">
                    @foreach($mediaFiles as $file)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 {{ $file->is_featured ? 'border-primary' : '' }}">
                            @if($file->is_featured)
                            <span class="position-absolute top-0 end-0 p-2">
                                <i class="fas fa-star text-warning"></i>
                            </span>
                            @endif
                            <div class="preview-container position-relative" data-file-type="{{ $file->file_type }}"
                                data-tags="{{ implode(',', $file->tags->pluck('name')->toArray()) }}" data-file='{!! json_encode([
       "id" => $file->id,
       "title" => $file->title, 
       "file_type" => $file->file_type,
       "url" => asset("storage/" . $file->file_path),
       "mime_type" => $file->mime_type,
       "tags" => $file->tags->pluck(' name')->toArray()
                                ]) !!}'>
                                @if($file->file_type === 'image')
                                <div
                                    style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    <img src="{{ asset('storage/' . $file->thumbnail_path) }}" class="card-img-top"
                                        style="object-fit: cover; width: 100%; height: 100%;"
                                        onerror="this.onerror=null; this.src='{{ asset('storage/' . $file->file_path) }}';"
                                        loading="lazy">
                                </div>
                                @elseif($file->file_type === 'video')
                                <div class="video-preview" style="height: 200px; background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-play-circle fa-4x text-primary"></i>
                                    </div>
                                </div>
                                @else
                                <div class="document-thumbnail d-flex align-items-center justify-content-center"
                                    style="height: 200px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                                    <div class="text-center">
                                        @php
                                        $iconClass = 'fa-file-alt';
                                        $iconColor = '#6c757d';
                                        if(str_contains($file->mime_type, 'pdf')) {
                                        $iconClass = 'fa-file-pdf';
                                        $iconColor = '#dc3545';
                                        } elseif(str_contains($file->mime_type, 'word') ||
                                        str_contains($file->mime_type, 'msword')) {
                                        $iconClass = 'fa-file-word';
                                        $iconColor = '#0d6efd';
                                        } elseif(str_contains($file->mime_type, 'excel') ||
                                        str_contains($file->mime_type, 'spreadsheet')) {
                                        $iconClass = 'fa-file-excel';
                                        $iconColor = '#198754';
                                        } elseif(str_contains($file->mime_type, 'powerpoint') ||
                                        str_contains($file->mime_type, 'presentation')) {
                                        $iconClass = 'fa-file-powerpoint';
                                        $iconColor = '#fd7e14';
                                        }
                                        @endphp
                                        <i class="fas {{ $iconClass }} fa-4x mb-2" style="color: {{ $iconColor }};"></i>
                                        <div class="small text-muted mt-2">{{ strtoupper(explode('/',
                                            $file->mime_type)[1] ?? '') }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="overlay position-absolute top-0 start-0 w-100 h-100"
                                    style="background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s; cursor: pointer;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-eye text-white fa-2x"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title text-truncate">{{ $file->title }}</h5>
                                <p class="card-text small text-muted">{{ $file->description }}</p>
                            </div>

                            @if($file->group)
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    <i class="fas fa-folder"></i> {{ $file->group->name }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
        @include('customer.partials.footer')
    </div>
</div>

<!-- previewModal -->
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
                    style="min-height: calc(100vh - 56px);">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Request Modal -->
<div class="modal fade" id="fileRequestModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="fileRequestForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Request a File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">File Name</label>
                        <input type="text" name="file_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Upload Files (optional)</label>
                        <div id="dropzoneFileRequest" class="dropzone">
                            <div class="dz-message" data-dz-message>
                                <span>Drop files here or click to upload</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    Dropzone.autoDiscover = false;
    $(document).ready(function () {
        let uploadedFiles = [];
        let currentMediaId = null;
        let fileRequestDropzone;
        function loadEngagement(mediaId) {
            $.ajax({
                url: `/customer/media/${mediaId}/engagement`,
                type: 'GET',
                success: function (response) {
                    $('.likes-count').text(response.likes_count);
                    $('.views-count').text(response.views_count);

                    const likeBtn = $('.btn-like');
                    if (response.is_liked) {
                        likeBtn.find('i').removeClass('far').addClass('fas text-danger');
                    } else {
                        likeBtn.find('i').removeClass('fas text-danger').addClass('far');
                    }

                    // Load comments
                    const commentList = $('.comment-list');
                    commentList.empty();

                    if (response.comments.length === 0) {
                        commentList.html(`
                        <div class="text-center text-muted py-4">
                            <i class="far fa-comments fa-2x mb-2"></i>
                            <p class="mb-0">No comments yet</p>
                            <small>Be the first to comment</small>
                        </div>
                    `);
                    } else {
                        response.comments.forEach(comment => {
                            commentList.append(`
                            <div class="comment rounded p-3 mb-2">
                                <div class="d-flex">
                                    <img src="/assets/img/avatar.png" class="rounded-circle me-2" 
                                        width="32" height="32" alt="${comment.user.name}">
                                    <div class="flex-grow-1">
                                        <div class="bg-light rounded p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="text-primary">${comment.user.name}</strong>
                                                <small class="text-muted ms-2">${formatTimestamp(comment.created_at)}</small>
                                            </div>
                                            <div class="mt-1">${comment.comment}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        });
                    }

                    $('.comments-count').text(response.comments.length);
                },
                error: function (xhr, status, error) {
                    toastr.error('Error loading engagement data');
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

        $(document).on('click', '.preview-container', function () {
            const file = $(this).data('file');
            currentMediaId = file.id;

            const modal = $('#previewModal');
            modal.find('.modal-title').text(file.title);
            const content = modal.find('#previewContent');
            content.empty();


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
            }
            else if (file.file_type === 'video') {
                content.html(`
            <video controls class="w-100">
                <source src="${file.url}">
                Your browser does not support the video tag.
            </video>
        `);
            }
            else if (file.file_type === 'document') {
                const extension = file.url.split('.').pop().toLowerCase();

                switch (extension) {
                    case 'pdf':
                        content.html(`
                    <div class="pdf-container" style="width: 100%; height: 600px;">
                        <object data="${file.url}" type="application/pdf" width="100%" height="100%">
                            <embed src="${file.url}" type="application/pdf" width="100%" height="100%">
                                <p>This browser does not support PDFs. Please download the PDF to view it: 
                                    <a href="${file.url}" download>Download PDF</a>
                                </p>
                            </embed>
                        </object>
                    </div>
                `);
                        break;

                    case 'csv':
                        content.html(`<div class="loader">Loading CSV...</div>`);
                        content.removeClass('bg-black').addClass('bg-white');
                        Papa.parse(file.url, {
                            download: true,
                            complete: function (results) {
                                let html = '<div class="table-responsive bg-white" style="max-height: 600px; overflow-y: auto;">';
                                html += '<table class="table table-bordered table-hover table-striped">';
                                results.data.forEach((row, index) => {
                                    html += '<tr>';
                                    if (index === 0) {
                                        row.forEach(cell => html += `<th class="bg-light">${cell}</th>`);
                                    } else {
                                        row.forEach(cell => html += `<td>${cell}</td>`);
                                    }
                                    html += '</tr>';
                                });
                                html += '</table></div>';
                                content.html(html);
                            },
                            error: function (error) {
                                content.html(`
                            <div class="alert alert-danger">
                                Error loading CSV file. <a href="${file.url}" download>Download instead</a>
                            </div>
                        `);
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
                                let html = '<div class="excel-preview bg-white" style="max-height: calc(100vh - 56px); overflow-y: auto; width: 100%;">';

                                workbook.SheetNames.forEach(sheetName => {
                                    const worksheet = workbook.Sheets[sheetName];
                                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });


                                    const columnCount = Math.max(...jsonData.map(row => row.length));
                                    const tableClass = columnCount <= 4 ? 'table table-bordered table-hover table-striped columns-few w-100'
                                        : 'table table-bordered table-hover table-striped w-100';

                                    html += `
                    <div class="sheet-container bg-white p-3 w-100">
                        <h5 class="text-dark mb-3">Sheet: ${sheetName}</h5>
                        <div class="table-responsive w-100">
                            <table class="${tableClass}">
                `;

                                    jsonData.forEach((row, index) => {
                                        html += '<tr>';
                                        if (index === 0) {

                                            if (columnCount <= 4) {
                                                row.forEach(cell => html += `<th class="bg-light" style="min-width: 200px">${cell || ''}</th>`);

                                                for (let i = row.length; i < 4; i++) {
                                                    html += '<th class="bg-light" style="min-width: 200px"></th>';
                                                }
                                            } else {
                                                row.forEach(cell => html += `<th class="bg-light" style="min-width: 150px">${cell || ''}</th>`);
                                            }
                                        } else {

                                            if (columnCount <= 4) {
                                                row.forEach(cell => html += `<td style="min-width: 200px">${cell || ''}</td>`);
                                                for (let i = row.length; i < 4; i++) {
                                                    html += '<td style="min-width: 200px"></td>';
                                                }
                                            } else {
                                                row.forEach(cell => html += `<td style="min-width: 150px">${cell || ''}</td>`);
                                            }
                                        }
                                        html += '</tr>';
                                    });

                                    html += '</table></div></div>';
                                });
                                html += '</div>';
                                content.html(html);
                            });
                        break;

                    case 'doc':
                    case 'docx':
                        content.html(`<div class="loader">Loading Word document...</div>`);
                        content.removeClass('bg-black').addClass('bg-white');
                        fetch(file.url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.arrayBuffer();
                            })
                            .then(arrayBuffer => {
                                if (arrayBuffer.byteLength === 0) {
                                    throw new Error('File is empty');
                                }
                                return mammoth.convertToHtml({ arrayBuffer });
                            })
                            .then(result => {
                                if (result.value.trim() === '') {
                                    throw new Error('No content could be extracted');
                                }
                                content.html(`
                            <div class="word-preview p-4 bg-white" style="max-height: 600px; overflow-y: auto;">
                                ${result.value}
                            </div>
                        `);
                            })
                            .catch(error => {
                                content.html(`
                            <div class="text-center py-5 bg-white">
                                <i class="fas fa-file-word fa-4x mb-3 text-primary"></i>
                                <h5>Preview not available</h5>
                                <p class="text-muted mb-4">This Word document cannot be previewed in the browser.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="${file.url}" class="btn btn-primary" download>
                                        <i class="fas fa-download me-2"></i>Download Document
                                    </a>
                                    <a href="${file.url}" class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i>Open in New Tab
                                    </a>
                                </div>
                            </div>
                        `);
                            });
                        break;

                    default:
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
            }

            // Record view
            $.ajax({
                url: `/customer/media/${file.id}/view`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    loadEngagement(file.id);
                }
            });

            modal.modal('show');
        });

        $(document).on('click', '.btn-like', function (e) {
            e.preventDefault();
            if (!currentMediaId) return;

            const btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: `/customer/media/${currentMediaId}/toggle-like`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log(response);
                    if (response.action === 'liked') {
                        btn.addClass('liked').find('i').removeClass('far').addClass('fas');
                    } else {
                        btn.removeClass('liked').find('i').removeClass('fas').addClass('far');
                    }
                    $('.likes-count').text(response.likes_count);
                },
                error: function () {
                    toastr.error('Error updating like status');
                },
                complete: function () {
                    btn.prop('disabled', false);
                }
            });
        });

        // $('.comment-form').on('submit', function (e) {
        //     e.preventDefault();
        //     if (!currentMediaId) return;

        //     const form = $(this);
        //     const input = form.find('input[name="comment"]');
        //     const comment = input.val().trim();

        //     if (!comment) {
        //         toastr.warning('Please enter a comment');
        //         return;
        //     }

        //     $.ajax({
        //         url: `/customer/media/${currentMediaId}/comment`,
        //         type: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         data: {
        //             comment: comment
        //         },
        //         success: function (response) {
        //             input.val('');
        //             loadEngagement(currentMediaId);
        //             toastr.success('Comment posted successfully');
        //         },
        //         error: function () {
        //             toastr.error('Error posting comment');
        //         }
        //     });
        // });

        // Media filtering
        $('.btn-filter').click(function () {
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');
            filterMedia();
        });

        $('#tagFilter').on('input', function () {
            filterMedia();
        });

        function filterMedia() {
            const type = $('.btn-filter.active').data('filter');
            const tagSearch = $('#tagFilter').val().toLowerCase();

            $('#mediaGrid .col-md-3').each(function () {
                const fileType = $(this).find('.preview-container').data('file-type');
                const tags = $(this).find('.preview-container').data('tags') || '';

                const matchesType = type === 'all' || type === fileType;
                const matchesTag = !tagSearch || tags.toLowerCase().includes(tagSearch);

                if (matchesType && matchesTag) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            const visibleItems = $('#mediaGrid .col-md-3:visible').length;
            $('#noResults').remove();

            if (visibleItems === 0) {
                $('#mediaGrid').append(`
            <div id="noResults" class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                <h4 class="text-muted">No matching files found</h4>
                <p class="text-muted">Try adjusting your filters</p>
            </div>
        `);
            }
        }

        // Initialize video players
        const players = Array.from(document.querySelectorAll('.plyr')).map(p => new Plyr(p));

        // Modal cleanup
        $('#previewModal').on('hidden.bs.modal', function () {
            currentMediaId = null;
            $(this).find('#previewContent').empty();
            $(this).find('.comment-list').empty();
            $(this).find('.comment-form input').val('');
            $(this).find('.likes-count').text('0');
            $(this).find('.views-count').text('0');
            $(this).find('.comments-count').text('0');
            $(this).find('.btn-like i').removeClass('fas text-danger').addClass('far');
        });

        $(document).on('click', '.download-btn', function (e) {
            if (!$(this).attr('href')) {
                e.preventDefault();
                toastr.error('Download link not available');
            }
        });

        // File Request Form Handling

        fileRequestDropzone = new Dropzone("#dropzoneFileRequest", {
            url: "/customer/file-request",
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            maxFiles: 10,
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar",
            addRemoveLinks: true,
            dictDefaultMessage: "Drop files here or click to upload (required)",
            dictInvalidFileType: "File type not allowed",
            dictFileTooBig: "File is too big (max 10MB)",
            dictMaxFilesExceeded: "Only 10 files can be uploaded at once",
            dictRemoveFile: "Remove",
            init: function () {
                let myDropzone = this;

                this.on("addedfile", function (file) {
                    uploadedFiles.push(file);
                });

                this.on("removedfile", function (file) {
                    uploadedFiles = uploadedFiles.filter(f => f !== file);
                });

                this.on("uploadprogress", function (file, progress) {
                    let progressElement = file.previewElement.querySelector(".dz-progress");
                    if (progressElement) {
                        progressElement.style.display = "block";
                    }
                });

                $("#fileRequestForm").on("submit", function (e) {
                    e.preventDefault();


                    const fileName = $('input[name="file_name"]').val();
                    const description = $('textarea[name="description"]').val();

                    if (!fileName) {
                        toastr.error('File name is required');
                        return;
                    }

                    if (!description) {
                        toastr.error('Description is required');
                        return;
                    }


                    if (myDropzone.files.length === 0) {
                        toastr.error('At least one file is required');
                        return;
                    }


                    myDropzone.processQueue();
                });

                this.on("queuecomplete", function () {
                    submitFileRequest();
                });
            }
        });


        $('#fileRequestModal').on('hidden.bs.modal', function () {
            fileRequestDropzone.removeAllFiles(true);
            uploadedFiles = [];
            $('#fileRequestForm')[0].reset();
        });

        function submitFileRequest() {
            if (uploadedFiles.length === 0) {
                toastr.error('At least one file is required');
                return;
            }

            let formData = new FormData($("#fileRequestForm")[0]);

            uploadedFiles.forEach((file, index) => {
                formData.append('files[]', file);
            });

            let submitBtn = $("#fileRequestForm").find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Submitting...');

            $.ajax({
                url: '/customer/file-request',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        $('#fileRequestModal').modal('hide');
                        toastr.success('File request submitted successfully');
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        Object.keys(xhr.responseJSON.errors).forEach(key => {
                            toastr.error(xhr.responseJSON.errors[key][0]);
                        });
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html('Submit Request');
                }
            });
        }
    });
</script>
@endpush