<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset Your Password</title>
<style>
body {
	font-family: Arial, sans-serif;
	line-height: 1.6;
	margin: 0;
	padding: 0;
	background-color: #FEDB00;
}
.email-container {
	max-width: 600px;
	margin: 20px auto;
	background: white;
	border-radius: 8px;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	padding: 30px;
	border: 1px solid #ddd;
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
	border: 1px solid #eee;
	border-radius: 8px;
	background: #fff;
}
h1 {
	color: #2c3e50;
	font-size: 24px;
	margin-bottom: 20px;
	text-align: center;
}
p {
	margin-bottom: 20px;
	font-size: 16px;
}
.button {
	text-align: center;
	margin: 30px 0;
}
.reset-button {
	display: inline-block;
	padding: 15px 40px;
	background: linear-gradient(45deg, #FEDB00, #FFE44D);
	color: #000;
	text-decoration: none;
	border-radius: 30px;
	font-weight: bold;
	text-transform: uppercase;
	font-size: 16px;
	transition: all 0.3s ease;
	border: 2px solid #FEDB00;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.reset-button:hover {
	background: linear-gradient(45deg, #FFE44D, #FEDB00);
	transform: translateY(-2px);
	box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
}
.footer {
	margin-top: 30px;
	padding-top: 20px;
	border-top: 2px solid #eee;
	font-size: 12px;
	color: #666;
	text-align: center;
}
.link-container {
	background: #f9f9f9;
	padding: 15px;
	border-radius: 5px;
	border: 1px solid #eee;
	margin: 20px 0;
}
</style>
</head>
<body>
<div class="email-container">
	<div class="logo">
		<img src="{{ url('assets/img/logo.png') }}" alt="Company Logo">
	</div>
	<div class="content">
		<h1>Reset Your Password</h1>
		<p>Hello,</p>
		<p>We received a request to reset your password. If you didn't make this request, you can ignore this email.</p>
		<p>To reset your password, click the button below:</p>
		<div class="button">
			<a href="{{ url('reset-password', $token) }}" class="reset-button">Reset Password</a>
		</div>
		<p>If the button doesn't work, you can copy and paste this link into your browser:</p>
		<div class="link-container">
			<p style="word-break: break-all; font-size: 14px; color: #666; margin: 0;">
				{{ url('reset-password', $token) }}
			</p>
		</div>
		<p>This password reset link will expire in 60 minutes.</p>
	</div>
	<div class="footer">
		<p>This is an automated email, please do not reply.</p>
		<p>&copy; {{ date('Y') }} Demo App. All rights reserved.</p>
	</div>
</div>
</body>
</html>

