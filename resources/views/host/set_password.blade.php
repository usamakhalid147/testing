<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title> Reset Password - {{ $site_name ?? '' }} </title>
		<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
		<link rel="shortcut icon" href="{{ $favicon ?? '' }}">
		<meta name="csrf-token" content="{{ csrf_token() }}">      
		
		<!-- CSS Files -->
		{!! Html::style('host_assets/css/host_app.css?v='.$version) !!}
	</head>
	<body>
		<div class="row d-flex justify-content-center mt-4">
			<div class="col-md-4">
				<div class="card mx-auto">
					<div class="card-body">
						<div class="form-container">
							<h3 class="text-center"> @lang('messages.set_new_password') </h3>
							{!! Form::open(['url' => route('host.set_password'), 'class' => '','id'=>'set_password_form']) !!}
							{!! Form::hidden('email',$email) !!}
							{!! Form::hidden('reset_token',$reset_token) !!}
							<div class="form-group">
								<label for="new_password" class="form-label"> @lang('messages.new_password') </label>
								<input type="password" name="password" class="form-control">
							</div>
							<div class="form-group">
								<label for="confirm_password" class="form-label"> @lang('messages.confirm_password') </label>
								<input type="password" name="password_confirmation" class="form-control">
							</div>
							<span class="text-danger"> {{ $errors->first('password') }} </span>
							<div class="form-group mt-4">
								<button type="submit" class="btn btn-primary d-flex w-100 justify-content-center">
								@lang('messages.update_password')
								</button>
							</div>
							{!! Form::close() !!}
						</div>
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
		<!-- Jquery UI -->
		<script src="{{ asset('admin_assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
		<!-- Bootstrap Notify -->
        <script src="{{ asset('admin_assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
        <!-- Jquery Backstretch -->
		<script src="{{ asset('admin_assets/js/plugin/backstretch/jquery.backstretch.min.js') }}"></script>

		@if(Session::has('message'))
		<script type="text/javascript">
			$(document).ready(function() {
				var content = {};
				state = "{!! Session::get('state') !!}";
				content.message = "{!! Session::get('message') !!}";
				content.title = "{!! Session::get('title') !!}";
				content.icon = 'fa fa-bell';

				$.notify(content,{
					type: state,
					placement: {
						from: "top",
						align: "center"
					},
					time: 2000,
					delay: 0,
				});
			});
		</script>
		@endif
		<script type="text/javascript">
			$(document).ready(function() {
				var sliders = {!! $sliders !!};
				var slider_options = {duration: 3000, fade: 1000};
				$.backstretch(sliders, slider_options);
			});

		</script>
	</body>
</html>