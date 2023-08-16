@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="col-lg-9 col-md-10 col-sm-10 mx-auto  pt-4 px-3">
		<div class="col-12 mb-4">
			<h2 class="text-dark-gray fw-bold"> @lang('messages.account_settings') </h2>
			<h6 class="d-block">
			<span class="strong fw-bold"> {{ $user->full_name }} </span>, <span> {{ $user->email }} </span>
			<a href="{{ resolveRoute('view_profile',['id' => $user->id]) }}"> @lang('messages.go_to_profile') </a>
			</h6>
		</div>
		<div class="row justify-content-center g-2 g-md-4">
			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-description icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.personal_information')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.personal_info_desc')
						</p>
					</div>
				</a>
			</div>
			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-add-photo icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.profile_photos')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.profile_photos_desc')
						</p>
					</div>
				</a>
			</div>

			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'login-and-security']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-security icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.login_and_security')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.login_and_security_desc')
						</p>
					</div>
				</a>
			</div>

			{{--
			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'payment-payouts']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-payout icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.payment_payouts')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.payment_payouts_desc')
						</p>
					</div>
				</a>
			</div>
			--}}
			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'site-setting']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-preference icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.global_preference')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.global_preference_desc')
						</p>
					</div>
				</a>
			</div>

			<div class="col-md-4 col-sm-6">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'transactions']) }}" class="force-common-link card shadow-card py-4">
					<i class="icon icon-receipt icon-large icon-outlined ms-4 mt-2" area-hidden="true"></i>
					<div class="card-body">
						<h4 class="text-gray card-title">
							@lang('messages.transaction_history')
							<i class="icon icon-arrow-right" area-hidden="true"></i>
						</h4>
						<p class="card-text text-muted fw-normal">
							@lang('messages.transaction_history_desc')
						</p>
					</div>
				</a>
			</div>
		</div>
	</div>
</main>
@endsection