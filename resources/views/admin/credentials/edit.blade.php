@extends('layouts.adminLayout.app')
@section('api_credentials')
<li class="nav-item">
	<a class="nav-link active" href="#map" role="tab" data-bs-toggle="tab">
		<i class="fas fa-map-marked-alt"></i>@lang('admin_messages.map')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#google" role="tab" data-bs-toggle="tab">
		<i class="fab fa-google"></i>@lang('admin_messages.google')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#facebook" role="tab" data-bs-toggle="tab">
		<i class="fab fa-facebook"></i>@lang('admin_messages.facebook')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#apple" role="tab" data-bs-toggle="tab">
		<i class="fab fa-apple"></i>@lang('admin_messages.apple')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#cloudinary" role="tab" data-bs-toggle="tab">
		<i class="fas fa-cloud-upload-alt"></i>@lang('admin_messages.cloudinary')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#twilio" role="tab" data-bs-toggle="tab">
		<i class="fas fa-envelope"></i>@lang('admin_messages.twilio')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#recaptcha" role="tab" data-bs-toggle="tab">
		<i class="fas fa-recycle"></i>@lang('admin_messages.recaptcha')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#firebase" role="tab" data-bs-toggle="tab">
		<i class="fas fa-comments"></i>@lang('admin_messages.firebase')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#conveythis" role="tab" data-bs-toggle="tab">
		<i class="fas fa-language"></i>@lang('admin_messages.conveythis')
	</a>
</li>
@endsection
@section('payment_gateways')
<li class="nav-item">
	<a class="nav-link active" href="#stripe" role="tab" data-bs-toggle="tab">
		<i class="fab fa-cc-stripe"></i>
		@lang('admin_messages.stripe')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#paypal" role="tab" data-bs-toggle="tab">
		<i class="fab fa-paypal"></i>@lang('admin_messages.paypal')
	</a>
</li>
<li class="nav-item">
	<a class="nav-link" href="#one_pay" role="tab" data-bs-toggle="tab">
		<i class="fas fa-credit-card"></i>
		@lang('admin_messages.one_pay')
	</a>
</li>
@endsection
@section('map')
<div class="form-group">
	<label for="is_google_map_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_google_map_enabled',$yes_no_array, old('is_google_map_enabled',credentials('is_enabled','googleMap')), ['class' => 'form-select', 'id' => 'is_google_map_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_google_map_enabled') }} </span>
</div>
<div class="form-group">
	<label for="map_api_key"> @lang('admin_messages.map_api_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('map_api_key', old('map_api_key',credentials('map_api_key','googleMap')), ['class' => 'form-control', 'id' => 'map_api_key']) !!}
	<span class="text-danger"> {{ $errors->first('map_api_key') }} </span>
</div>
<div class="form-group">
	<label for="map_server_key"> @lang('admin_messages.map_server_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('map_server_key', old('map_server_key',credentials('map_server_key','googleMap')), ['class' => 'form-control', 'id' => 'map_server_key']) !!}
	<span class="text-danger"> {{ $errors->first('map_server_key') }} </span>
</div>
@endsection
@section('google')
<div class="form-group">
	<label for="is_google_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_google_enabled',$yes_no_array, old('is_google_enabled',credentials('is_enabled','Google')), ['class' => 'form-select', 'id' => 'is_google_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_google_enabled') }} </span>
</div>
<div class="form-group">
	<label for="google_client_id"> @lang('admin_messages.client_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('google_client_id', old('google_client_id',credentials('client_id','Google')), ['class' => 'form-control', 'id' => 'google_client_id']) !!}
	<span class="text-danger"> {{ $errors->first('google_client_id') }} </span>
</div>
<div class="form-group">
	<label for="google_secret_key"> @lang('admin_messages.secret_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('google_secret_key', old('google_secret_key',credentials('secret_key','Google')), ['class' => 'form-control', 'id' => 'google_secret_key']) !!}
	<span class="text-danger"> {{ $errors->first('google_secret_key') }} </span>
</div>
@endsection
@section('facebook')
<div class="form-group">
	<label for="is_facebook_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_facebook_enabled',$yes_no_array, old('is_facebook_enabled',credentials('is_enabled','Facebook')), ['class' => 'form-select', 'id' => 'is_facebook_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_facebook_enabled') }} </span>
</div>
<div class="form-group">
	<label for="facebook_app_id"> @lang('admin_messages.app_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('facebook_app_id', old('facebook_app_id',credentials('app_id','Facebook')), ['class' => 'form-control', 'id' => 'facebook_app_id']) !!}
	<span class="text-danger"> {{ $errors->first('facebook_app_id') }} </span>
</div>
<div class="form-group">
	<label for="facebook_app_secret"> @lang('admin_messages.app_secret') <em class="text-danger"> * </em> </label>
	{!! Form::text('facebook_app_secret', old('facebook_app_secret',credentials('app_secret','Facebook')), ['class' => 'form-control', 'id' => 'facebook_app_secret']) !!}
	<span class="text-danger"> {{ $errors->first('facebook_app_secret') }} </span>
</div>
@endsection
@section('apple')
<div class="form-group">
	<label for="is_apple_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_apple_enabled',$yes_no_array, old('is_apple_enabled',credentials('is_enabled','Facebook')), ['class' => 'form-select', 'id' => 'is_apple_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_apple_enabled') }} </span>
</div>
<div class="form-group">
	<label for="apple_service_id"> @lang('admin_messages.service_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('apple_service_id', old('apple_service_id',credentials('service_id','Apple')), ['class' => 'form-control', 'id' => 'apple_service_id']) !!}
	<span class="text-danger"> {{ $errors->first('apple_service_id') }} </span>
</div>
<div class="form-group">
	<label for="apple_team_id"> @lang('admin_messages.team_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('apple_team_id', old('apple_team_id',credentials('team_id','Apple')), ['class' => 'form-control', 'id' => 'apple_team_id']) !!}
	<span class="text-danger"> {{ $errors->first('apple_team_id') }} </span>
</div>
<div class="form-group">
	<label for="apple_key_id"> @lang('admin_messages.key_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('apple_key_id', old('apple_key_id',credentials('key_id','Apple')), ['class' => 'form-control', 'id' => 'apple_key_id']) !!}
	<span class="text-danger"> {{ $errors->first('apple_key_id') }} </span>
</div>
<div class="form-group">
	<label for="apple_key_file" class="form-label"> @lang('admin_messages.key_file') <em class="text-danger"> * </em> </label>
	{!! Form::file('apple_key_file', ['class' => 'form-control', 'id' => 'apple_key_file']) !!}
	<span class="text-danger"> {{ $errors->first('apple_key_file') }} </span>
</div>
@endsection
@section('cloudinary')
<div class="form-group">
	<label for="cloud_name"> @lang('admin_messages.cloud_name') <em class="text-danger"> * </em> </label>
	{!! Form::text('cloud_name', old('cloud_name',credentials('cloud_name','Cloudinary')), ['class' => 'form-control', 'id' => 'cloud_name']) !!}
	<span class="text-danger"> {{ $errors->first('cloud_name') }} </span>
</div>
<div class="form-group">
	<label for="cloud_api_key"> @lang('admin_messages.cloud_api_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('cloud_api_key', old('cloud_api_key',credentials('api_key','Cloudinary')), ['class' => 'form-control', 'id' => 'cloud_api_key']) !!}
	<span class="text-danger"> {{ $errors->first('cloud_api_key') }} </span>
</div>
<div class="form-group">
	<label for="cloud_api_secret"> @lang('admin_messages.cloud_api_secret') <em class="text-danger"> * </em> </label>
	{!! Form::text('cloud_api_secret', old('cloud_api_secret',credentials('api_secret','Cloudinary')), ['class' => 'form-control', 'id' => 'cloud_api_secret']) !!}
	<span class="text-danger"> {{ $errors->first('cloud_api_secret') }} </span>
</div>
@endsection
@section('twilio')
<div class="form-group">
	<label for="is_twilio_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_twilio_enabled',$yes_no_array, old('is_twilio_enabled',credentials('is_enabled','Twilio')), ['class' => 'form-select', 'id' => 'is_twilio_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_twilio_enabled') }} </span>
</div>
<div class="form-group">
	<label for="account_sid"> @lang('admin_messages.account_sid') <em class="text-danger"> * </em> </label>
	{!! Form::text('account_sid', old('account_sid',credentials('account_sid','Twilio')), ['class' => 'form-control', 'id' => 'account_sid']) !!}
	<span class="text-danger"> {{ $errors->first('account_sid') }} </span>
</div>
<div class="form-group">
	<label for="auth_token"> @lang('admin_messages.auth_token') <em class="text-danger"> * </em> </label>
	{!! Form::text('auth_token', old('auth_token',credentials('auth_token','Twilio')), ['class' => 'form-control', 'id' => 'auth_token']) !!}
	<span class="text-danger"> {{ $errors->first('auth_token') }} </span>
</div>
<div class="form-group">
	<label for="from_number"> @lang('admin_messages.from_number') <em class="text-danger"> * </em> </label>
	{!! Form::text('from_number', old('from_number',credentials('from_number','Twilio')), ['class' => 'form-control', 'id' => 'from_number']) !!}
	<span class="text-danger"> {{ $errors->first('from_number') }} </span>
</div>
@endsection
@section('recaptcha')
<div class="form-group">
	<label for="is_recaptcha_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_recaptcha_enabled',$yes_no_array, old('is_recaptcha_enabled',credentials('is_enabled','ReCaptcha')), ['class' => 'form-select', 'id' => 'is_recaptcha_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_recaptcha_enabled') }} </span>
</div>
<div class="form-group">
	<label for="recaptcha_version"> @lang('admin_messages.recaptcha_version') <em class="text-danger"> * </em> </label>
	{!! Form::select('recaptcha_version',array('2' => Lang::get('admin_messages.version_2'),'3' => Lang::get('admin_messages.version_3')), old('recaptcha_version',credentials('recaptcha_version','ReCaptcha')), ['class' => 'form-select', 'id' => 'recaptcha_version']) !!}
	<span class="text-danger"> {{ $errors->first('recaptcha_version') }} </span>
</div>
<div class="form-group">
	<label for="site_key"> @lang('admin_messages.site_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('recaptcha_site_key', old('recaptcha_site_key',credentials('site_key','ReCaptcha')), ['class' => 'form-control', 'id' => 'site_key']) !!}
	<span class="text-danger"> {{ $errors->first('recaptcha_site_key') }} </span>
</div>
<div class="form-group">
	<label for="cloud_api_secret"> @lang('admin_messages.secret_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('recaptcha_secret_key', old('recaptcha_secret_key',credentials('secret_key','ReCaptcha')), ['class' => 'form-control', 'id' => 'secret_key']) !!}
	<span class="text-danger"> {{ $errors->first('recaptcha_secret_key') }} </span>
</div>
@endsection
@section('firebase')
<div class="form-group">
	<label for="is_firebase_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_firebase_enabled',$yes_no_array, old('is_firebase_enabled',credentials('is_enabled','Firebase')), ['class' => 'form-select', 'id' => 'is_firebase_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_firebase_enabled') }} </span>
</div>
<div class="form-group">
	<label for="firebase_api_key" class="form-label"> @lang('admin_messages.api_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_api_key', old('firebase_api_key',credentials('api_key','Firebase')), ['class' => 'form-control', 'id' => 'firebase_api_key', 'placeholder' => Lang::get('admin_messages.api_key')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_api_key') }} </span>
</div>
<div class="form-group">
	<label for="firebase_auth_domain" class="form-label"> @lang('admin_messages.auth_domain') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_auth_domain', old('firebase_auth_domain',credentials('auth_domain','Firebase')), ['class' => 'form-control', 'id' => 'firebase_auth_domain', 'placeholder' => Lang::get('admin_messages.auth_domain')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_auth_domain') }} </span>
</div>
<div class="form-group">
	<label for="firebase_database_url" class="form-label"> @lang('admin_messages.database_url') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_database_url', old('firebase_database_url',credentials('database_url','Firebase')), ['class' => 'form-control', 'id' => 'firebase_database_url', 'placeholder' => Lang::get('admin_messages.database_url')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_database_url') }} </span>
</div>
<div class="form-group">
	<label for="firebase_project_id" class="form-label"> @lang('admin_messages.project_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_project_id', old('firebase_project_id',credentials('project_id','Firebase')), ['class' => 'form-control', 'id' => 'firebase_project_id', 'placeholder' => Lang::get('admin_messages.project_id')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_project_id') }} </span>
</div>
<div class="form-group">
	<label for="firebase_storage_bucket" class="form-label"> @lang('admin_messages.storage_bucket') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_storage_bucket', old('firebase_storage_bucket',credentials('storage_bucket','Firebase')), ['class' => 'form-control', 'id' => 'firebase_storage_bucket', 'placeholder' => Lang::get('admin_messages.storage_bucket')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_storage_bucket') }} </span>
</div>
<div class="form-group">
	<label for="firebase_messaging_sender_id" class="form-label"> @lang('admin_messages.messaging_sender_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_messaging_sender_id', old('firebase_messaging_sender_id',credentials('messaging_sender_id','Firebase')), ['class' => 'form-control', 'id' => 'firebase_messaging_sender_id', 'placeholder' => Lang::get('admin_messages.messaging_sender_id')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_messaging_sender_id') }} </span>
</div>
<div class="form-group">
	<label for="firebase_app_id" class="form-label"> @lang('admin_messages.app_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('firebase_app_id', old('firebase_app_id',credentials('app_id','Firebase')), ['class' => 'form-control', 'id' => 'firebase_app_id', 'placeholder' => Lang::get('admin_messages.app_id')]) !!}
	<span class="text-danger"> {{ $errors->first('firebase_app_id') }} </span>
</div>
<div class="form-group">
	<label for="firebase_service_account" class="form-label"> @lang('admin_messages.service_account') <em class="text-danger"> * </em> </label>
	{!! Form::file('firebase_service_account', ['class' => 'form-control', 'id' => 'firebase_service_account']) !!}
	<span class="text-danger"> {{ $errors->first('firebase_service_account') }} </span>
</div>
@endsection
@section('conveythis')
<div class="form-group">
	<label for="is_conveythis_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! 
		Form::select(
			'is_conveythis_enabled',
			$yes_no_array,
			old('is_conveythis_enabled',credentials('is_enabled','Conveythis')),
			[
				'class' => 'form-select',
				'id' => 'is_conveythis_enabled'
			]
		)
	!!}
	<span class="text-danger"> {{ $errors->first('is_conveythis_enabled') }} </span>
</div>
@endsection
@section('stripe')
<div class="form-group">
	<label for="is_stripe_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_stripe_enabled',$yes_no_array, old('is_stripe_enabled',credentials('is_enabled','Stripe')), ['class' => 'form-select', 'id' => 'is_stripe_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_stripe_enabled') }} </span>
</div>
<div class="form-group">
	<label for="stripe_publish_key"> @lang('admin_messages.publish_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('stripe_publish_key', old('stripe_publish_key',credentials('publish_key','Stripe')), ['class' => 'form-control', 'id' => 'stripe_publish_key']) !!}
	<span class="text-danger"> {{ $errors->first('stripe_publish_key') }} </span>
</div>
<div class="form-group">
	<label for="stripe_secret_key"> @lang('admin_messages.secret_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('stripe_secret_key', old('stripe_secret_key',credentials('secret_key','Stripe')), ['class' => 'form-control', 'id' => 'stripe_secret_key']) !!}
	<span class="text-danger"> {{ $errors->first('stripe_secret_key') }} </span>
</div>
<div class="form-group">
	<label for="stripe_currency_code"> @lang('admin_messages.payment_currency') <em class="text-danger"> * </em> </label>
	{!! Form::select('stripe_currency_code', $payment_currencies, old('stripe_currency_code',credentials('payment_currency','Stripe')), ['class' => 'form-select', 'id' => 'stripe_currency_code']) !!}
	<span class="text-danger"> {{ $errors->first('stripe_currency_code') }} </span>
</div>
<div class="form-group">
	<label for="stripe_account_type"> @lang('admin_messages.account_type') <em class="text-danger"> * </em> </label>
	{!! Form::select('stripe_account_type', array('custom' => Lang::get('admin_messages.custom') , 'express' => Lang::get('admin_messages.express')), old('stripe_account_type',credentials('account_type','Stripe')), ['class' => 'form-select', 'id' => 'stripe_account_type']) !!}
	<span class="text-danger"> {{ $errors->first('stripe_account_type') }} </span>
</div>
@endsection
@section('one_pay')
<div class="form-group">
	<label for="is_one_pay_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_one_pay_enabled',$yes_no_array, old('is_one_pay_enabled',credentials('is_enabled','OnePay')), ['class' => 'form-select', 'id' => 'is_one_pay_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_one_pay_enabled') }} </span>
</div>
<div class="form-group">
	<label for="one_pay_mode"> @lang('admin_messages.paymode') <em class="text-danger"> * </em> </label>
	{!! Form::select('one_pay_mode', array('sandbox' => 'Sandbox', 'live' => 'Live'), old('one_pay_mode',credentials('paymode','OnePay')), ['class' => 'form-control', 'id' => 'one_pay_mode']) !!}
	<span class="text-danger"> {{ $errors->first('one_pay_mode') }} </span>
</div>
<div class="form-group">
	<label for="one_pay_access_code"> @lang('admin_messages.access_code') <em class="text-danger"> * </em> </label>
	{!! Form::text('one_pay_access_code', old('one_pay_access_code',credentials('access_code','OnePay')), ['class' => 'form-control', 'id' => 'one_pay_access_code']) !!}
	<span class="text-danger"> {{ $errors->first('one_pay_access_code') }} </span>
</div>
<div class="form-group">
	<label for="one_pay_merchant"> @lang('admin_messages.merchant') <em class="text-danger"> * </em> </label>
	{!! Form::text('one_pay_merchant', old('one_pay_merchant',credentials('merchant','OnePay')), ['class' => 'form-control', 'id' => 'one_pay_merchant']) !!}
	<span class="text-danger"> {{ $errors->first('one_pay_merchant') }} </span>
</div>
<div class="form-group">
	<label for="one_pay_hash_key"> @lang('admin_messages.hash_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('one_pay_hash_key', old('one_pay_hash_key',credentials('hash_key','OnePay')), ['class' => 'form-control', 'id' => 'one_pay_hash_key']) !!}
	<span class="text-danger"> {{ $errors->first('one_pay_hash_key') }} </span>
</div>
<div class="form-group">
	<label for="one_pay_currency_code"> @lang('admin_messages.payment_currency') <em class="text-danger"> * </em> </label>
	{!! Form::select('one_pay_currency_code', $payment_currencies, old('one_pay_currency_code',credentials('payment_currency','OnePay')), ['class' => 'form-select', 'id' => 'one_pay_currency_code']) !!}
	<span class="text-danger"> {{ $errors->first('one_pay_currency_code') }} </span>
</div>
@endsection
@section('paypal')
<div class="form-group">
	<label for="is_paypal_enabled"> @lang('admin_messages.is_enabled') <em class="text-danger"> * </em> </label>
	{!! Form::select('is_paypal_enabled',$yes_no_array, old('is_paypal_enabled',credentials('is_enabled','Paypal')), ['class' => 'form-select', 'id' => 'is_paypal_enabled']) !!}
	<span class="text-danger"> {{ $errors->first('is_paypal_enabled') }} </span>
</div>
<div class="form-group">
	<label for="paypal_mode"> @lang('admin_messages.paymode') <em class="text-danger"> * </em> </label>
	{!! Form::select('paypal_mode', array('sandbox' => 'Sandbox', 'live' => 'Live'), old('paypal_mode',credentials('paymode','Paypal')), ['class' => 'form-control', 'id' => 'paypal_mode']) !!}
	<span class="text-danger"> {{ $errors->first('paypal_mode') }} </span>
</div>
<div class="form-group">
	<label for="paypal_client_id"> @lang('admin_messages.client_id') <em class="text-danger"> * </em> </label>
	{!! Form::text('paypal_client_id', old('paypal_client_id',credentials('client_id','Paypal')), ['class' => 'form-control', 'id' => 'paypal_client_id']) !!}
	<span class="text-danger"> {{ $errors->first('paypal_client_id') }} </span>
</div>
<div class="form-group">
	<label for="paypal_secret_key"> @lang('admin_messages.secret_key') <em class="text-danger"> * </em> </label>
	{!! Form::text('paypal_secret_key', old('paypal_secret_key',credentials('secret_key','Paypal')), ['class' => 'form-control', 'id' => 'paypal_secret_key']) !!}
	<span class="text-danger"> {{ $errors->first('paypal_secret_key') }} </span>
</div>
<div class="form-group">
	<label for="paypal_currency_code"> @lang('admin_messages.payment_currency') <em class="text-danger"> * </em> </label>
	{!! Form::select('paypal_currency_code', $payment_currencies, old('paypal_currency_code',credentials('payment_currency','Paypal')), ['class' => 'form-select', 'id' => 'paypal_currency_code']) !!}
	<span class="text-danger"> {{ $errors->first('paypal_currency_code') }} </span>
</div>
@endsection
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.credentials") </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('admin.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#"> @lang("admin_messages.api_credentials") </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => $update_url, 'class' => 'form-horizontal','id'=>'credentials_form','method' => "PUT", 'files' => true]) !!}
				{!! Form::hidden('active_menu',$active_menu) !!}
				{!! Form::hidden('current_tab','',['id' => 'current_tab']) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div id="navigation-pills">
							<div class="title">
								<h3></h3>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-2">
											<ul class="nav nav-pills nav-pills-icons flex-column navigation-links" role="tablist">
												@yield($active_menu)
											</ul>
										</div>
										<div class="col-md-9">
											<div class="tab-content">
												<div class="tab-pane" id="map">
													@yield('map')
												</div>
												<div class="tab-pane" id="google">
													@yield('google')
												</div>
												<div class="tab-pane" id="facebook">
													@yield('facebook')
												</div>
												<div class="tab-pane" id="apple">
													@yield('apple')
												</div>
												<div class="tab-pane" id="cloudinary">
													@yield('cloudinary')
												</div>
												<div class="tab-pane" id="twilio">
													@yield('twilio')
												</div>
												<div class="tab-pane" id="recaptcha">
													@yield('recaptcha')
												</div>
												<div class="tab-pane" id="firebase">
													@yield('firebase')
												</div>
												<div class="tab-pane" id="conveythis">
													@yield('conveythis')
												</div>
												<div class="tab-pane" id="stripe">
													@yield('stripe')
												</div>
												<div class="tab-pane" id="paypal">
													@yield('paypal')
												</div>
												<div class="tab-pane" id="one_pay">
													@yield('one_pay')
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer text-right">
						<div class="col-md-12">
							<button type="submit" class="btn btn-round btn-primary pull-right"> @lang('admin_messages.submit') </button>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		var default_tab = "{{ $active_menu == 'payment_gateways' ? 'stripe' : 'map' }}";
        var current_tab = getParameterByName('current_tab');
        current_tab = current_tab != '' ? current_tab : default_tab;
        $('.navigation-links').find('[href="#'+current_tab+'"]').tab('show');
        $('#'+current_tab).addClass('active show');
        setGetParameter('current_tab',current_tab)
        $('#current_tab').val(current_tab);

        $(document).on('click', '.navigation-links a',function() {
        	current_tab = $(this).attr('href').substring(1);
        	setGetParameter('current_tab',current_tab);
        	$('#current_tab').val(current_tab);
        });
    });
</script>
@endpush