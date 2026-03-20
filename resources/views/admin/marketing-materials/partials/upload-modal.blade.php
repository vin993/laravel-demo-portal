<div class="modal fade" id="uploadModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="uploadForm" enctype="multipart/form-data">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title">Upload Marketing Material</h5>
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
								<input type="text" class="form-control" name="group_name" id="upload_group" required>
								<div class="invalid-feedback">Please enter a group name</div>
							</div>
							<div class="form-group mb-3">
								<label>Tags</label>
								<input type="text" name="tags" class="form-control" placeholder="Enter tags separated by commas">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label>Industries</label>
								<select class="form-control select2-multiple" id="upload_industry_interests" name="industry_ids[]" multiple="multiple"></select>
							</div>
							<div class="form-group mb-3">
								<label>Dealer Companies</label>
								<select class="form-control select2-multiple" id="upload_dealer_id" name="dealer_ids[]" multiple="multiple"></select>
							</div>
							<div class="form-group mb-3">
								<label>Manufacturers</label>
								<select class="form-control select2-multiple" id="upload_manufacturer_id" name="manufacturer_ids[]" multiple="multiple"></select>
							</div>
							<div class="form-group mb-3">
								<label class="d-flex justify-content-between align-items-center">
									<span>Assigned Users <span class="badge bg-secondary ms-2 assigned-users-count">0</span></span>
								</label>
								<div class="assigned-users-list p-2 border rounded bg-light">
									<div class="text-muted small">
										<i class="fas fa-info-circle me-1"></i>
										Select industries, dealers and manufacturers to see assigned users
									</div>
								</div>
								<small class="text-muted">
									<i class="fas fa-user-shield me-1"></i>
									Only approved users with matching industries, dealers and manufacturers will have access
								</small>
							</div>
							<div class="form-check mb-3">
								<input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
								<label class="form-check-label" for="is_featured">Featured Material</label>
							</div>
						</div>
						<div class="col-md-12">
							<div id="materialDropzone" class="dropzone"></div>
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
