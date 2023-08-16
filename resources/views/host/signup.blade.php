@extends('layouts.app')
@section('content')
<main id="site-content" role="main" class="signup-top">
	<div class="container py-3 py-3-md-5 login-signup">
		<div class="m-md-3 rounded-3 overflow-hidden signup_main">
			<div class="row mx-0">
				<div class="col-md-5 px-0 position-relative">
					<div class="hstack justify-content-center h-100 bg-secondary">
						<img src="{{ asset('images/login.svg') }}" class="img-fluid logBanner">
					</div>
					<a class="navbar-brand position-absolute top-0 start-0 res-logo" href="{{ resolveRoute('home') }}">
					    <img src="{{ $site_logo }}" class="header-logo">
					</a>
				</div>
				<div class="col-md-7 px-md-0 bg-white">
					<div id="conveythis-language" class="float-right"></div>
					<div class="col-md-11 mx-auto py-4">
						<div class="modal-dialog modal-dialog-centered p-3 rounded-4 signup_email">
							<div class="modal-content">
								<div class="modal-body" :class="{ 'loading' : isLoading }">
									<div class="form">
										{!! Form::open(['url' => route('host.create_host'), 'class' => '','id'=>'owner_signup_form', 'files' => true]) !!}
										<div class="profile-info slide-form active" id="profile" v-show="tab == 'profile'">
											<div class="hstack justify-content-between flex-wrap mb-4">
												<h2 class="text-center mb-0 fw-normal"> @lang('messages.manager_details') </h2>
												<div class="fs-6">
													{{--
													<p class="m-0 text-muted">
														@lang('messages.already_have_account_host')
													</p>
													--}}
													<a href="{{ route('host.login') }}" class="text-decoration-underline text-secondary fw-500">
														@lang('messages.login')
													</a>
												</div>
											</div>
											<div class="row g-3">
												<div class="form-floating col-12" :class="{'required': user.full_name == '' || user.full_name == undefined}">
													<input type="text" name="full_name" class="form-control" v-model="user.full_name">
													<label> @lang('messages.full_name') <em class="text-danger"> *</em></label>
													<span class="text-danger"> @{{ (error_messages.full_name) ? error_messages.full_name[0] : '' }} </span>
												</div>
												<div class="form-floating col-6" :class="{'required': user.manager_title == '' || user.manager_title == undefined}">
													<input type="text" name="manager_title" class="form-control" v-model="user.manager_title">
													<label for="manager_title"> @lang('admin_messages.manager') @lang('admin_messages.title') <em class="text-danger"> *</em></label>
													<span class="text-danger"> @{{ (error_messages.manager_title) ? error_messages.manager_title[0] : '' }} </span>
												</div>
												<div class="form-floating col-6" :class="{'required': user.email == '' || user.email == undefined}">
													<input type="email" name="email" class="form-control" id="owner_signup_email" v-model="user.email">
													<label for="owner_signup_email"> @lang('admin_messages.manager') @lang('messages.email') <em class="text-danger"> *</em></label>
													<span class="text-danger"> @{{ (error_messages.email) ? error_messages.email[0] : '' }} </span>
												</div>
												<div class="col-6">
													<div class="password-with-toggler input-group floating-input-group">
														<div style="margin-bottom:0px !important;" class="form-floating flex-grow-1" :class="{'required': user.password == '' || user.password == undefined}">
															<input type="password" name="password" class="password form-control" v-model="user.password">
															<label> @lang('messages.password') <em class="text-danger"> *</em></label>
														</div>
														<span class="input-group-text"><i class="icon icon-eye cursor-pointer toggle-password active" area-hidden="true"></i></span>
													</div>
													<span class="text-danger"> @{{ (error_messages.password) ? error_messages.password[0] : '' }} </span>
												</div>
												<div class="col-6">
													<div class="password-with-toggler input-group floating-input-group">
														<div style="margin-bottom:0px !important;" class="form-floating flex-grow-1" :class="{'required': user.password_confirmation == '' || user.password_confirmation == undefined}">
															<input type="password" name="password_confirmation" id="host_password_confirmation" class="password form-control" v-model="user.password_confirmation">
															<label for="password_confirmation"> @lang('messages.confirm_password') <em class="text-danger"> *</em></label>
														</div>
														<span class="input-group-text"><i class="icon icon-eye cursor-pointer toggle-password active" area-hidden="true"></i></span>
													</div>
													<span class="text-danger"> @{{ (error_messages.password_confirmation) ? error_messages.password_confirmation[0] : '' }} </span>
												</div>
												<div class="col-12 mt-0">
													<small class="text-muted float-start">Password must be at least 8 characters, 1 uppercase letter, 1 lowercase letter, & 1 number</small>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="telephone_number" class="form-control" v-model="user.telephone_number">
													<label for="telephone_number"> @lang('admin_messages.telephone_number') </label>
												</div>
												<div class="form-floating col-6" :class="{'required': user.phone_number == '' || user.phone_number == undefined}">
													<input type="text" name="phone_number" class="form-control" v-model="user.phone_number">
													<label for="phone_number"> @lang('messages.manager_mobile_number') <em class="text-danger"> *</em></label>
													<span class="text-danger"> @{{ (error_messages.phone_number) ? error_messages.phone_number[0] : '' }} </span>
												</div>
												<div class="form-floating col-6" :class="{'required': user.country == '' || user.country == undefined}">
													<select name="country" class="form-select" v-model="user.country" v-on:change="user.city = ''">
														<option value="">@lang('messages.select')</option>
														<option :value="country.name" v-for="country in countries">@{{country.full_name}} (+@{{country.phone_code}})</option>
													</select>
													<label for="country"> @lang('messages.country') <em class="text-danger"> *</em> </label>
													<span class="text-danger"> @{{ (error_messages.country) ? error_messages.country[0] : '' }} </span>
												</div>
												<div class="form-floating col-6" :class="{'required': user.city == '' || user.city == undefined}">
													<select name="city" class="form-select" v-model="user.city">
														<option value="">@lang('messages.select')</option>
														<option :value="city.name" v-for="city in cities" v-show="city.country == user.country">@{{city.name}}</option>
													</select>
													<label for="city"> @lang('messages.city_desc') <em class="text-danger"> *</em></label>
													<span class="text-danger"> @{{ (error_messages.city) ? error_messages.city[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="dob" class="form-control" id="dob" v-model="user.dob">
													<label class="form-label"> @lang('messages.date_of_birth') </label>
													<span class="text-danger"> @{{ (error_messages.dob) ? error_messages.dob[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<select name="gender" class="form-select" v-model="user.gender">
														<option value="">@lang('messages.select')</option>
														<option value="Male"> @lang('messages.male') </option>
														<option value="Female"> @lang('messages.female') </option>
														<option value="Others"> @lang('messages.others') </option>
													</select>
													<label class="form-label"> @lang('messages.gender') </label>
													<span class="text-danger"> @{{ (error_messages.gender) ? error_messages.gender[0] : '' }} </span>
												</div>
												<div class="col-4 ms-auto">
													<button type="button" class="btn btn-primary d-flex w-100 justify-content-center" :disabled="checkUserButtonIsDisabled()" v-on:click="validateTab('company');">
														@lang('messages.next')
													</button>
												</div>
											</div>
										</div>
										<div class="company-info slide-form" id="company" v-show="tab == 'company'">
											<div class="hstack justify-content-between">
												<a href="javascript:;" v-on:click="nextTab('profile')" class="text-decoration-underline text-secondary fw-500">
													@lang('messages.prev')
												</a>
												<h2 class="text-center fw-normal"> @lang('messages.company_details') </h2>
												<a href="javascript:;" class="text-decoration-underline text-secondary fw-500"></a>
											</div>
											<div class="row g-2">
												<div class="form-group input-file input-file-image col-12">
													<label for="email" class="text-start w-100 mb-2 fs-3">
														@lang('messages.logo')
													</label>
													<input type="file" class="form-control form-control-file" id="image" name="logo" accept="image/*">
													<div class="col-12 mt-0">
														<small class="text-muted float-start">Max upload size for logo image is 1mb</small>
													</div>
													<span class="text-danger"> @{{ (error_messages.logo) ? error_messages.logo[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="company_name" class="form-control" v-model="user.company_name">
													<label for="company_name"> @lang('messages.company_name') </label>
													<span class="text-danger"> @{{ (error_messages.company_name) ? error_messages.company_name[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="company_tax_number" class="form-control" v-model="user.company_tax_number">
													<label for="company_tax_number"> @lang('messages.company_tax_number') </label>
													<span class="text-danger"> @{{ (error_messages.company_tax_number) ? error_messages.company_tax_number[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="company_tele_phone_number" class="form-control" v-model="user.company_tele_phone_number">
													<label for="company_tele_phone_number"> @lang('messages.company_tele_phone_number') </label>
													<span class="text-danger"> @{{ (error_messages.company_tele_phone_number) ? error_messages.company_tele_phone_number[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="company_fax_number" class="form-control" v-model="user.company_fax_number">
													<label for="company_fax_number"> @lang('messages.company_fax_number') </label>
													<span class="text-danger"> @{{ (error_messages.company_fax_number) ? error_messages.company_fax_number[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="address_line1" class="form-control" v-model="user.address_line1" >
													<label for="address_line1"> @lang('messages.company_address') </label>
													<span class="text-danger"> @{{ (error_messages.address_line1) ? error_messages.address_line1[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text"class="form-control" name="address_line2" v-model="user.address_line2" >
													<label for="address_line2"> @lang('messages.ward') </label>
													<span class="text-danger"> @{{ (error_messages.address_line2) ? error_messages.address_line2[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" name="company_state" class="form-control" v-model="user.company_state">
													<label for="state"> @lang('messages.state_desc') </label>
													<span class="text-danger"> @{{ (error_messages.company_state) ? error_messages.company_state[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<select name="company_country" class="form-control" v-model="user.company_country" v-on:change="user.company_city = ''">
														<option value="">@lang('messages.select')</option>
														<option :value="country.name" v-for="country in countries">@{{country.full_name}} (+@{{country.phone_code}})</option>
													</select>
													<label for="country"> @lang('messages.country') </label>
													<span class="text-danger"> @{{ (error_messages.company_country) ? error_messages.company_country[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<select name="company_city" class="form-control" v-model="user.company_city">
														<option value="">@lang('messages.select')</option>
														<option :value="city.name" v-for="city in cities" v-show="city.country == user.company_country">@{{city.name}}</option>
													</select>
													<label for="city"> @lang('messages.city_desc') </label>
													<span class="text-danger"> @{{ (error_messages.company_city) ? error_messages.company_city[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" class="form-control" name="company_pincode" v-model="user.company_pincode" >
													<label for="pincode"> @lang('messages.pincode') </label>
													<span class="text-danger"> @{{ (error_messages.company_pincode) ? error_messages.company_pincode[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" class="form-control" name="company_website" v-model="user.company_website" >
													<label for="website"> @lang('messages.company_website') </label>
													<span class="text-danger"> @{{ (error_messages.company_website) ? error_messages.company_website[0] : '' }} </span>
												</div>
												<div class="form-floating col-6">
													<input type="text" class="form-control" name="company_email" v-model="user.company_email" >
													<label for="email"> @lang('messages.company_email') </label>
													<span class="text-danger"> @{{ (error_messages.company_email) ? error_messages.company_email[0] : '' }} </span>
												</div>
												<label for="owner_agree_tac" class="form-check-label fs-7 text-start">
													@lang('messages.signup_agree')
												</label>
												@foreach($agree_pages as $page)
													<div class="form-check mt-0 col-12">
														<input
															type="checkbox"
															name="agree_page[]"
															class="form-check-input"
															id="{{ $page->id }}"
															value="{{ $page->id }}"
															v-model="{{ Illuminate\Support\Str::slug($page->slug, '_') }}"
														>
														<label for="{{ $page->id }}" class="text-start float-start form-check-label fs-6">
															<a href="{{ $page->url }}" target="_blank" class="primary-link"> {{ $page->name }} </a>
														</label>
													</div>
													@if (!$loop->last)
															<br>
													@endif
												@endforeach
												<div class="col-12">
													@if(checkEnabled('ReCaptcha') && credentials('version','ReCaptcha') == '2')
													<div class="col-12">
														<div class="recaptcha-container mt-2">
															<div class="g-recaptcha" data-sitekey="{{ credentials('site_key','ReCaptcha') }}"></div>
														</div>
														@if($errors->has('g-recaptcha-response'))
															<span class="text-danger"> {{ $errors->first('g-recaptcha-response') }} </span>
														@endif
													</div>
													@endif
												</div>
												<div class="form-group col-12">
													<span class="text-danger"> @lang('messages.company_details_entry_warning') </span>
												</div>
												<div class="col-12">
													<div class="form-group col-4 ms-auto">
														<button type="button" id="signup" class="btn btn-primary d-flex w-100 justify-content-center" :disabled="checkCompanyButtonIsDisabled()" v-on:click="nextStep();">
															@lang('messages.signup')
														</button>
													</div>
												</div>
											</div>
										</div>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
			'countries' => $countries,
			'cities' => $city_list,
			'user' => [
				'email' => old('email', ''),
				'first_name' => old('first_name', ''),
				'last_name' => old('last_name', ''),
				'password' => old('password', ''),
				'password_confirmation' => old('password_confirmation', ''),
				'country' => old('country', ''),
				'city' => old('city', ''),
				'gender' => old('gender', ''),
				'dob' => old('dob', ''),
				'phone_number' => old('phone_number', ''),
				'manager_title' => old('manager_title', ''),
				'company_name' => old('company_name', ''),
				'company_tax_number' => old('company_tax_number', ''),
				'company_fax_number' => old('company_fax_number', ''),
				'company_tele_phone_number' => old('company_tele_phone_number', ''),
				'address_line1' => old('address_line1', ''),
				'address_line2' => old('address_line2', ''),
				'company_state' => old('company_state', ''),
				'company_city' => old('company_city', ''),
				'company_country' => old('company_country', ''),
				'company_pincode' => old('company_pincode', ''),
				'company_website' => old('company_website', ''),
				'company_email' => old('company_email', ''),
			],
		]) 
	!!}
</script>
@endpush