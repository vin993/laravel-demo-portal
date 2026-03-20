<div class="modal fade" id="editModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form id="editForm">
				@csrf
				<input type="hidden" name="material_id" id="edit_material_id">
				<div class="modal-header">
					<h5 class="modal-title">Edit Marketing Material</h5>
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
								<textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
							</div>
							<div class="form-group mb-3">
								<label>Group</label>
								<input type="text" class="form-control" name="group_name" id="edit_group_name" required>
							</div>
							<div class="form-group mb-3">
								<label>Tags</label>
								<input type="text" name="tags" id="edit_tags" class="form-control" placeholder="Enter tags separated by commas">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group mb-3">
								<label>Industries</label>
								<select class="form-control select2-multiple" id="edit_industry_interests" name="industry_ids[]" multiple="multiple" data-search-url="/api/industries/search"></select></select>
							</div>
							<div class="form-group mb-3">
								<label>Dealer Companies</label>
								<select class="form-control select2-multiple" id="edit_dealer_id" name="dealer_ids[]" multiple="multiple" data-search-url="/api/dealers/search"></select>
							</div>
							<div class="form-group mb-3">
								<label>Manufacturers</label>
								<select class="form-control select2-multiple" id="edit_manufacturer_id" name="manufacturer_ids[]" multiple="multiple" data-search-url="/api/manufacturers/search"></select>
							</div>
							<div class="form-group mb-3">
								<label class="d-flex justify-content-between align-items-center">
									<span>Assigned Users <span class="badge bg-secondary ms-2 assigned-users-count">0</span></span>
								</label>
								<div class="assigned-users-list p-2 border rounded bg-light">
									<div class="text-muted small">
										<i class="fas fa-info-circle me-1"></i>
										Select industries and dealers and manufacturers to see assigned users
									</div>
								</div>
							</div>
							<div class="form-check mb-3">
								<input type="checkbox" class="form-check-input" id="edit_is_featured" name="is_featured">
								<label class="form-check-label" for="edit_is_featured">Featured Material</label>
							</div>
						</div>
						<div class="col-md-12">
							<div id="editMaterialDropzone" class="dropzone"></div>
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
