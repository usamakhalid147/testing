<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title> @lang('admin_messages.hotel_panel') - {{ $site_name ?? '' }} </title>
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
		{!! Html::style('host_assets/css/host_app.css?v='.$version) !!}
	</head>
	<body class="login">
		<div class="wrapper wrapper-login">
			{!! Form::open(['url' => route('host.authenticate'), 'class' => 'form-horizontal','id'=>'login-form']) !!}
			<div class="container container-login animated fadeIn">
				<h3 class="text-center">
					Sign In To
					<span class="font-weight-bold text-primary"> {{ $site_name }} </span>
				</h3>
				<div class="login-form">
					<div class="form-group form-floating-label">
						<input id="email" name="email" type="text" class="form-control input-border-bottom" required value="{{ displayCrendentials() ? 'peter@cron24.com' : ''}}">
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
							<a href="#" class="float-end" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">@lang('messages.forgot_password')</a>
						</div>
					</div>
					<div class="form-action mb-3">
						<button type="submit" class="btn btn-primary btn-rounded btn-login">Sign In </button>
					</div>
				</div>
				<div class="text-center">
					@lang('messages.dont_have_an_account')
					<a href="{{ route('host.signup') }}" class="">
						@lang('messages.signup')
					</a>
				</div>
			</div>
			{!! Form::close() !!}
		</div>

		<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModal" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">@lang('messages.reset_password')</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body login-form-wrap">
						<div class="form">
							{!! Form::open(['url' => route('host.reset_password'), 'class' => '','id'=>'reset_password_form']) !!}
							<div class="form-floating">
								<input type="email" name="email" class="form-control" id="forgot_email" placeholder="@lang('messages.email')" value="{{ old('email') }}">
								<label for="forgot_email"> @lang('messages.email') </label>
							</div>
							<span class="text-danger"> {{ $errors->first('email') }} </span>
							<div class="form-group mt-4">
								<button type="submit" class="btn btn-primary d-flex w-100 justify-content-center">
									@lang('messages.send_reset_link')
								</button>
							</div>
							{!! Form::close() !!}
						</div>
						<div class="line-separator my-2 d-flex align-items-center"></div>
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
            const APP_URL = {!! json_encode(url('/')) !!};
            const HOST_URL = {!! json_encode(route('host.dashboard')) !!};
            const SITE_NAME = '{!! $site_name !!}';
            const userCurrency = '{!! global_settings("default_currency") !!}';
            const userLanguage = '{!! session("language") !!}';
            const CURRENCY_SYMBOL = '{!! session("currency_symbol") !!}';
            const STRIPE_PUBLISH_KEY = "{!! credentials('publish_key','Stripe') !!}";
            const DEFAULT_LANGUAGE = '{!! App::getLocale() !!}';
            const currentRouteName = "{!! Route::currentRouteName() !!}";
            const flatpickrFormat = "{!! $selected_format['flatpickr_format'] !!}";
        </script>

		{!! Html::script('host_assets/js/host_app.js?v='.$version) !!}
		{!! Html::script('js/common.js?v='.$version) !!}
		<!-- Jquery UI -->
		<script src="{{ asset('admin_assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
		<!-- Bootstrap Notify -->
        <script src="{{ asset('admin_assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
        <!-- Jquery Backstretch -->
		<script src="{{ asset('admin_assets/js/plugin/backstretch/jquery.backstretch.min.js') }}"></script>

		@if(Session::has('message'))
		<script type="text/javascript">
			$(document).ready(function() {
				let state = "{!! session('state') !!}";

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