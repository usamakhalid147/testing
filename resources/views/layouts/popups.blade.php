@guest
<!-- Login With Mobile Modal Start -->
<div class="modal fade" id="loginMobileModal" tabindex="-1" aria-labelledby="loginMobileModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" :class="{'loading':isLoading}">
				<div class="form-group">
					<label class="form-label"> @lang('messages.country') </label>
					<select name="login_country_code" class="form-select" v-model="mobile_login_data.country_code">
						@foreach(resolve('Country') as $country)
						<option value="{{ $country->name }}"> {{ $country->full_name.' (+'.$country->phone_code.')' }} </option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="form-label"> @lang('messages.phone_number') </label>
					<input type="number" class="form-control" name="login_phone_number" placeholder="@lang('messages.phone_number')" v-model="mobile_login_data.phone_number">
				</div>
				<div class="line-separator my-2 d-flex align-items-center"></div>
				<div class="form-group" v-if="mobile_login_data.show_verification_code">
					<label class="form-label d-flex">
						@lang('messages.enter_otp')
						<span v-if="mobile_login_data.show_resend_btn" class="ms-auto">
							<a href="javascript:;" v-on:click="LoginWithPhoneNumber('send_otp')"> @lang('messages.resend_otp') </a>
						</span>
					</label>
					<input type="text" class="form-control" name="verify_otp" v-model="mobile_login_data.verify_code">
				</div>
				<span class="text-danger mt-0" v-show="mobile_login_data.error_message"> @{{ mobile_login_data.error_message }} </span>
				<div class="form-group mt-4">
					<button type="submit" v-if="!mobile_login_data.show_verification_code" class="btn btn-primary d-flex w-100 justify-content-center" v-on:click="LoginWithPhoneNumber('send_otp')">
						@lang('messages.continue')
					</button>
					<button type="submit" v-if="mobile_login_data.show_verification_code" class="btn btn-primary d-flex w-100 justify-content-center" v-on:click="LoginWithPhoneNumber('verify_otp')">
						@lang('messages.verify_otp')
					</button>
				</div>
				<div class="mt-4 text-center">
					@lang('messages.dont_have_account')
					<a href="{{ route('signup') }}" class="">
						@lang('messages.signup')
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Login With Mobile Modal End -->
<!-- Signup Email Modal Start -->
<div class="modal fade signup_email" id="signupEmailModal" tabindex="-1" aria-labelledby="signupEmailModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title text-center w-100">
					@lang('messages.signup_with') @lang('messages.email')
				</div>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form">
					{!! Form::open(['url' => resolveRoute('create_user'), 'class' => '','id'=>'signup_form']) !!}
					@if(request()->get('referral'))
						<input type="hidden" name="referral" value="{{ request()->get('referral') }}">
					@endif
					<div class="form-floating" :class="{'required': user.full_name == '' || user.full_name == undefined}">
						<input type="text" name="full_name" class="form-control" v-model="user.full_name">
						<label> @lang('messages.full_name') <em class="text-danger"> *</em></label>
						<span class="text-danger"> {{ $errors->first('full_name') }} </span>
					</div>
					<div class="form-floating" :class="{'required': user.email == '' || user.email == undefined}">
						<input type="email" name="email" class="form-control" id="signup_email" v-model="user.email">
						<label for="signup_email"> @lang('messages.email') <em class="text-danger"> *</em> </label>
						<span class="text-danger"> {{ $errors->first('email') }} </span>
					</div>
					<div class="password-with-toggler input-group floating-input-group">
						<div class="form-floating flex-grow-1" :class="{'required': user.password == '' || user.password == undefined}">
							<input type="password" name="password" class="password form-control" v-model="user.password">
							<label> @lang('messages.password') <em class="text-danger"> *</em></label>
						</div>
						<span class="input-group-text"><i class="icon icon-eye cursor-pointer toggle-password active" area-hidden="true"></i></span>
					</div>
					<span class="text-danger"> {{ $errors->first('password') }} </span>
					<div class="password-with-toggler input-group floating-input-group">
						<div style="margin-bottom:0px !important;" class="form-floating flex-grow-1 mb-0" :class="{'required': user.password_confirmation == '' || user.password_confirmation == undefined}">
							<input type="password" name="password_confirmation" id="password_confirmation" class="password form-control" v-model="user.password_confirmation">
							<label> @lang('messages.password_confirmation') <em class="text-danger"> *</em></label>
						</div>
						<span class="input-group-text" style="margin-bottom:0px !important;"><i class="icon icon-eye cursor-pointer toggle-password active" area-hidden="true"></i></span>
						<small class="text-muted mb-2">Password must be at least 8 characters, 1 uppercase letter, 1 lowercase letter, & 1 number</small>
					</div>
					<div class="form-floating" :class="{'required': user.phone_number == '' || user.phone_number == undefined}">
						<input type="text" name="phone_number" id="phone_number" class="form-control" v-model="user.phone_number">
						<label for="phone_number"> @lang('messages.phone_number') <em class="text-danger"> *</em></label>
						<span class="text-danger"> {{ $errors->first('phone_number') }} </span>
					</div>
					<div class="form-floating">
						<input type="text" name="address_line_1" class="form-control" v-model="user.address_line_1">
						<label> @lang('messages.home_address') </label>
						<span class="text-danger"> {{ $errors->first('address_line_1') }} </span>
					</div>
					<div class="form-floating">
						<input type="text" name="address_line_2" class="form-control" v-model="user.address_line_2">
						<label> @lang('messages.ward') </label>
						<span class="text-danger"> {{ $errors->first('address_line_2') }} </span>
					</div>
					<div class="form-floating">
						<input type="text" name="state" class="form-control" v-model="user.state">
						<label> @lang('messages.town') </label>
						<span class="text-danger"> {{ $errors->first('state') }} </span>
					</div>
					<div class="form-floating" :class="{'required': user.country == '' || user.country == undefined}">
						<select name="country" class="form-control" v-model="user.country" v-on:change="user.city = ''">
							<option value="" selected>@lang('messages.select')</option>
							<option :value="country.name" v-for="country in countries">@{{country.full_name}} (+@{{country.phone_code}})</option>
						</select>
						<label for="country"> @lang('messages.country') <em class="text-danger"> *</em></label>
						<span class="text-danger"> {{ $errors->first('country') }} </span>
					</div>
					<div class="form-floating" :class="{'required': user.city == '' || user.city == undefined}">
						<select name="city" class="form-control" v-model="user.city">
							<option value="">@lang('messages.select')</option>
							<option :value="city.name" v-for="city in cities" v-show="city.country == user.country">@{{city.name}}</option>
						</select>
						<label for="city"> @lang('messages.city_desc') <em class="text-danger"> *</em></label>
						<span class="text-danger"> {{ $errors->first('city') }} </span>
					</div>
					<div class="form-floating">
						<input type="text" name="postal_code" class="form-control" v-model="user.postal_code">
						<label> @lang('messages.postal_code') </label>
						<span class="text-danger"> {{ $errors->first('postal_code') }} </span>
					</div>
					<div class="form-floating">
						<p class="mb-0"> @lang('messages.gender') </p>
					</div>
					<div class="form-floating">
						<div class="form-check form-check-inline">
						  <input type="radio" name="gender" class="form-check-input" id="male" value="Male">
						  <label class="form-check-label" for="male"> @lang('messages.male') </label>
						</div>
						<div class="form-check form-check-inline">
						  <input type="radio" name="gender" class="form-check-input" id="female" value="Female">
						  <label class="form-check-label" for="female"> @lang('messages.female') </label>
						</div>
					</div>
					<div class="row input-group px-2">
						<div class="col-12">
							<p class="mb-0"> @lang('messages.birthday') </p>
							<small class="text-muted"> @lang('messages.birthday_desc',['replace_key_1' => $site_name]) </small>
						</div>
						<div class="form-group col pt-2">
							<label for="birthday_month"> @lang('messages.month') </label>
							{!! Form::selectMonthWithDefault('birthday_month', null, Lang::get('messages.month'), ['class' => 'form-select mt-2']) !!}
						</div>
						<div class="form-group col pt-2">
							<label for="birthday_day"> @lang('messages.day') </label>
							{!! Form::selectRangeWithDefault('birthday_day', 1, 31, null, trans('messages.day'), [ 'class' => 'form-select mt-2']) !!}
						</div>
						<div class="form-group col pt-2">
							<label for="birthday_year"> @lang('messages.year') </label>
							{!! Form::selectRangeWithDefault('birthday_year', date('Y'), date('Y') - 80, '', Lang::get('messages.year'),[ 'class' => 'form-select mt-2']) !!}
						</div>
					</div>
					<span class="text-danger"> {{ $errors->first('birthday_day') }} </span>
					<label for="signup_agree" class="form-check-label">
							@lang('messages.signup_agree',['replace_key_1' => $site_name])
						</label>
					<div class="form-check mt-3">
						<label class="form-check-label">
							@foreach($agree_pages as $page)
								<input
									type="checkbox"
									name="agree_page[]"
									class="form-check-input"
									id="{{ $page->id }}"
									value="{{ $page->id }}"
									v-model="{{ Illuminate\Support\Str::slug($page->slug, '_') }}"
								>
								<label for="{{ $page->id }}" class="form-check-label">
									<a href="{{ $page->url }}" target="_blank" class="primary-link"> {{ $page->name }} <em class="text-danger"> *</em> </a>
								</label>
								@if (!$loop->last)
									<br>
								@endif
							@endforeach
						</label>
					</div>
					@if(checkEnabled('ReCaptcha') && credentials('version','ReCaptcha') == '2')
					<div class="recaptcha-container mt-2">
						<div class="g-recaptcha" data-sitekey="{{ credentials('site_key','ReCaptcha') }}"></div>
					</div>
					@endif
					@if($errors->has('g-recaptcha-response'))
						<span class="text-danger"> {{ $errors->first('g-recaptcha-response') }} </span>
					@endif
					<div class="form-group mt-4">
						<button type="submit" class="btn btn-primary d-flex w-100 justify-content-center" :disabled="!terms_of_use || !refund_and_cancellation_policy || !cookie_policy || !privacy_policy">
							@lang('messages.signup')
						</button>
					</div>
					{!! Form::close() !!}
					<div class="line-separator my-2 d-flex align-items-center"></div>
					<div class="mt-3 text-center">
						@lang('messages.already_have_account')
						<a href="{{ route('login') }}" class="">
							@lang('messages.login')
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Signup Email Modal End -->
<!-- Forgot Password Modal Start -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form">
					{!! Form::open(['url' => resolveRoute('reset_password'), 'class' => '','id'=>'reset_password_form']) !!}
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
				<div class="mt-4 text-center">
					<a href="#" class="" data-bs-dismiss="modal">
						@lang('messages.login')
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Forgot Password Modal End -->
@endguest
<!-- Currency Modal Start -->
<div class="modal fade" id="CurrencyModel" tabindex="-1" aria-labelledby="CurrencyModel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content" :class="{'loading':isLoading}">
			<div class="modal-header border-0">
				<ul class="nav nav-pills lang-currency" id="pills-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="tab-btn active btn-link border-0 bg-white fs-4 " id="pills-currency-tab" data-bs-toggle="pill" data-bs-target="#pills-currency" type="button" role="tab" aria-controls="pills-currency" aria-selected="false"> @lang('messages.currency') </button>
					</li>
				</ul>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="tab-content" id="pills-tabContent">
				 
				  <div class="tab-pane d-block" id="pills-currency" role="tabpanel" aria-labelledby="pills-currency-tab">
				  	<ul class="popup-user-default list-unstyled row align-items-center">
					  	@foreach($currency_list as $key => $currency)
					  	<li class="col-2" :class="(userCurrency == '{{ $key }}') ? 'option-selected' : ''">
						  	<label class="w-100 user-option p-2 m-2 cursor-pointer border rounded text-truncate text-center">
						  		<input type="radio" name="user_currency" class="user-option-input" value="{{ $key }}" v-model="userCurrency" :checked="userCurrency == '{{ $key }}'" v-on:change="updateUserDefault('currency')">
								<span class="cursor-pointer"> {{ $currency }} </span>
							</label>
						</li>
						@endforeach
				  	</ul>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Language Modal Start -->
<div class="modal fade" id="LanguageModel" tabindex="-1" aria-labelledby="LanguageModel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content" :class="{'loading':isLoading}">
			<div class="modal-header border-0">
				<ul class="nav nav-pills lang-currency" id="pills-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="tab-btn active btn-link border-0 bg-white" id="pills-language-tab" data-bs-toggle="pill" data-bs-target="#pills-language" type="button" role="tab" aria-controls="pills-language" aria-selected="true"> @lang('messages.language') </button>
					</li>
				</ul>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="tab-content" id="pills-tabContent">
				  <div class="tab-pane d-block" id="pills-currency" role="tabpanel" aria-labelledby="pills-currency-tab">
				  	<ul class="popup-user-default list-unstyled row align-items-center">
					  	@foreach($currency_list as $key => $currency)
					  	<li class="col-2" :class="(userCurrency == '{{ $key }}') ? 'option-selected' : ''">
						  	<label class="w-100 user-option p-2 m-2 cursor-pointer border rounded text-truncate text-center">
						  		<input type="radio" name="user_currency" class="user-option-input" value="{{ $key }}" v-model="userCurrency" :checked="userCurrency == '{{ $key }}'" v-on:change="updateUserDefault('currency')">
								<span class="cursor-pointer"> {{ $currency }} </span>
							</label>
						</li>
						@endforeach
				  	</ul>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Report Profile Modal Start -->
<div class="modal fade" id="reportProfileModal" tabindex="-1" aria-labelledby="reportProfileModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<h4> @lang('messages.whats_happening') </h4>
				<p class="text-gray">
					<i class="material-icons align-text-bottom">lock</i>
					<span class="mt-0">@lang('messages.only_shared_with_site',['replace_key_1' => $site_name])</span>
				</p>
				<div class="form">
					{!! Form::open(['url' => '', 'class' => 'report_form', 'id'=>'report_form']) !!}
					<div class="form-group">
						<div class="radio">
							<label>
								<input type="radio" name="report_type" id="spamming_me" value="spamming_me">
								@lang('messages.spamming_me')
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="report_type" id="being_offensive" value="being_offensive">
								@lang('messages.being_offensive')
							</label>
						</div>
						<div class="radio">
							<label>
								<input type="radio" name="report_type" id="something_else" value="something_else">
								@lang('messages.something_else')
							</label>
						</div>
					</div>
					<div class="form-group mt-4">
						<button type="submit" class="btn btn-primary d-flex float-end">
							@lang('messages.ok')
						</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@if(Route::currentRouteName() == 'hotel_search')
<!-- Search Page More Filter Modal Start -->
<div class="modal fade" id="moreFiltersModal" tabindex="-1" aria-labelledby="moreFiltersModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.more_filters') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="mb-3 modal-body pb-5">
				<div class="px-md-3 px-0 mb-2">
					<h3 class="mb-3"> @lang('messages.type_of_property') </h3>
					<div class="d-flex flex-row flex-wrap w-100">
						@foreach($property_types as $property_type)
						<div class="form-check d-flex col-6 mb-2">
							<input type="checkbox" name="property_types[]" class="form-check-input" id="property_type_{{ $property_type->id }}" value="{{ $property_type->id }}" v-model="searchFilter.property_type">
							<label for="property_type_{{ $property_type->id }}" class="form-check-label ms-2"> {{ $property_type->name }} </label>
						</div>
						@endforeach
					</div>
				</div>
				{{--<div class="px-md-3 px-0 mb-2 pt-2 border-top">
					<h3 class="mb-3"> @lang('messages.amenities') </h3>
					<div class="d-flex flex-row flex-wrap w-100">
						@foreach($amenities as $amenity)
						<div class="form-check d-flex col-6 mb-2">
							<input type="checkbox" name="amenities[]" class="form-check-input" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" v-model="searchFilter.amenities">
							<label for="amenity_{{ $amenity->id }}" class="form-check-label ms-2"> {{ $amenity->name }} </label>
						</div>
						@endforeach
					</div>
				</div>--}}
				<div class="px-md-3 px-0 mb-2 pt-2 border-top">
					<div class="star-rate">
						<h5 class="mb-3">Star rating</h5>
						<div class="d-flex flex-wrap">
							<button>1
								<span class="material-icons-outlined">
									star
								</span>
							</button>
							<button>2
								<span class="material-icons-outlined">
									star
								</span>
							</button>
							<button>3
								<span class="material-icons-outlined">
									star
								</span>
							</button>
							<button>4
								<span class="material-icons-outlined">
									star
								</span>
							</button>
							<button>5
								<span class="material-icons-outlined">
									star
								</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer d-flex align-items-center justify-content-between">
				<a href="javascript:void(0)" class="common-link" v-on:click="resetFilter('more_filters');"> @lang('messages.cancel') </a>
				<a href="javascript:void(0)" v-on:click="applyFilter('more_filters');" data-bs-dismiss="modal"> @lang('messages.apply') </a>
			</div>
		</div>
	</div>
</div>
<!-- Search Page More Filter Modal End -->
@endif
<!-- Report Profile Modal Start -->
@if(Route::currentRouteName() == 'reservations')
<!-- Host Cancel Booking Modal Start -->
<div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4> @lang('messages.cancel_your_reservation') </h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form">
					{!! Form::open(['url' => resolveRoute('cancel_reservation'), 'class' => 'cancel_form', 'id'=>'cancel-form']) !!}
					{!! Form::hidden('reservation_id','',[':value' => 'selectedReservation.id']) !!}
					<div class="form-group">
						<label for="cancelMessage" class="form-label">
							@lang('messages.why_are_you_cancel')
						</label>
						<p class="text-xsmall m-0"> @lang('messages.info_not_shared_with_guest') </p>
						<select name="cancel_reason" class="form-select mt-1 px-2" v-model="cancelDetails.cancelReason">
							<option value="" selected> @lang('messages.select') </option>
							@foreach(HOST_CANCEL_REASONS as $reason)
							<option value="{{ $reason }}"> @lang('messages.'.$reason) </option>
							@endforeach
						</select>
					</div>
					{{--<div class="form-floating">
						<textarea name="cancel_message" class="form-control rows-5" id="cancelMessage" v-model="cancelDetails.cancelMessage" placeholder="@lang('messages.type_message_to_guest')"></textarea>
						<label for="cancelMessage"> @lang('messages.type_message_to_guest') </label>
					</div>--}}
					<div class="form-group mt-4">
						<button type="submit" class="btn btn-primary float-end" :disabled="cancelDetails.cancelReason == ''">
							@lang('messages.cancel_reservation')
						</button>
						<button type="button" class="btn btn-default float-end me-3" data-bs-dismiss="modal" aria-label="Close">
							@lang('messages.close')
						</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Host Cancel Booking Modal Start -->
@endif
@if(Route::currentRouteName() == 'bookings')
<!-- Guest Cancel Booking Modal Start -->
<div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4> @lang('messages.cancel_your_booking') </h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form">
					{!! Form::open(['url' => resolveRoute('cancel_reservation'), 'class' => 'cancel_form', 'id'=>'cancel-form']) !!}
					{!! Form::hidden('reservation_id','',[':value' => 'selectedReservation.id']) !!}
					<div class="form-group">
						<label for="selected_rooms" class="form-label"> @lang('messages.rooms') </label>
						<select name="room_reservations" class="form-select">
							<option value="all" selected> @lang('messages.all') </option>
							<option v-for="room_reservation in selectedReservation.room_reservations" v-show="room_reservation.status == 'Accepted'" :value="room_reservation.id"> @{{ room_reservation.room_name }} </option>
						</select>
					</div>
					<div class="form-group">
						<label for="cancelMessage" class="form-label">
							@lang('messages.why_are_you_cancel')
						</label>
						<p class="text-xsmall m-0"> @lang('messages.info_not_shared_with_host') </p>
						<select name="cancel_reason" class="form-select mt-1 px-2" v-model="cancelDetails.cancelReason">
							<option value="" selected> @lang('messages.select') </option>
							@foreach(GUEST_CANCEL_REASONS as $reason)
							<option value="{{ $reason }}"> @lang('messages.'.$reason) </option>
							@endforeach
						</select>
					</div>
					{{--<div class="form-floating">
						<textarea name="cancel_message" class="form-control rows-5" id="cancelMessage" v-model="cancelDetails.cancelMessage" placeholder="@lang('messages.type_message_to_host')"></textarea>
						<label for="cancelMessage"> @lang('messages.type_message_to_host') </label>
					</div>--}}
					<div class="form-group mt-4">
						<button type="submit" class="btn btn-primary float-end" :disabled="cancelDetails.cancelReason == ''">
							@lang('messages.cancel_booking')
						</button>
						<button type="button" class="btn btn-default float-end me-3" data-bs-dismiss="modal" aria-label="Close">
							@lang('messages.close')
						</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Guest Cancel Booking Modal Start -->
@endif
<!-- desktop search Modal -->
<div class="modal fade headerSearchModal p-0" id="headerSearchModal" tabindex="-1" aria-labelledby="headerSearchModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content no-border">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body mb-2">
				<form action="{{ resolveRoute('search') }}" class="search-form pb-3">
					<div class="align-items-start d-flex flex-column header-category">
						<h5 class="pb-3 text-black"> @lang('messages.find_your_place') </h5>
	                </div>
					<div class="row g-3">
						<div class="col">
							<input type="hidden" name="place_id" class="autocomplete-place_id" value="{{ $searchFilter['place_id'] ?? '' }}">
							<div class="form-floating">
							  <input type="text" name="location" class="form-control autocomplete-input" id="locationInput" placeholder="@lang('messages.where')">
							  <label for="locationInput">@lang('messages.where')</label>
							</div>
						</div>
						<div class="col">
							<input type="hidden" name="checkin" class="popup_checkin" id="" value="{{ $default_checkin }}">
							<input type="hidden" name="checkout" class="popup_checkout" id="popup_checkout"  value="{{ $default_checkout }}">
							<div class="form-floating">
								<input type="text" id="popup_date_picker" value="{{ $default_checkin.' to '.$default_checkout }}" class="form-control popup_date_picker" placeholder="@lang('messages.add_dates')" readonly>
							  	<label for="popup_date_picker"> @lang('messages.checkin') - @lang('messages.checkout') </label>
							</div>
						</div>
						<div class="col">
							<div class="form-floating">
								<input type="button" name="" class="btn bg-white text-start form-control popup_occupancy" data-bs-toggle="dropdown" data-bs-auto-close="outside" value="">
							  	<label for="search_date_picker"> @lang('messages.guests')</label>
							  	<ul class="dropdown-menu guest-menu w-100 p-3">
							  		<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6">@lang('messages.rooms')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_rooms" data-type="rooms" disabled>
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="rooms" class="popup_rooms" value="1" class="form-control">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_rooms" data-type="rooms">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
									<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6">@lang('messages.adults')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_adults" data-type="adults">
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="adults" value="2" class="form-control popup_adults">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_adults" data-type="adults">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
									<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6 mb-0">@lang('messages.children')</p>
												<p class="m-0 text-gray">@lang('messages.children_desc')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_children" data-type="children">
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="children" value="1" class="form-control popup_children">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_children" data-type="children">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<div class="col d-flex align-items-center mb-3">
							<button class="btn btn-primary">
								<i class="icon icon-search" aria-hidden="true"></i>
								<span class="ms-1"> @lang('messages.search') </span>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- desktop search popup -->
<!-- mobile search modal -->
<div class="modal fade mobileSearchModal" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModal" aria-hidden="true">
	<div class="modal-dialog modal-fullscreen-sm-down" role="document">
		<div class="modal-content no-border">
			<div class="modal-header border-0">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="{{ resolveRoute('search') }}" class="search-form m-0">
					@if(Route::currentRouteName() == 'search')
						<input type="hidden" name="place_id" class="autocomplete-place_id" value="{{ $searchFilter['place_id'] ?? '' }}">
						<div class="form-floating">
							<input type="text" name="location" class="form-control autocomplete_input" id="home-location" placeholder="@lang('messages.where')" v-model="searchFilter.location">
							<label for="home-location">@lang('messages.where')</label>
						</div>
					@else
						<input type="hidden" name="place_id" class="autocomplete-place_id">
						<div class="form-floating">
							<input type="text" name="location" class="form-control autocomplete_input" id="home-location" placeholder="@lang('messages.where')">
							<label for="home-location">@lang('messages.where')</label>
						</div>
						<div class="form-floating">
							<input type="hidden" name="checkin" id="home_checkin" value="{{$default_checkin}}">
							<input type="hidden" name="checkout" id="home_checkout" value="{{$default_checkout}}">
							<input class="form-control popup_mobile_date_picker" id="popup_mobile_date_picker" placeholder="@lang('messages.checkin') - @lang('messages.checkout')" readonly value="{{ $default_checkin.' to '.$default_checkout }}">
							<label for="popup_mobile_date_picker"> @lang('messages.checkin') - @lang('messages.checkout') </label>
						</div>
						<div class="form-floating">
							<div class="form-floating">
								<input type="button" name="" class="btn bg-white text-start form-control popup_occupancy" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="popup_occupancy" value="">
							  	<label for="guest"> @lang('messages.guests')</label>
							  	<ul class="dropdown-menu guest-menu w-100 p-3">
							  		<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6">@lang('messages.rooms')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_rooms" data-type="rooms" disabled>
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="rooms" class="popup_rooms" value="1" class="form-control">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_rooms" data-type="rooms">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
									<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6">@lang('messages.adults')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_adults" data-type="adults">
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="adults" value="2" class="form-control popup_adults">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_adults" data-type="adults">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
									<li class="mb-2">
										<div class="d-flex justify-content-between align-items-end">
											<div>
												<p class="h6 mb-0">@lang('messages.children')</p>
												<p class="m-0 text-gray fs-6 fw-normal">@lang('messages.children_desc')</p>
											</div>
											<div class="d-flex justify-content-between align-items-center">
												<button type="button" class="btn btn-default me-auto p-0 rounded-circle popup-btn-minus popup_minus_children" data-type="children">
												    <span class="icon icon-minus"></span>
										  		</button>
									  			<input type="number" name="children" value="1" class="form-control popup_children">
												<button type="button" class="btn btn-default popup-btn-plus rounded-circle ms-auto p-0 popup_plus_children" data-type="children">
													<span class="icon icon-plus"></span>
												</button>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					@endif
					<div class="button-container ms-md-2 mt-2">
						<button class="btn btn-primary d-flex align-items-center search-btn justify-content-center">
							<i class="icon icon-search" aria-hidden="true"></i>
							<span class="ms-1"> @lang('messages.search') </span>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- Common Modal for display errors -->
<div class="modal fade" id="commonErrorModal" tabindex="-1" aria-labelledby="commonErrorModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3">
			<div class="modal-header">
				<p class="text-center">
					@lang('messages.failed')
				</p>
			</div>
			<div class="modal-body py-4">
				<p class="text-gray" id="error-description"></p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-bs-dismiss="modal"> @lang('messages.close') </button>
			</div>
		</div>
	</div>
</div>
<!-- Common Modal for display errors -->
<!-- Delete Photo Confirmation Modal -->
@if(Route::currentRouteName() == 'manage_hotel' && request()->step == 'photos')
<div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-labelledby="deletePhotoModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			<div class="modal-header">
				<p class="text-center"> @lang('messages.delete') </p>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-4">
				<p class="text-gray">
					<input type="hidden" id="selected_index">
					@lang('messages.delete_photo_description')
				</p>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.close') </button>
				<button class="btn btn-primary" v-on:click="deletePhoto();"> @lang('messages.delete') </button>
			</div>
		</div>
	</div>
</div>
@endif
<!-- Delete Photo Confirmation Modal -->
@if((Route::currentRouteName() == 'manage_listing' && in_array(request()->step,['photos','photo_360'])) || Route::currentRouteName() == 'room_details')
<div class="modal model-lg fade" id="photoViwerModal">
	<div class="modal-dialog">
		<div class="modal-content p-3">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-4" :class="{'loading' : isLoading}">
				<div id="photoViewer" style="height: 500px;width: 100%;"></div>
			</div>
		</div>
	</div>
</div>
@endif
@if(!isset($exception) && Route::currentRouteName() == 'hotel_details')
<!-- All Reviews Modal -->
<div class="modal fade" id="reviewsModal" tabindex="-1" aria-labelledby="reviewsModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body row py-4">
				<div class="col-4 review-header d-flex">
					<div class="h3"> <span class="theme-color material-icons">star_rate</span> </div>
					<div class="h3"> {{ floatval($hotel->rating) }} <span class="review-text ms-1 text-gray">( {{ $hotel->total_rating }} @choice('messages.hotel_review',$hotel->total_rating))</span> </div>
				</div>
				<div class="col-8 review-container">
					@foreach($reviews as $review)
					<div class="user-review">
						<div class="col-12 my-2 d-flex">
							<div class="review-user">
								<a class="media-photo media-round align-top" href="{{ resolveRoute('view_profile',['id' => $review->user_from])}}">
									<img class="profile-image" src="{{ $review->user->profile_picture_src }}" title="{{ $review->user->first_name }}">
								</a>
							</div>
							<div class="ms-2 ps-2">
								<h4 class="text-black d-block"> {{ $review->user->first_name }} </h4>
								<h5 class="text-gray d-block"> {{ $review->created_at->format('F Y') }} </h5>
							</div>
						</div>
						<div class="col-12 col-md-9 mb-2 mt-md-2">
							<p class="text-gray"> {{ $review->public_comment }} </p>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
<!-- All Reviews Modal -->
<!-- share Modal Start -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header border-0">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<!-- Modal body -->
			<div class="modal-body">
				<h5 class="font-bolder m-0 text-center w-100 fs-4 mb-4"> @lang('messages.share_with_others')</h5>
				<div class="row g-2 g-md-3">
					@foreach($share_data as $share)
					<div class="col-6">
						<div class="social_style">
							<a href="{{ $share['link'] }}" class="share-btn link-icon border rounded-4 overflow-hidden d-flex align-items-center" rel="nofollow" target="_blank" title="{{ $share['header_title'] }}">
								<i class="{{ $share['icon'] }}" aria-hidden="true"></i>
								<span> {{ $share['header_title'] }} </span>
							</a>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>
<!-- share Modal End -->
<!-- Amenities Model -->
<div class="modal fade" id="amenitiesModal" tabindex="-1" aria-labelledby="amenitiesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg modal-fullscreen-md-down">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title fw-bold fs-3" id="amenitiesModalLabel"> @lang('messages.listing.amenities')</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="listing-content">
					<div class="row">
						@foreach($selected_amenities as $amenity)
						<div class="col-md-6 mt-1">
							<img class="img-icon" src="{{ $amenity->image_src }}">
							<span class="fw-normal">
								{{ $amenity->name }}
								@if($amenity->description != '')
								<i class="icon icon-info desc-tooltip" data-bs-toggle="tooltip" title="{{ $amenity->description }}" area-hidden="true"></i>
								@endif
							</span>
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Amenities Model -->
@endif
@guest
<!-- Responsive User Profile Modal Start -->
<div class="userProfileModal d-none" id="userProfileModal">
	<div class="px-3 pt-5 d-block">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div class="welcome-note">
				<h2> @lang('messages.hi') @lang('messages.guest') </h2>
			</div>
			<div class="user-profile">
				<a href="#">
					<img class="rounded-profile-image-normal" src="{{ asset('images/profile_picture.png') }}" alt="Profile">
				</a>
			</div>
		</div>
		<div class="line-divider"></div>
		<ul class="navigation-menu-items">
			<li class="w-100 bg-light">
				<a href="{{ resolveRoute('login') }}" class="btn text-white btn-success d-flex justify-content-center">
					@lang('messages.login')
				</a>
			</li>
		</ul>
		<div class="line-divider"></div>
		<ul class="navigation-menu-items">
			<p class="tl-link mb-3"> @lang('messages.manage_profile') </p>
			<li>
				<a href="{{ resolveRoute('create_listing') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-link icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.create_new_listing')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@if(global_settings('referral_enabled'))
			<li>
				<a href="{{ resolveRoute('invite') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3"> 
					<span class="icon icon-link icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.refer_and_earn') </span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@endif
			<li>
				<a href="{{ resolveRoute('update_account_settings',['page' => 'site-setting']) }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-globe icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.currency_language')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
		</ul>
		<div class="line-divider"></div>
		<ul class="navigation-menu-items">
			<p class="tl-link mb-3"> {{ $site_name }} </p>
			<li>
				<a href="{{ resolveRoute('help') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-help icon-link fs-1 me-2"></span>
					<span>@lang('messages.help')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			<div class="line-divider"></div>
			@foreach($footer_sections as $name => $footer_section)
			<ul class="navigation-menu-items">
				<p class="tl-link mb-3"> @lang('messages.'.$name) </p>
				<ul class="list-unstyled">
					@foreach($footer_section as $section)
					<li>
						<a href="{{ url($section->slug) }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
							<span class="icon icon-link icon-link fs-1 me-2"></span>
							<span>{{ $section->name }} </span>
							<span class="icon icon-arrow-right ms-auto"></span>
						</a>
					</li>
					@endforeach
				</ul>
			</ul>
			<div class="line-divider"></div>
			@endforeach
		</ul>
	</div>
</div>
@endguest
@auth
<!-- Responsive User Profile Modal Start -->
<div class="userProfileModal d-none" id="userProfileModal">
	<div class="px-3 pt-5 d-block">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div class="welcome-note">
				<h2> @lang('messages.hi') {{ Auth::user()->first_name }} </h2>
				<a href="{{ resolveRoute('view_profile',['id' => Auth::id()]) }}"> @lang('messages.show_profile') </a>
			</div>
			<div class="user-profile">
				<a href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}">
					<img class="rounded-profile-image-normal" src="{{ Auth::user()->profile_picture_src }}" alt="{{ Auth::user()->first_name }}">
				</a>
			</div>
		</div>
		<div class="line-divider"></div>
		<ul class="navigation-menu-items">
			<p class="tl-link mb-3"> @lang('messages.manage_profile') </p>
			<li>
				<a href="{{ resolveRoute('dashboard') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-dashboard icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.dashboard')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			<li>
				<a href="{{ resolveRoute('account_settings') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-account-settings icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.account_settings')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@if(Auth::user()->is_host)
			<li>
				<a href="{{ resolveRoute('listings') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-manage-listing icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.manage_listings')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			<li>
				@if(global_settings('host_can_add_coupon'))
				<a href="{{ resolveRoute('host_coupon_codes') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3"> 
					<span class="icon icon-coupen-code icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.manage_coupon_code') </span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
				@endif
			</li>
			<li>
				<a href="{{ resolveRoute('reservations') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-reservation icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.reservations')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@else
			<li>
				<a href="{{ resolveRoute('create_listing') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-create-list icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.create_new_listing')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@endif
			@if(global_settings('referral_enabled'))
			<li>
				<a href="{{ resolveRoute('invite') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3"> 
					<span class="icon icon-earn icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.refer_and_earn') </span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			@endif
			<li>
				<a href="{{ resolveRoute('update_account_settings',['page' => 'site-setting']) }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-globe icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.currency_language')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
		</ul>
		<div class="line-divider"></div>
		<ul class="navigation-menu-items">
			<p class="tl-link mb-3"> {{ $site_name }} </p>
			<li>
				<a href="{{ resolveRoute('help') }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
					<span class="icon icon-help icon-outlined fs-1 me-2"></span>
					<span>@lang('messages.help')</span>
					<span class="icon icon-arrow-right ms-auto"></span>
				</a>
			</li>
			<div class="line-divider"></div>
			@foreach($footer_sections as $name => $footer_section)
			<ul class="navigation-menu-items">
				<p class="tl-link mb-3"> @lang('messages.'.$name) </p>
				<ul class="list-unstyled">
					@foreach($footer_section as $section)
					<li>
						<a href="{{ url($section->slug) }}" class="d-inline-flex justify-content-start align-items-center w-100 py-3">
							<span class="icon icon-link icon-outlined fs-1 me-2"></span>
							<span>{{ $section->name }} </span>
							<span class="icon icon-arrow-right ms-auto"></span>
						</a>
					</li>
					@endforeach
				</ul>
			</ul>
			<div class="line-divider"></div>
			@endforeach
		</ul>
		<ul class="navigation-menu-items">
			<li class="w-100 bg-light">
				<a href="{{ resolveRoute('logout') }}" class="btn text-white btn-danger d-flex justify-content-center">
					@lang('messages.logout')
				</a>
			</li>
		</ul>
	</div>
</div>
<!-- Responsive User Profile -->
@if(Route::currentRouteName() == 'wishlists')
<!-- Create New List Modal Start -->
<div class="modal fade" id="createWishlistModal" tabindex="-1" aria-labelledby="createWishlistModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h3> @lang('messages.create_list') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" :class="{'loading' :isLoading }">
				{!! Form::open(['url' => '', 'class' => '']) !!}
				<div class="form-floating">
					<input type="text" name="wishlist_name" class="form-control" id="wishlist_name" v-model="wishlist.wishlist_name" placeholder="@lang('messages.name')">
					<label for="wishlist_name"> @lang('messages.name') </label>
					<span class="text-small"> @lang('messages.eg') @lang('messages.summer') </span>
					<span class="text-danger" v-show="wishlist_error">
						@{{ wishlist_error }}
					</span>
				</div>

				<div class="form-group">
					<label for="wishlist_privacy"> @lang('messages.privacy_settings') </label>
					<select name="wishlist_privacy" class="form-select mt-2" id="wishlist_privacy" v-model="wishlist.wishlist_privacy">
						<option value="1"> @lang('messages.every_one') </option>
						<option value="0"> @lang('messages.invite_only') </option>
					</select>
				</div>
				{{--<span class="text-danger"> @{{ wishlist_error }} </span>--}}
				<div class="form-group mt-5 pt-md-3 d-flex align-items-center">
					<a href="javascript:;" class="common-link" data-bs-dismiss="modal">
						@lang('messages.cancel')
					</a>
					<button type="button" class="btn btn-primary ms-auto px-4 px-md-5" v-on:click="createWishlist()">
						@lang('messages.save')
					</button>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
<!-- Create New List Modal End -->
@endif
@if(Route::currentRouteName() == 'wishlist.list')
<!-- Edit WishList Modal Start -->
<div class="modal fade" id="editWishlistModal" tabindex="-1" aria-labelledby="editWishlistModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" :class="{'loading' :isLoading }">
				{!! Form::open(['url' => '', 'class' => '']) !!}
				<div class="form-floating">
					<input type="text" name="wishlist_name" class="mt-2 form-control" id="wishlist_name" v-model="wishlist.wishlist_name" placeholder="@lang('messages.name')">
					<label for="wishlist_name"> @lang('messages.name') </label>
					<span class="text-small"> @lang('messages.eg') @lang('messages.summer') </span>
				</div>
				<div class="form-group">
					<label for="wishlist_privacy"> @lang('messages.privacy_settings') </label>
					<select name="wishlist_privacy" class="form-select mt-2" id="wishlist_privacy" v-model="wishlist.wishlist_privacy">
						<option value="1"> @lang('messages.every_one') </option>
						<option value="0"> @lang('messages.invite_only') </option>
					</select>
				</div>
				<span class="text-danger"> @{{ wishlist_error }} </span>
				<div class="form-group mt-5 pt-md-3 d-flex align-items-center">
					<a href="#" class="theme-link" v-on:click="updateOrRemoveWishlist(true)">
						@lang('messages.delete')
					</a>
					<button type="button" class="btn btn-primary ms-auto px-4 px-md-5" v-on:click="updateOrRemoveWishlist()">
						@lang('messages.save')
					</button>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
<!-- Edit WishList Modal End -->
@endif
@if(in_array(Route::currentRouteName(),['hotel_details','hotel_search']))
<!-- Save To List Modal Start -->
<div class="modal fade" id="saveToListModal" tabindex="-1" aria-labelledby="saveToListModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header justify-content-center border-bottom">
				<h3 class="fs-4 text-center w-100 m-0 font-bolder"> @lang('messages.save_to_list') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="wishlist-container py-2" :class="{'loading' :isLoading }">
				<ul class="wishlist-lists px-2">
					<li class="px-3 text-justify mb-3" v-if="wishlists.length == 0"> @lang('messages.no_wishlist_created') </li>
					<li class="d-flex align-items-center cursor-pointer mb-3">
						<button type="button" class="bg-white border-0 font-bolder fs-4 open-modal d-flex" data-current="saveToListModal" data-target="createWishlistModal">
							<div class="wishlist-square-image justify-content-center border rounded-3 d-flex align-items-center cursor-pointe">
								<i class="icon icon-plus fs-1"></i>
							</div>
							<div class="ms-4 align-self-sm-center">
									@lang('messages.create_list')
							</div>
						</button>
					</li>
					<li class="d-flex align-items-center cursor-pointer mb-3" v-for="list in wishlists" v-on:click="saveToWishlist(list.id,room_id)">
						<div class="wishlist-square-image rounded-3 overflow-hidden">
							<img class="img" v-lazy="list.thumbnail">
						</div>
						<div class="wishlist-name ms-4">
							<p class="h5 text-black"> @{{ list.name }} </h3>
							<p class="h6 text-muted mb-0" v-if="list.list_count > 0"> @{{ list.list_count }} @lang('messages.hotels') </p>
							<p class="h6 text-muted mb-0" v-if="list.experience_count > 0"> @{{ list.experience_count }} @lang('messages.experiences') </p>
							<p class="h6 text-muted mb-0" v-if="list.list_count == 0 && list.experience_count == 0"> @lang('messages.nothing_saved_yet') </p>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- Save To List Modal End -->
<!-- Create New List Modal Start -->
<div class="modal fade" id="createWishlistModal" tabindex="-1" aria-labelledby="createWishlistModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.create_list') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" :class="{'loading' :isLoading }">
				<div class="form-floating">
					<input type="text" name="wishlist_name" class="form-control" id="wishlist_name" v-model="wishlist.wishlist_name" placeholder="@lang('messages.name')">
					<label for="wishlist_name" class="form-label"> @lang('messages.name') </label>
					<span class="text-small"> @lang('messages.eg') @lang('messages.summer') </span>
				</div>
				<span class="text-danger"> @{{ wishlist_error }} </span>
				<div class="mt-5 pt-md-3 d-flex align-items-center">
					<a href="#" class="common-link open-modal" data-current="createWishlistModal" data-target="saveToListModal">
						@lang('messages.cancel')
					</a>
					<button type="button" class="btn btn-primary ms-auto px-4 px-md-5" v-on:click="createWishlist()">
						@lang('messages.save')
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Create New List Modal End -->
@endif
@if(Route::currentRouteName() == 'host_calendar')
<!-- Calendar Event Modal Start -->
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.update_availability') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" :class="{'loading' :isLoading }">
				<div class="calendar-availability d-flex">
					<label class="availability-option" :class="(calendar_data.status == 'available') ? 'availability-selected' : '' ">
						<span> @lang('messages.available') </span>
						<input type="radio" name="calendar_status" class="availability-input" value="available" v-model="calendar_data.status" :checked="calendar_data.status == 'available'">
					</label>
					<label class="availability-option" :class="(calendar_data.status == 'not_available') ? 'availability-selected' : ''">
						<span> @lang('messages.not_available') </span>
						<input type="radio" name="calendar_status" class="availability-input" value="not_available" v-model="calendar_data.status" :checked="calendar_data.status == 'not_available'">
					</label>
					<button type="button" class="btn btn-danger mx-2" v-show="calendar_data.calendar_id != ''" v-on:click="updateCalendarEvent('delete')">
						<i class="icon icon-delete"></i>
					</button>
				</div>
				<div class="form-floating">
					<input type="text" name="start_date" class="form-control mt-2" id="start_date" v-model="calendar_data.formatted_start_date" placeholder="@lang('messages.start_date')" readonly>
					<label for="start_date" class="form-label"> @lang('messages.start_date') </label>
				</div>
				<div class="form-floating">
					<input type="text" name="end_date" class="form-control mt-2" id="end_date" v-model="calendar_data.formatted_end_date" placeholder="@lang('messages.end_date')" readonly>
					<label for="end_date" class="form-label"> @lang('messages.end_date') </label>
				</div>
				<div class="form-floating">
					<input type="text" name="notes" class="form-control mt-2" id="notes" v-model="calendar_data.notes" placeholder="@lang('messages.notes')">
					<label for="notes" class="form-label"> @lang('messages.notes') </label>
				</div>
				<span class="text-danger"> @{{ calendar_data.error_message }} </span>
				<div class="mt-5 pt-md-3 d-flex align-items-center">
					<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.cancel') </button>
					<button type="button" class="btn btn-primary ms-auto px-4 px-md-5" v-on:click="updateCalendarEvent()">
						@lang('messages.save')
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Calendar Event Modal End -->
@endif
@if(Route::currentRouteName() == 'conversation')
@if(isset($user_type) && $user_type == 'Host')
<!-- Pre Approve Request Modal -->
<div class="modal fade requestActionModal" id="preApproveModal" tabindex="-1" aria-labelledby="preApproveModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			<div class="border-bottom">
				<p class="mb-2 text-center">
					@lang('messages.pre_approve_this_request')
				</p>
			</div>
			<div class="modal-body py-3">
				<div class="form-floating">
					<textarea name="message" class="form-control rows-5" id="preApproveMessage" v-model="requestDetails.message" placeholder="@lang('messages.type_message_to_guest')"></textarea>
					<label for="preApproveMessage"> @lang('messages.type_message_to_guest') </label>
				</div>
				<div class="form-check">
					<input type="checkbox" name="agree_terms" id="agree_terms" class="form-check-input" v-model="requestDetails.agree_terms">
					<label class="form-check-label" for="agree_terms">
						@lang('messages.by_checking_this_box'),
						@lang('messages.i_agree_the')
						@foreach($agree_pages as $page)
							@if (!$loop->first && $loop->last)
								<span> @lang('messages.and') </span>
							@endif
							<a href="{{ $page->url }}" target="_blank" class="primary-link"> {{ $page->name }} </a>
							@if ($loop->last)
								<span>.</span>
							@else
								@if($loop->remaining != 1)
									<span>,</span>
								@endif
							@endif
						@endforeach
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.close') </button>
				<button class="btn btn-primary" v-on:click="requestAction('pre_approve');" :disabled="!requestDetails.agree_terms || requestDetails.message == ''"> @lang('messages.pre_approve') </button>
			</div>
		</div>
	</div>
</div>
<!-- Pre Approve Request Modal -->
<!-- Pre Accept Request Modal -->
<div class="modal fade requestActionModal" id="preAcceptModal" tabindex="-1" aria-labelledby="preAcceptModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			<div class="border-bottom">
				<p class="mb-2 text-center">
					@lang('messages.pre_accept_this_request')
				</p>
			</div>
			<div class="modal-body py-3">
				<div class="form-floating">
					<textarea name="message" class="form-control rows-5" id="preAcceptMessage" v-model="requestDetails.message" placeholder="@lang('messages.type_message_to_guest')"></textarea>
					<label for="preAcceptMessage"> @lang('messages.type_message_to_guest') </label>
				</div>
				<div class="form-check">
					<input type="checkbox" name="agree_terms" id="agree_terms" class="form-check-input" v-model="requestDetails.agree_terms">
					<label class="form-check-label" for="agree_terms">
						@lang('messages.by_checking_this_box'),
						@lang('messages.i_agree_the')
						@foreach($agree_pages as $page)
							@if (!$loop->first && $loop->last)
								<span> @lang('messages.and') </span>
							@endif
							<a href="{{ $page->url }}" target="_blank" class="primary-link"> {{ $page->name }} </a>
							@if ($loop->last)
								<span>.</span>
							@else
								@if($loop->remaining != 1)
									<span>,</span>
								@endif
							@endif
						@endforeach
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.close') </button>
				<button class="btn btn-primary" v-on:click="requestAction('pre_accept');" :disabled="!requestDetails.agree_terms || requestDetails.message == ''"> @lang('messages.pre_accept') </button>
			</div>
		</div>
	</div>
</div>
<!-- Pre Accept Request Modal -->
<!-- Decline Request Modal -->
<div class="modal fade requestActionModal" id="declineModal" tabindex="-1" aria-labelledby="declineModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			<div class="border-bottom">
				<p class="mb-1 text-center">
					@lang('messages.decline_this_request')
				</p>
				<p class="mb-2 small"> @lang('messages.help_us_to_improve_your_experience') </p>
			</div>
			<div class="modal-body py-3">
				<div class="form-group">
					<label for="cancelMessage"> @lang('messages.why_are_you_decline') </label>
					<p class="text-xsmall m-0"> @lang('messages.info_not_shared_with_guest') </p>
					<select name="cancel_reason" class="form-select mt-1 px-2" v-model="requestDetails.reason">
						<option value="" selected> @lang('messages.select') </option>
						@foreach(HOST_DECLINE_REASONS as $reason)
						<option value="{{ $reason }}"> @lang('messages.'.$reason) </option>
						@endforeach
					</select>
				</div>
				<div class="form-floating">
					<textarea name="message" class="form-control rows-5" id="declineMessage" v-model="requestDetails.message" placeholder="@lang('messages.type_message_to_guest')"></textarea>
					<label for="declineMessage"> @lang('messages.type_message_to_guest') </label>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.close') </button>
				<button class="btn btn-primary" v-on:click="requestAction('decline');" :disabled="requestDetails.reason == '' || requestDetails.message == ''"> @lang('messages.decline') </button>
			</div>
		</div>
	</div>
</div>
<!-- Decline Request Modal -->
@endif
@if(isset($user_type) && $user_type == 'Guest')
<!-- Cancel Request Popup -->
<div class="modal fade requestActionModal" id="cancelRequestModal" tabindex="-1" aria-labelledby="cancelRequestModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			<div class="border-bottom">
				<p class="mb-1 text-center">
					@lang('messages.cancel_this_request')
				</p>
				<p class="mb-2 small"> @lang('messages.help_us_to_improve_your_experience') </p>
			</div>
			<div class="modal-body py-3">
				<div class="form-group">
					<label for="cancelMessage"> @lang('messages.why_are_you_cancel') </label>
					<p class="text-xsmall m-0"> @lang('messages.info_not_shared_with_host') </p>
					<select name="cancel_reason" class="form-select mt-1 px-2" v-model="requestDetails.reason">
						<option value="" selected> @lang('messages.select') </option>
						@foreach(GUEST_CANCEL_REASONS as $reason)
						<option value="{{ $reason }}"> @lang('messages.'.$reason) </option>
						@endforeach
					</select>
				</div>
				<div class="form-floating">
					<textarea name="message" class="form-control rows-5" id="cancelMessage" v-model="requestDetails.message" placeholder="@lang('messages.type_message_to_host')"></textarea>
					<label for="cancelMessage"> @lang('messages.type_message_to_host') </label>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.close') </button>
				<button class="btn btn-primary" v-on:click="requestAction('cancel_request');" :disabled="requestDetails.reason == '' || requestDetails.message == ''"> @lang('messages.cancel') </button>
			</div>
		</div>
	</div>
</div>
<!-- Cancel Request Popup -->
@endif
@endif
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content p-3" :class="{'loading' : isLoading}">
			{!! Form::open(['url' => '#', 'class' => 'form-horizontal','id'=>'common-delete-form','method' => "DELETE"]) !!}
			<div class="modal-header">
				<h5 class="modal-title fw-bold"> @lang('admin_messages.confirm_delete') </h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-4">
				<p> @lang('admin_messages.this_process_is_irreverible') </p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-bs-dismiss="modal"> @lang('admin_messages.cancel') </button>
                <button type="submit" class="btn btn-danger"> @lang('admin_messages.proceed') </button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endauth

@if(!isset($exception) && Route::currentRouteName() == 'host_calendar')
<!-- import Calendar Modal Start -->
<div class="modal fade" id="importCalendarModal" tabindex="-1" aria-labelledby="importCalendarModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			{!! Form::open(['url' => resolveRoute('import_calendar'), 'name' => 'import-calendar']) !!}
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.listing.import_new_calendar') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-3">
				<p> @lang('messages.import_calendar_desc') </p>
				<input type="hidden" name="hotel_id" :value="hotel_id">
				<div class="form-group">
					<label class="form-label"> @lang('messages.custom_name_for_calendar') </label>
					<input type="text" value="{{ old('calendar_name','') }}" name="calendar_name" placeholder="@lang('messages.custom_name_for_calendar')" class="form-control">
					<span class="text-danger">
						{{ $errors->first('calendar_name') }}
					</span>
				</div>
				<div class="form-group">
					<label class="form-label"> @lang('messages.url_of_the_calendar') </label>
					<input type="text" value="{{ old('calendar_url','') }}" name="calendar_url" placeholder="@lang('messages.url_of_the_calendar')" class="form-control">
					<span class="text-danger"> {{ $errors->first('calendar_url') }} </span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary ms-auto">
					@lang('messages.import_calendar')
				</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<!-- import Calendar Modal End -->
<!-- Export Calendar Modal Start -->
<div class="modal fade" id="exportCalendarModal" tabindex="-1" aria-labelledby="exportCalendarModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.export_calendar') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-3">
				<p> @lang('messages.copy_paste_calendar_link') </p>
				<input type="text" class="form-control" value="{{ resolveRoute('export_calendar',['id' => $room->id]) }}" readonly>
			</div>
		</div>
	</div>
</div>
<!-- Export Calendar Modal End -->
<!-- Remove Calendar Modal Start -->
<div class="modal fade" id="removeCalendarModal" tabindex="-1" aria-labelledby="removeCalendarModal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			{!! Form::open(['url' => resolveRoute('remove_calendar'), 'id'=>'remove-calendar-form']) !!}
			<div class="modal-header justify-content-center border-bottom">
				<h3> @lang('messages.remove_calendar') </h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body py-3">
				@forelse ($imported_calendars as $calendar)
				<div class="form-check">
					<input type="checkbox" name="calendar[]" id="calendar_{{ $calendar->id }}" class="form-check-input" value="{{ $calendar->id }}">
					<label class="form-check-label h4" for="calendar_{{ $calendar->id }}"> {{ $calendar->name }} </label>
				</div>
				@empty
				<p> @lang('messages.no_calendar_synced') </p>
				@endforelse
			</div>
			<div class="modal-footer d-flex">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal" aria-label="Close">
					@lang('messages.close')
				</button>
				@if($imported_calendars->count() > 0)
				<button type="submit" class="btn btn-primary ms-auto">
					@lang('messages.remove_selected')
				</button>
				@endif
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<!-- Remove Calendar Modal End -->
@endif