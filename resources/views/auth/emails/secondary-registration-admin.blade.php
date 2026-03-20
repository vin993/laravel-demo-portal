<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>New Secondary User Registration - Admin Notification</title>
<style>
body {
	font-family: Arial, sans-serif;
	line-height: 1.6;
	margin: 0;
	padding: 0;
	background-color: #f5f5f5;
}
.email-container {
	max-width: 600px;
	margin: 20px auto;
	background: white;
	border-radius: 8px;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	padding: 30px;
}
.logo {
	text-align: center;
	margin-bottom: 30px;
	background: #000;
	padding: 20px;
	border-radius: 8px;
}
.logo img {
	max-width: 150px;
	height: auto;
}
.content {
	color: #333;
	padding: 20px;
	border-radius: 8px;
}
h1 {
	color: #2c3e50;
	font-size: 24px;
	margin-bottom: 20px;
	text-align: center;
}
.notification-banner {
	background-color: #FEDB00;
	color: #000;
	padding: 15px;
	border-radius: 6px;
	margin-bottom: 25px;
	text-align: center;
	font-weight: bold;
}
.info-section {
	background: #f8f9fa;
	border: 1px solid #e9ecef;
	border-radius: 6px;
	padding: 20px;
	margin-bottom: 20px;
}
.info-section h2 {
	color: #2c3e50;
	font-size: 18px;
	margin-top: 0;
	margin-bottom: 15px;
	border-bottom: 2px solid #FEDB00;
	padding-bottom: 8px;
}
.info-row {
	display: flex;
	margin-bottom: 10px;
}
.info-label {
	font-weight: bold;
	width: 120px;
	color: #495057;
}
.info-value {
	color: #212529;
}
.action-button {
	text-align: center;
	margin: 30px 0;
}
.review-button {
	display: inline-block;
	padding: 12px 35px;
	background: #FEDB00;
	color: #000;
	text-decoration: none;
	border-radius: 6px;
	font-weight: bold;
	font-size: 16px;
	border: none;
	transition: all 0.3s ease;
}
.review-button:hover {
	background: #FFE44D;
	transform: translateY(-2px);
}
.footer {
	margin-top: 30px;
	padding-top: 20px;
	border-top: 1px solid #dee2e6;
	font-size: 12px;
	color: #6c757d;
	text-align: center;
}
.timestamp {
	color: #6c757d;
	font-size: 12px;
	text-align: right;
	margin-top: 10px;
}
</style>
</head>
<body>
<div class="email-container">
	<div class="logo">
		<img src="{{ url('assets/img/logo.png') }}" alt="Company Logo">
	</div>
	<div class="content">
		<div class="notification-banner">
			New Secondary User Registration Requires Review
		</div>
		<div class="info-section">
			<h2>Secondary User Details</h2>
			<div class="info-row">
				<span class="info-label">Name:</span>
				<span class="info-value">{{ $user->name }}</span>
			</div>
			<div class="info-row">
				<span class="info-label">Email:</span>
				<span class="info-value">{{ $user->email }}</span>
			</div>
			@if($user->userDetail->phone)
			<div class="info-row">
				<span class="info-label">Phone:</span>
				<span class="info-value">{{ $user->userDetail->phone }}</span>
			</div>
			@endif
			<div class="info-row">
				<span class="info-label">Location:</span>
				<span class="info-value">{{ $user->userDetail->city }}, {{ $user->userDetail->state }}, {{ $user->userDetail->country }}</span>
			</div>
		</div>
		<div class="info-section">
			<h2>Primary User Details</h2>
			<div class="info-row">
				<span class="info-label">Name:</span>
				<span class="info-value">{{ $primaryUser->name }}</span>
			</div>
			<div class="info-row">
				<span class="info-label">Email:</span>
				<span class="info-value">{{ $primaryUser->email }}</span>
			</div>
		</div>
		<div class="action-button">
			<a href="{{ url('admin/dashboard') }}" class="review-button">Review Registration</a>
		</div>
		<div class="timestamp">
			Registration Time: {{ $user->created_at->format('Y-m-d H:i:s') }}
		</div>
	</div>
	<div class="footer">
		<p>This is an automated administrative notification. Please do not reply.</p>
		<p>&copy; {{ date('Y') }} Demo App. All rights reserved.</p>
	</div>
</div>
</body>
</html>

