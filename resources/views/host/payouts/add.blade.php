@extends('layouts.hostLayout.app')

@section('methods')
<div class="row">
	<div class="row">
		<div class="form-group col-md-6">
			<label> @lang('messages.billing_country') </label>
			{!! Form::select('country_code',$country_list,'',['class'=>'form-select','v-model' => 'payout_country','placeholder' => Lang::get('messages.select')]) !!}
			<span class="text-danger" v-show="error_messages.payout_country"> @lang('validation.required',['attribute' => Lang::get('messages.billing_country')]) </span>
		</div>
	</div>
	@foreach(PAYOUT_METHODS as $method)
	<div class="row">
		<div class="radio">	
			<label>
				<input type="radio" name="payout_method" id="payout_method_{{ $method['key'] }}" class="form-check-input" v-model="payout_method" value="{{ $method['key'] }}"> {{ $method['value'] }}
			</label>
		</div>
	</div>
	@endforeach
</div>
@endsection

@section('address_form')
	<label class="fw-bolder" v-show="payout_country == 'JP' && payout_method == 'stripe'"> Address Kana: </label>
	<div class="form-group">
		<label for="address1"> @lang('messages.address1') </label>
		{!! Form::text('address1', null, ['id' => 'address1','class' => 'form-control','v-model'=>"address1", 'placeholder' => Lang::get('messages.address1')]) !!}
		<span class="text-danger" v-show="error_messages.address1"> @lang('validation.required',['attribute' => Lang::get('messages.address1')]) </span>
	</div>
	<div class="form-group">
		<label for="address2"> @lang('messages.address2') </label>
		{!! Form::text('address2', null, ['id' => 'address2','class' => 'form-control','v-model'=>"address2", 'placeholder' => Lang::get('messages.address2')]) !!}
	</div>
	<div class="form-group">
		<label for="city"> @lang('messages.city') </label>
		{!! Form::text('city', null, ['id' => 'city','class' => 'form-control','v-model'=>"city", 'placeholder' => Lang::get('messages.city')]) !!}
		<span class="text-danger" v-show="error_messages.city"> @lang('validation.required',['attribute' => Lang::get('messages.city')]) </span>
	</div>
	<div class="form-group">
		<label for="state"> @lang('messages.state') / @lang('messages.province') </label>
		{!! Form::text('state', null, ['id' => 'state','class' => 'form-control','v-model'=>"state", 'placeholder' => Lang::get('messages.state')]) !!}
		<span class="text-danger" v-show="error_messages.state"> @lang('validation.required',['attribute' => Lang::get('messages.state')]) </span>
	</div>
	<div class="form-group">
		<label for="postal_code"> @lang('messages.postal_code') </label>
		{!! Form::text('postal_code', null, ['id' => 'postal_code','class' => 'form-control','v-model' => 'postal_code', 'placeholder' => Lang::get('messages.postal_code')]) !!}
		<span class="text-danger" v-show="error_messages.postal_code"> @lang('validation.required',['attribute' => Lang::get('messages.postal_code')]) </span>
	</div>
@endsection

@section('payout_methods')
	<div v-show="payout_method == 'paypal'">
		<div class="form-group">
			<label for="paypal_email"> @lang('messages.paypal_email') </label>
			{!! Form::text('paypal_email', null, ['id' => 'paypal_email','class' => 'form-control','v-model' => 'paypal_email', 'placeholder' => Lang::get('messages.paypal_email')]) !!}
			<span class="text-danger" v-show="paypal_email == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.paypal_email')]) </span>
		</div>
	</div>

	<div v-show="payout_method == 'bank_transfer'">
		<div class="form-group">
			<label for="holder_name"> @lang('messages.holder_name') </label>
			{!! Form::text('bank_holder_name', null, ['id' => 'holder_name','class' => 'form-control','v-model' => 'bank_holder_name', 'placeholder' => Lang::get('messages.holder_name')]) !!}
			<span class="text-danger" v-show="bank_holder_name == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.holder_name')]) </span>
		</div>
		<div class="form-group">
			<label for="account_number"> @lang('messages.account_number') </label>
			{!! Form::text('bank_account_number', null, ['id' => 'account_number','class' => 'form-control','v-model' => 'bank_account_number', 'placeholder' => Lang::get('messages.account_number')]) !!}
			<span class="text-danger" v-show="bank_account_number == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.account_number')]) </span>
		</div>
		<div class="form-group">
			<label for="bank_name"> @lang('messages.bank_name') </label>
			{!! Form::text('bank_name', null, ['id' => 'bank_name','class' => 'form-control','v-model' => 'bank_name', 'placeholder' => Lang::get('messages.bank_name')]) !!}
			<span class="text-danger" v-show="bank_name == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.bank_name')]) </span>
		</div>
		<div class="form-group">
			<label for="bank_location"> @lang('messages.bank_location') </label>
			{!! Form::text('bank_location', null, ['id' => 'bank_location','class' => 'form-control','v-model' => 'bank_location', 'placeholder' => Lang::get('messages.bank_location')]) !!}
			<span class="text-danger" v-show="bank_location == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.bank_location')]) </span>
		</div>
		<div class="form-group">
			<label for="bank_code"> @lang('messages.bank_code') </label>
			{!! Form::text('bank_code', null, ['id' => 'bank_code','class' => 'form-control','v-model' => 'bank_code', 'placeholder' => Lang::get('messages.bank_code')]) !!}
			<span class="text-danger" v-show="bank_code == '' && displayFormThreeError"> @lang('validation.required',['attribute' => Lang::get('messages.bank_code')]) </span>
		</div>
	</div>

	<div v-show="payout_method == 'stripe'" v-if="payout_country != ''">
		{!! Form::hidden("stripe_token",null,['id' => "stripe_token"]) !!}
		{!! Form::hidden("is_iban",'',['id' => "is_iban", ':value' => "iban_req_countries.includes(payout_country) ? 'Yes' : 'No'"]) !!}
		{!! Form::hidden("is_branch_code",'',['id' => "is_branch_code", ':value' => "branch_code_req_countries.includes(payout_country) ? 'Yes' : 'No'"]) !!}
		<!-- Payout Currency -->
		<div class="form-group">
			<label for="payout_currency">@lang('messages.payout_currency')</label>
			<select name="payout_currency" id="payout_currency" class="form-select" v-model="payout_currency" placeholder="@lang('messages.payout_currency')">
				<option value=""> @lang('messages.select') </option>
				<option :value="currency" v-for="currency in payout_currency_list[payout_country]"> @{{ currency }} </option>
			</select>
			<span class="text-danger"> {{ $errors->first('payout_currency') }} </span>
		</div>
		<!-- Payout Currency -->
		<!-- Bank Name -->
		<div class="form-group" v-if="mandatory_fields[payout_country]" v-show="mandatory_fields[payout_country][3]">
			<label class="form-label" for="bank_name"> @{{ mandatory_fields[payout_country][3] }} </label>
			{!! Form::text('bank_name', null, ['id' => 'bank_name', 'class' => 'form-control', 'v-model' => 'bank_name']) !!}
			<span class="text-danger"> {{ $errors->first('bank_name') }} </span>
		</div>
		<!-- Bank Name -->
		<!-- Branch Name -->
		<div class="form-group" v-if="mandatory_fields[payout_country]" v-show="mandatory_fields[payout_country][4]">
			<label class="form-label" for="bank_name">@{{ mandatory_fields[payout_country][4] }} </label>
			{!! Form::text('branch_name', null, ['id' => 'branch_name', 'class' => 'form-control', 'v-model' => 'branch_name']) !!}
			<span class="text-danger"> {{ $errors->first('branch_name') }} </span>
		</div>
		<!-- Branch Name -->
		<!-- Routing number -->
		<div class="form-group" v-if="mandatory_fields[payout_country]" v-show="!iban_req_countries.includes(payout_country)">
			<label class="form-label" for="routing_number">@{{ mandatory_fields[payout_country][0] }} </label>
			{!! Form::text('routing_number', null, ['id' => 'routing_number', 'class' => 'form-control', 'v-model' => 'routing_number']) !!}
			<span class="text-danger"> {{ $errors->first('routing_number') }} </span>
		</div>
		<!-- Routing number -->
		<!-- Branch code -->
		<div class="form-group" v-if="mandatory_fields[payout_country]" v-show="mandatory_fields[payout_country][2]">
			<label class="form-label" for="branch_code">@{{ mandatory_fields[payout_country][2] }} </label>
			{!! Form::text('branch_code', null, ['id' => 'branch_code', 'class' => 'form-control', 'v-model' => 'branch_code']) !!}
			<span class="text-danger"> {{ $errors->first('branch_code') }} </span>
		</div>
		<!-- Branch code -->
		<!-- Account Number -->
		<div class="form-group" v-if="mandatory_fields[payout_country]">
			<label class="form-label" for="account_number" v-show="!iban_req_countries.includes(payout_country)"> @{{ mandatory_fields[payout_country][1] }} </label>
			<label class="form-label" for="account_number" v-show="iban_req_countries.includes(payout_country)"> @lang('messages.iban_number') </label>
			{!! Form::text('account_number', null, ['id' => 'account_number', 'class' => 'form-control', 'v-model' => 'account_number']) !!}
			<span class="text-danger"> {{ $errors->first('account_number') }} </span>
		</div>
		<!-- Account Number -->
		<!-- Account Holder name -->
		<div class="form-group" v-if="mandatory_fields[payout_country]">
			<label class="form-label" for="holder_name" v-show="payout_country == 'JP'"> @{{ mandatory_fields[payout_country][5] }} </label>
			<label class="form-label" for="holder_name" v-show="payout_country != 'JP'"> @lang('messages.holder_name') </label>
			{!! Form::text('holder_name', null, ['id' => 'holder_name', 'class' => 'form-control', 'v-model' => 'holder_name']) !!}
			<span class="text-danger"> {{ $errors->first('holder_name') }} </span>
		</div>
		<!-- Account Holder name -->
		<!-- SSN Last 4 only for US -->
		<div class="form-group" v-show="payout_country == 'US'">
			<label class="form-label" for="ssn_last_4"> @lang('messages.ssn_last_4') </label>
			{!! Form::text('ssn_last_4', null, ['id' => 'ssn_last_4', 'class' => 'form-control', 'maxlength' => '4', 'v-model' => 'ssn_last_4']) !!}
			<span class="text-danger"> {{ $errors->first('ssn_last_4') }} </span>
		</div>
		<!-- SSN Last 4 only for US -->
		<!-- Phone number -->
		<div class="form-group">
			<label class="form-label" for="phone_number" > @lang('admin_messages.phone_number') </label>
			{!! Form::text('phone_number', null, ['id' => 'phone_number', 'class' => 'form-control', 'v-model' => 'phone_number']) !!}
			<span class="text-danger"> {{ $errors->first('phone_number') }} </span>
		</div>
		<!-- Phone number -->

		<!-- Address Kanji Only for Japan -->
		<div class="form-row" v-if="payout_country == 'JP'">
			<label class="fw-bolder"> Address Kanji: </label>
			<div class="form-group">
				<label class="form-label" for="kanji_address1"> @lang('messages.address1') </label>
				{!! Form::text('kanji_address1', null, ['id' => 'kanji_address1', 'class' => 'form-control', 'v-model' => 'kanji_address1']) !!}
				<span class="text-danger"> {{ $errors->first('kanji_address1') }} </span>
			</div>
			<div class="form-group">
				<label class="form-label" for="kanji_address2"> @lang('messages.address2') </label>
				{!! Form::text('kanji_address2', null, ['id' => 'kanji_address2', 'class' => 'form-control', 'v-model' => 'kanji_address2']) !!}
				<span class="text-danger"> {{ $errors->first('kanji_address2') }} </span>
			</div>
			<div>
				<label class="form-label" for="kanji_city"> @lang('messages.city') </label>
				{!! Form::text('kanji_city', null, ['id' => 'kanji_city', 'class' => 'form-control', 'v-model' => 'kanji_city']) !!}
				<span class="text-danger"> {{ $errors->first('kanji_city') }} </span>
			</div>
			<div>
				<label class="form-label" for="payout_info_payout_state"> @lang('messages.state') / @lang('messages.province') </label>
				{!! Form::text('kanji_state', null, ['id' => 'kanji_state', 'class' => 'form-control', 'v-model' => 'kanji_state']) !!}
				<span class="text-danger"> {{ $errors->first('kanji_state') }} </span>
			</div>
			<div>
				<label class="form-label" for="kanji_postal_code"> @lang('messages.postal_code') </label>
				{!! Form::text('kanji_postal_code', null, ['id' => 'kanji_postal_code', 'class' => 'form-control', 'v-model' => 'kanji_postal_code']) !!}
				<span class="text-danger"> {{ $errors->first('kanji_postal_code') }} </span>
			</div>
		</div>
		<!-- Address Kanji Only for Japan -->
		<!-- Legal document -->
		<div class="mx-1 form-group mb-3">
			<label class="form-label" for="legal_document">@lang('messages.legal_document') </label>
			{!! Form::file('legal_document', ['id' => 'legal_document', 'class' => 'form-control', 'accept' => "images/*"]) !!}
		</div>
		<!-- Legal document -->
		<!-- Additional document -->
		<div class="mx-1 form-group mb-3">
			<label class="form-label" for="additional_document"> @lang('messages.additional_document') </label>
			{!! Form::file('additional_document', ['id' => 'additional_document', 'class' => 'form-control', 'accept' => "images/*"]) !!}
			<span class="text-danger"> {{ $errors->first('document') }} </span>
		</div>
		<!-- Additional document -->
		<label class="text-danger" v-show="stripe_error_message != ''"> @{{ stripe_error_message }} </label>
	</div>
@endsection

@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('host.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('host.payout_methods') }}">@lang("admin_messages.payout_methods")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.add")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('host.payout_methods.store'), 'class' => 'form-horizontal','id'=>'createPayoutForm','files' => "true"]) !!}
				<div class="px-4 mt-4 card">
					<div class="card-header mt-4">
						<p class="fw-bold h4"> @lang('admin_messages.payouts') </p>
						<p class="pt-1 text-gray f_16"> @lang('admin_messages.payouts_desc') </p>
					</div>
					<div class="card-body px-3"  :class="{ 'loading' : isLoading }">
						<div v-show="currentStep == 1">
							@yield('methods')
						</div>
						<div v-show="currentStep == 2">
							@yield('address_form')
						</div>
						<div v-show="currentStep == 3">
							@yield('payout_methods')
						</div>
						<div class="form-row mt-4">
							<button type="button" class="btn btn-default" v-on:click="prevStep()" v-show="currentStep != 1">
							@lang('messages.back')
							</button>
							<button type="button" class="btn btn-primary float-end" v-on:click="nextStep()">
							@lang('messages.next')
							</button>
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
		window.vueInitData = {!! json_encode([
			'payout_method' => $payout_method,
			'payout_country_list' => $payout_country_list,
			'payout_currency_list' => $payout_currency_list,
			'iban_req_countries' => $iban_req_countries,
			'branch_code_req_countries' => $branch_code_req_countries,
			'mandatory_fields' => $mandatory_fields,
			'translation_messages' => [
				'please_fill_all_required_fields' => Lang::get('messages.please_fill_all_required_fields'),
			],
			'stripe_account_type' => credentials('account_type','Stripe'),
	    ]) !!};
	</script>
	<script src="https://js.stripe.com/v3/"></script>
@endpush