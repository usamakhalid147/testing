<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title> Login to Admin Panel - {{ $site_name ?? '' }} </title>
		<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
		<link rel="shortcut icon" href="{{ $favicon ?? '' }}">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Fonts and icons -->
        <script src="{{ asset('admin_assets/js/plugin/webfont/webfont.min.js') }}"></script>
        <script>
	        WebFont.load({
		        google: {"families":["Lato:300,400,700,900"]},
		        custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ["{{ asset('admin_assets/css/fonts.min.css') }} "]},
				active: function() {
					sessionStorage.fonts = true;
				}
	        });
        </script>
		
		<!-- CSS Files -->
		{!! Html::style('admin_assets/css/admin_app.css?v='.$version) !!}
	</head>
	<body class="login no-transition">
		<div class="wrapper wrapper-login">
			{!! Form::open(['url' => route('admin.authenticate'), 'class' => 'form-horizontal','id'=>'login-form']) !!}
			<div class="container container-login animated fadeIn">
				<h3 class="text-center">
					@lang('admin_messages.sign_in_to')
					<span class="fw-bold text-primary"> {{ $site_name }} </span>
				</h3>
				<div class="login-form">
					<div class="form-group form-floating-label">
						<input id="email" name="email" type="text" class="form-control input-border-bottom" value="{{ displayCrendentials() ? 'admin@gmail.com' : ''}}" required>
						<label for="email" class="form-placeholder"> @lang('admin_messages.email') </label>
					</div>
					<div class="form-group form-floating-label">
						<input id="password" name="password" type="password" class="form-control input-border-bottom" value="{{ displayCrendentials() ? '12345678' : ''}}" required>
						<label for="password" class="form-placeholder"> @lang('admin_messages.password') </label>
						<div class="show-password">
							<i class="flaticon-interface"></i>
						</div>
					</div>
					<div class="row form-sub ms-2">
						<div class="form-check">
							<input type="checkbox" name="remember_me" class="form-check-input" id="rememberme">
							<label for="rememberme" class="form-check-label"> @lang('messages.remember_me') </label>
						</div>
					</div>
					<div class="form-action mt-2">
						<button type="submit" class="btn btn-primary btn-rounded btn-login"> @lang('messages.signin') </button>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
		{!! Html::script('admin_assets/js/admin_app.js?v='.$version) !!}
		{!! Html::script('admin_assets/js/common.js?v='.$version) !!}

		<!-- Jquery UI -->
		<script src="{{ asset('admin_assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
		<!-- Bootstrap Notify -->
        <script src="{{ asset('admin_assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
        <!-- Jquery Backstretch -->
		<script src="{{ asset('admin_assets/js/plugin/backstretch/jquery.backstretch.min.js') }}"></script>

		@if(Session::has('message'))
		<script type="text/javascript">
			$(document).ready(function() {
				state = "{!! session('state') !!}";

				let content = {
					title: "{!! session('title') !!}",
					message: "{!! session('message') !!}"
				};
               	flashMessage(content,state);
			});
		</script>
		@endif
		<script type="text/javascript">
			// Show Password
			function showPassword(button) {
				var inputPassword = $(button).parent().find('input');
				if (inputPassword.attr('type') === "password") {
					inputPassword.attr('type', 'text');
				} else {
					inputPassword.attr('type','password');
				}
			}

			$('.show-password').on('click', function(){
				showPassword(this);
			});

			//Input with Floating Label
			$('.form-floating-label .form-control').keyup(function(){
				if($(this).val() !== '') {
					$(this).addClass('filled');
				} else {
					$(this).removeClass('filled');
				}
			});

			$(document).ready(function() {
				var sliders = {!! $sliders !!};
				var slider_options = {duration: 3000, fade: 1000};
				$.backstretch(sliders, slider_options);
			});

		</script>
	</body>
</html>