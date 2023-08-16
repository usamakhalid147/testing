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
				{!! Form::open(['url' => route('admin.referral_settings.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="per_user_limit"> @lang('admin_messages.per_user_limit') <em class="text-danger"> * </em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('per_user_limit', old('per_user_limit',referral_settings('per_user_limit')), ['class' => 'form-control', 'id' => 'per_user_limit']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('per_user_limit') }} </span>
						</div>
						<div class="form-group">
							<label for="user_become_guest_credit"> @lang('admin_messages.user_become_guest_credit') <em class="text-danger"> * </em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('user_become_guest_credit', old('user_become_guest_credit',referral_settings('user_become_guest_credit')), ['class' => 'form-control', 'id' => 'user_become_guest_credit']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('user_become_guest_credit') }} </span>
						</div>
						<div class="form-group">
							<label for="new_referral_credit"> @lang('admin_messages.new_referral_credit') <em class="text-danger"> * </em> </label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"> {{ global_settings('default_currency') }} </span>
								</div>
								{!! Form::text('new_referral_credit', old('new_referral_credit',referral_settings('new_referral_credit')), ['class' => 'form-control', 'id' => 'new_referral_credit']) !!}
							</div>
							<span class="text-danger">{{ $errors->first('new_referral_credit') }} </span>
						</div>
					</div>
					<div class="card-action">
						<button type="reset" class="btn btn-danger"> @lang('admin_messages.reset') </button>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection