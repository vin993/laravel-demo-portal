<div class="modal fade" id="previewModal">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header border-0">
				<h5 class="modal-title"></h5>
				<div class="btn-group me-2">
					<button class="btn btn-outline-primary btn-sm edit-material">
						<i class="fas fa-edit"></i> Edit
					</button>
					<button class="btn btn-outline-danger btn-sm delete-material">
						<i class="fas fa-trash"></i> Delete
					</button>
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="engagement-section p-3 border-bottom">
				<div class="d-flex align-items-center justify-content-between mb-3">
					<h6 class="material-title mb-0"></h6>
					<small class="text-muted material-date"></small>
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
			<div class="modal-footer">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<h6>Industries</h6>
							<div class="industry-tags"></div>
						</div>
						<div class="col-md-6">
							<h6>Dealer Companies</h6>
							<div class="dealer-tags"></div>
						</div>
						<div class="col-md-6">
							<h6>Manufacturers</h6>
							<div class="manufacturer-tags"></div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-12">
							<h6>Tags</h6>
							<div class="material-tags"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
