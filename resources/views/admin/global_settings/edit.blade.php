@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
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
					<a href="#"> {{ $sub_title }} </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.global_settings.update'), 'class' => 'form-horizontal','method' => "PUT",'files' => true]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="site_name"> @lang('admin_messages.site_name') <em class="text-danger"> * </em> </label>
							{!! Form::text('site_name', old('site_name',global_settings('site_name')), ['class' => 'form-control', 'id' => 'site_name']) !!}
							<span class="text-danger">{{ $errors->first('site_name') }}</span>
						</div>
						<div class="form-group">
							<label for="version"> @lang('admin_messages.version') <em class="text-danger"> * </em> </label>
							{!! Form::text('version', old('version',global_settings('version')), ['class' => 'form-control', 'id' => 'version']) !!}
							<span class="text-danger">{{ $errors->first('version') }}</span>
						</div>
						<div class="form-group">
							<label for="app_version"> @lang('admin_messages.app_version') <em class="text-danger"> * </em> </label>
							{!! Form::text('app_version', old('app_version',global_settings('app_version')), ['class' => 'form-control', 'id' => 'app_version']) !!}
							<span class="text-danger">{{ $errors->first('app_version') }}</span>
						</div>
						<div class="form-group">
							<label for="force_update"> @lang('admin_messages.force_update_app') <em class="text-danger"> * </em> </label>
							{!! Form::select('force_update', $yes_no_array, old('force_update',global_settings('force_update')), ['class' => 'form-select', 'id' => 'force_update']) !!}
							<span class="text-danger"> {{ $errors->first('force_update') }} </span>
						</div>
						<div class="form-group input-file input-file-image">
							<label for="primary_logo" class="form-label"> @lang('admin_messages.primary_logo') </label>
							<img class="img-upload-preview" src="{{ $site_logo ?? asset('images/logos/logo.png') }}">
							<input type="file" name="primary_logo" class="form-control form-control-file" id="primary_logo" accept="image/*">
							<label for="primary_logo" class="label-input-file btn btn-default btn-round">
								<span class="btn-label"><i class="fa fa-file-image"></i></span>
								@lang('admin_messages.choose_file')
							</label>
							<span class="text-danger d-block">{{ $errors->first('primary_logo') }}</span>
						</div>
						<div class="form-group input-file input-file-image">
							<label for="favicon" class="form-label"> @lang('admin_messages.favicon') </label>
							<img class="img-upload-preview" src="{{ $favicon ?? asset('images/logos/favicon.png') }}">
							<input type="file" name="favicon" class="form-control form-control-file" id="favicon" accept="image/*">
							<label for="favicon" class="label-input-file btn btn-default btn-round">
								<span class="btn-label"><i class="fa fa-file-image"></i></span>
								@lang('admin_messages.choose_file')
							</label>
							<span class="text-danger d-block">{{ $errors->first('favicon') }}</span>
						</div>
						<div class="form-group">
							<label for="maintenance_mode"> @lang('admin_messages.maintenance_mode') <em class="text-danger"> * </em> </label>
							{!! Form::select('maintenance_mode', ['up' => 'No', 'down' => 'Yes'], old('maintenance_mode',$maintenance_mode), ['class' => 'form-select', 'id' => 'maintenance_mode']) !!}
							<span class="text-danger"> {{ $errors->first('maintenance_mode') }} </span>
							@if(global_settings('maintenance_mode_secret') != '')
							<div class="mt-2 h4">
								<span> @lang('admin_messages.maintenance_mode_secret'): </span>
								<span class="mt-2 fw-bold"> {{ global_settings('maintenance_mode_secret') }} </span>
							</div>
							@endif
						</div>
						<div class="form-group">
							<label for="app_maintenance_mode"> @lang('admin_messages.app_maintenance_mode') <em class="text-danger"> * </em> </label>
							{!! Form::select('app_maintenance_mode', $yes_no_array, old('app_maintenance_mode',global_settings('android_app_maintenance_mode')), ['class' => 'form-select', 'id' => 'app_maintenance_mode']) !!}
							<span class="text-danger"> {{ $errors->first('app_maintenance_mode') }} </span>
						</div>
						<div class="form-group">
							<label for="admin_url"> @lang('admin_messages.admin_url') <em class="text-danger"> * </em> </label>
							{!! Form::text('admin_url', old('admin_url',global_settings('admin_url')), ['class' => 'form-control', 'id' => 'admin_url']) !!}
							<span class="text-danger">{{ $errors->first('admin_url') }}</span>
						</div>
						<div class="form-group">
							<label for="host_url"> @lang('admin_messages.host_url') <em class="text-danger"> * </em> </label>
							{!! Form::text('host_url', old('host_url',global_settings('host_url')), ['class' => 'form-control', 'id' => 'host_url']) !!}
							<span class="text-danger">{{ $errors->first('host_url') }}</span>
						</div>
						<div class="form-group">
							<label for="play_store"> @lang('admin_messages.play_store') <em class="text-danger"> * </em> </label>
							{!! Form::text('play_store', old('play_store',global_settings('play_store')), ['class' => 'form-control', 'id' => 'play_store']) !!}
							<span class="text-danger">{{ $errors->first('play_store') }}</span>
						</div>
						<div class="form-group">
							<label for="app_store"> @lang('admin_messages.app_store') <em class="text-danger"> * </em> </label>
							{!! Form::text('app_store', old('app_store',global_settings('app_store')), ['class' => 'form-control', 'id' => 'app_store']) !!}
							<span class="text-danger">{{ $errors->first('app_store') }}</span>
						</div>
						<div class="form-group">
							<label for="auto_payout"> @lang('admin_messages.auto_payout') <em class="text-danger"> * </em> </label>
							{!! Form::select('auto_payout', ['0' => 'No', '1' => 'Yes'], old('auto_payout',global_settings('auto_payout')), ['class' => 'form-select', 'id' => 'auto_payout']) !!}
							<span class="text-danger"> {{ $errors->first('auto_payout') }} </span>
						</div>
						<input type="hidden" name="is_locale_based" value="0">
						{{--
						<div class="form-group">
							<label for="is_locale_based"> @lang('admin_messages.enable_locale_based_url') <em class="text-danger"> * </em> </label>
							{!! Form::select('is_locale_based', $yes_no_array, old('is_locale_based',global_settings('is_locale_based')), ['class' => 'form-select', 'id' => 'is_locale_based']) !!}
							<span class="text-danger"> {{ $errors->first('is_locale_based') }} </span>
						</div>
						--}}
						<div class="form-group">
							<label for="referral_enabled"> @lang('admin_messages.referral_enabled') <em class="text-danger"> * </em> </label>
							{!! Form::select('referral_enabled', $yes_no_array, old('referral_enabled',global_settings('referral_enabled')), ['class' => 'form-select', 'id' => 'referral_enabled']) !!}
							<span class="text-danger"> {{ $errors->first('referral_enabled') }} </span>
						</div>
						<div class="form-group">
							<label for="host_can_add_coupon"> @lang('admin_messages.host_can_add_coupon') <em class="text-danger"> * </em> </label>
							{!! Form::select('host_can_add_coupon', $yes_no_array, old('host_can_add_coupon',global_settings('host_can_add_coupon')), ['class' => 'form-select', 'id' => 'host_can_add_coupon']) !!}
							<span class="text-danger"> {{ $errors->first('host_can_add_coupon') }} </span>
						</div>
						<div class="form-group">
							<label for="upload_driver"> @lang('admin_messages.upload_driver') <em class="text-danger"> * </em> </label>
							{!! Form::select('upload_driver', $upload_drivers, old('upload_driver',global_settings('upload_driver')), ['class' => 'form-select', 'id' => 'upload_driver']) !!}
							<span class="text-danger">
								{{ $errors->first('upload_driver') }}
							</span>
						</div>
						<div class="form-group">
							<label for="support_number"> @lang('admin_messages.support_number') <em class="text-danger"> * </em> </label>
							{!! Form::text('support_number', old('support_number',global_settings('support_number')), ['class' => 'form-control', 'id' => 'support_number']) !!}
							<span class="text-danger">{{ $errors->first('support_number') }}</span>
						</div>
						<div class="form-group">
							<label for="support_email"> @lang('admin_messages.support_email') *</label>
							{!! Form::text('support_email', old('support_email',global_settings('support_email')), ['class' => 'form-control', 'id' => 'support_email']) !!}
							<span class="text-danger">{{ $errors->first('support_email') }}</span>
						</div>
						<div class="form-group">
							<label for="default_currency"> @lang('admin_messages.default_currency') <em class="text-danger"> * </em> </label>
							{!! Form::select('default_currency', $currency_list, old('default_currency',global_settings('default_currency')), ['class' => 'form-select', 'id' => 'default_currency','data-default_currency'=>global_settings("default_currency")]) !!}
							<span class="text-danger">{{ $errors->first('default_currency') }}</span>
						</div>
						<div class="form-group">
							<label for="min_price"> @lang('admin_messages.min_price') <em class="text-danger"> * </em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('min_price', old('min_price',global_settings('min_price')), ['class' => 'form-control', 'id' => 'min_price']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('min_price') }}</span>
						</div>
						<div class="form-group">
							<label for="max_price"> @lang('admin_messages.max_price') <em class="text-danger"> * </em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('max_price', old('max_price',global_settings('max_price')), ['class' => 'form-control', 'id' => 'max_price']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('max_price') }}</span>
						</div>
						<input type="hidden" name="default_language" value="en">
						{{--
						<div class="form-group">
							<label for="default_language"> @lang('admin_messages.default_language') <em class="text-danger"> * </em> </label>
							{!! Form::select('default_language', $language_list, old('default_language',global_settings('default_language')), ['class' => 'form-select', 'id' => 'default_language']) !!}
							<span class="text-danger">
								{{ $errors->first('default_language') }}
							</span>
						</div>
						--}}
						<div class="form-group">
							<label for="date_format"> @lang('admin_messages.date_format') <em class="text-danger"> * </em> </label>
							{!! Form::select('date_format', $date_formats, old('date_format',global_settings('date_format')), ['class' => 'form-select', 'id' => 'date_format']) !!}
							<span class="text-danger">
								{{ $errors->first('date_format') }}
							</span>
						</div>
						<div class="form-group">
							<label for="timezone"> @lang('admin_messages.timezone') <em class="text-danger"> * </em> </label>
							{!! Form::select('timezone', $timezones, old('timezone',global_settings('timezone')), ['class' => 'form-select', 'id' => 'timezone']) !!}
							<span class="text-danger">
								{{ $errors->first('timezone') }}
							</span>
						</div>
						<div class="form-group">
							<label for="default_user_status"> @lang('admin_messages.default_user_status') <em class="text-danger"> * </em> </label>
							{!! Form::select('default_user_status',array('pending' => Lang::get('admin_messages.pending'),'active' => Lang::get('admin_messages.active'), 'inactive' => Lang::get('admin_messages.inactive')) , old('default_user_status',global_settings('default_user_status')), ['class' => 'form-select', 'id' => 'default_user_status']) !!}
							<span class="text-danger">
								{{ $errors->first('default_user_status') }}
							</span>
						</div>
						<div class="form-group">
							<label for="default_listing_status"> @lang('admin_messages.default_listing_status') <em class="text-danger"> * </em> </label>
							{!! Form::select('default_listing_status',array('pending' => Lang::get('admin_messages.pending'),'approved' => Lang::get('admin_messages.approved')) , old('default_listing_status',global_settings('default_listing_status')), ['class' => 'form-select', 'id' => 'default_listing_status']) !!}
							<span class="text-danger">
								{{ $errors->first('default_listing_status') }}
							</span>
						</div>
						<div class="form-group">
							<label for="backup_period"> @lang('admin_messages.how_many_days_store_database') <em class="text-danger"> * </em> </label>
							{!! Form::select('backup_period',$backup_period_array,old('backup_period',global_settings('backup_period')), ['class' => 'form-select', 'id' => 'backup_period']) !!}
							<span class="text-danger"> {{ $errors->first('backup_period') }} </span>
						</div>
						<div class="form-group">
							<label for="user_inactive_days"> @lang('admin_messages.user_inactive_days') <em class="text-danger"> * </em> </label>
							{!! Form::text('user_inactive_days', old('user_inactive_days',global_settings('user_inactive_days')), ['class' => 'form-control', 'id' => 'user_inactive_days']) !!}
							<span class="text-danger">{{ $errors->first('user_inactive_days') }}</span>
						</div>
						<div class="form-group">
							<label for="copyright_link"> @lang('admin_messages.copyright_link') <em class="text-danger"> * </em> </label>
							{!! Form::text('copyright_link', old('copyright_link',global_settings('copyright_link')), ['class' => 'form-control', 'id' => 'copyright_link']) !!}
							<span class="text-danger">{{ $errors->first('copyright_link') }}</span>
						</div>
						<div class="form-group">
							<label for="copyright_text"> @lang('admin_messages.copyright_text') <em class="text-danger"> * </em> </label>
							{!! Form::text('copyright_text', old('copyright_text',global_settings('copyright_text')), ['class' => 'form-control', 'id' => 'copyright_text']) !!}
							<span class="text-danger">{{ $errors->first('copyright_text') }}</span>
						</div>
						<div class="form-group">
							<label for="head_code"> @lang('admin_messages.head_code') <span class="d-block h6"> @lang('admin_messages.head_code_desc') </span> </label>
							{!! Form::textarea('head_code', old('head_code',global_settings('head_code')), ['rows' => 3, 'class' => 'form-control', 'id' => 'head_code']) !!}
							<span class="text-danger">
								{{ $errors->first('head_code') }}
							</span>
						</div>
						<div class="form-group">
							<label for="foot_code"> @lang('admin_messages.foot_code') <span class="d-block h6"> @lang('admin_messages.foot_code_desc') </span> </label>
							{!! Form::textarea('foot_code', old('foot_code',global_settings('foot_code')), ['rows' => 3, 'class' => 'form-control', 'id' => 'foot_code']) !!}
							<span class="text-danger">
								{{ $errors->first('foot_code') }}
							</span>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.global_settings') }}" class="btn btn-danger"> @lang('admin_messages.cancel') </a>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection