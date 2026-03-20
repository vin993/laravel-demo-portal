@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@include('customer.partials.nav')
<div id="layoutSidenav">
	@include('customer.partials.sidenav')
	<div id="layoutSidenav_content">
		<main>
			<div class="container-fluid px-4">
				<!-- Announcements Carousel -->
				<div class="row mt-4">
					<div class="col-12">
						<div class="announcement-wrapper position-relative">
							<div class="announcement-carousel card border-0 shadow-sm">
								<div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel">
									<div class="carousel-inner">
										@forelse($announcements as $key => $announcement)
										<div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
											<div class="row align-items-center">
												<div class="col-md-6">
													@if($announcement->image_path)
													<img src="{{ asset('storage/' . $announcement->image_path) }}"
														class="announcement-image img-fluid"
														alt="{{ $announcement->title }}">
													@endif
												</div>
												<div class="col-md-6">
													<div class="announcement-content p-3">
														<h5 class="mb-2">{{ $announcement->title }}</h5>
														<p class="mb-0 text-muted">
															<small>
																<i class="fas fa-clock me-1"></i>
																{{ $announcement->created_at->format('M d, Y') }}
															</small>
														</p>
													</div>
												</div>
											</div>
										</div>
										@empty
										<div class="carousel-item active">
											<div class="row align-items-center">
												<div class="col-12">
													<div class="announcement-content p-3 text-center">
														<p class="mb-0 text-muted">No announcements available</p>
													</div>
												</div>
											</div>
										</div>
										@endforelse
									</div>
								</div>
							</div>

							@if($announcements->count() > 1)
							<button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel"
								data-bs-slide="prev">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Previous</span>
							</button>
							<button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel"
								data-bs-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
								<span class="visually-hidden">Next</span>
							</button>
							@endif
						</div>
					</div>
				</div>
				<div class="container-fluid px-4">
					<div class="d-flex justify-content-between align-items-center mt-5 mb-4">
						<!-- <h1 class="mb-0">Dashboard</h1> -->
						@if($user->is_primary)
						<span class="badge bg-warning px-3 py-2">
							<i class="fas fa-star me-2"></i>Primary User Account
						</span>
						@endif
					</div>

					<div class="row">
						<div class="col-xl-6">
							<div class="card mb-4 border-0 shadow-sm">
								<div class="card-header tbl-head-crd-header">
									<i class="fas fa-user-circle me-2"></i>Profile Information
								</div>
								<div class="card-body">
									<ul class="list-unstyled">
										<li class="mb-3">
											<i class="fas fa-user me-2 text-primary"></i>
											<strong>Name:</strong> {{ $user->name }}
										</li>
										<li class="mb-3">
											<i class="fas fa-envelope me-2 text-primary"></i>
											<strong>Email:</strong> {{ $user->email }}
										</li>
										<!-- <li class="mb-3">
											<i class="fas fa-building me-2 text-primary"></i>
											<strong>Company:</strong> {{ $user->userDetail->company_name ?? 'N/A' }}
										</li> -->
										@if($user->is_primary)
										<li class="mb-3">
											<i class="fas fa-clock me-2 text-primary"></i>
											<strong>Primary Since:</strong>
											{{ $user->primary_user_since ? $user->primary_user_since->format('M d, Y') :
											'N/A' }}
										</li>
										<div class="alert alert-warning mt-3 mb-0">
											<i class="fas fa-info-circle me-2"></i>
											<strong>Primary User Privileges:</strong>
											<ul class="mb-0 mt-2">
												<li>Manage team members</li>
												<li>Access advanced features</li>
												<li>View analytics</li>
											</ul>
										</div>
										@endif
									</ul>
								</div>
							</div>
						</div>

						<div class="col-xl-6">
							<div class="card mb-4 border-0 shadow-sm">
								<div class="card-header tbl-head-crd-header">
									<i class="fas fa-industry me-2"></i>Industry Interests
								</div>
								<div class="card-body">
									<div class="tags">
										@forelse($user->industries as $industry)
										<span class="badge rounded-pill mb-2 me-2"
											style="background-color: #FEDB00; color: #000; padding: 8px 16px; font-size: 0.9em;">
											<i class="fas fa-industry me-1"></i>
											{{ $industry->name }}
										</span>
										@empty
										<div class="text-muted">
											<i class="fas fa-info-circle me-2"></i>No industries selected
										</div>
										@endforelse
									</div>
								</div>
							</div>

							<div class="card mb-4 border-0 shadow-sm">
								<div class="card-header tbl-head-crd-header">
									<i class="fas fa-store me-2"></i>Dealer Companies
								</div>
								<div class="card-body">
									<div class="tags">
										@forelse($user->dealers as $dealer)
										<span class="badge rounded-pill mb-2 me-2"
											style="background-color: #FEDB00; color: #000; padding: 8px 16px; font-size: 0.9em;">
											<i class="fas fa-store me-1"></i>
											{{ $dealer->name }}
										</span>
										@empty
										<div class="text-muted">
											<i class="fas fa-info-circle me-2"></i>No dealers selected
										</div>
										@endforelse
									</div>
								</div>
							</div>

							<div class="card mb-4 border-0 shadow-sm">
								<div class="card-header tbl-head-crd-header">
									<i class="fas fa-store me-2"></i>Manufacturers
								</div>
								<div class="card-body">
									<div class="tags">
										@forelse($user->manufacturers as $manufacturer)
										<span class="badge rounded-pill mb-2 me-2"
											style="background-color: #FEDB00; color: #000; padding: 8px 16px; font-size: 0.9em;">
											<i class="fas fa-store me-1"></i>
											{{ $manufacturer->name }}
										</span>
										@empty
										<div class="text-muted">
											<i class="fas fa-info-circle me-2"></i>No manufacturers selected
										</div>
										@endforelse
									</div>
								</div>
							</div>

							@if($user->is_primary)
							<div class="card mb-4 border-0 shadow-sm">
								<div class="card-header tbl-head-crd-header">
									<i class="fas fa-chart-line me-2"></i>Quick Actions
								</div>
								<div class="card-body">
									<div class="d-grid gap-2">
										<a href="{{ route('customer.managed-users') }}" class="btn btn-warning">
											<i class="fas fa-users me-2"></i>Managed Users
										</a>
										<a href="{{ route('customer.medias') }}" class="btn btn-warning">
											<i class="fas fa-image me-2"></i>Media Files
										</a>
										<a href="{{ route('profile.edit') }}" class="btn btn-warning">
											<i class="fas fa-cog me-2"></i>My Profile
										</a>
									</div>
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>
		</main>
		@include('customer.partials.footer')
	</div>
</div>

<style>
.card {
	transition: transform 0.2s;
}
.card:hover {
	transform: translateY(-5px);
}
.badge {
	transition: all 0.2s;
}
.badge:hover {
	transform: scale(1.05);
}
.btn-warning,
.btn-outline-warning {
	transition: all 0.2s;
}
.btn-warning:hover,
.btn-outline-warning:hover {
	background-color: white;
	transform: translateY(-2px);
}
.announcement-wrapper {
	max-width: 1200px;
	margin: 0 auto;
	padding: 0 15px;
	position: relative;
}
.announcement-carousel {
	background: white;
	border-radius: 8px;
	margin: 0 15px;
	overflow: hidden;
}
.announcement-carousel .carousel-item {
	min-height: 350px;
	background: white;
	transition: transform 0.6s ease-in-out;
}
.announcement-image {
	object-fit: cover;
	height: 350px;
	width: 100%;
	padding: 15px;
	border-radius: 12px;
}
.announcement-content {
	height: 100%;
	display: flex;
	flex-direction: column;
	justify-content: center;
	padding: 25px;
}
.announcement-content h5 {
	font-size: 1.75rem;
	font-weight: 600;
	margin-bottom: 1rem;
	color: #2c3e50;
}
.carousel-control-prev,
.carousel-control-next {
	width: 30px;
	height: 30px;
	top: 50%;
	transform: translateY(-50%);
	background-color: #FEDB00;
	border-radius: 50%;
	opacity: 0.9;
	z-index: 10;
	margin-left: -10px;
	margin-right: -10px;
}
.carousel-control-prev {
	left: 0;
}
.carousel-control-next {
	right: 0;
}
.carousel-control-prev:hover,
.carousel-control-next:hover {
	opacity: 1;
	background-color: #e5c500;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
	width: 20px;
	height: 20px;
}
</style>
@endsection
