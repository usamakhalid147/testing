@extends('layouts.app')
@section('sidebar')
<div class="border hotel-price-info px-4 pb-4">
	<div class="col-md-12 mt-4">
		<div class="mb-1">
			{!! $hotel->getHotelRatings() !!}
			<h4 class="text-truncate-2"> 
				<a href="{{ resolveRoute('hotel_details',['id' => $hotel->id]) }}">{{ $hotel->name }} </a></h4>
		</div>
		<div class="hotel-image-container w-100">
			<img class="hotel-image" src="{{ $hotel->image_src }}" alt="{{ $hotel->name }}">
		</div>
		<div class="d-flex mt-2 justify-content-between">
			<div class="">
				<i class="icon icon-location" area-hidden="true"></i>
				<span class="p-1">
					{{ $hotel->hotel_address->city }}
				</span>
			</div>
			<div class="fw-bolder">
				{{ $price_details['currency_symbol'].' '.number_format($price_details['total_price']) }} / @lang('messages.per_nights',['key' => $price_details['total_nights']])
			</div>
		</div>
	</div>
	<div class="line-divider w-100"></div>
	<div class="booking_details col-12 px-0">
		<div class="row mx-0">
			<div class="col-md-12 px-1">
				<div class="hstack gap-3 justify-content-between">
					<div class="">
						<i class="icon icon-room_type" area-hidden="true"></i>
						<span class="p-1">
							{{ $price_details['total_rooms'] }}
							{{ $price_details['total_rooms'] > 1 ? Lang::get('messages.rooms') : Lang::get('messages.room') }}
						</span>
					</div>
					<div class="">
						<i class="icon icon-guests" area-hidden="true"></i>
						<span class="p-1">
							{{ $price_details['total_adults'] }}
							{{ $price_details['total_adults'] > 1 ? Lang::get('messages.adults') : Lang::get('messages.adult') }}
						</span>
					</div>
					<div class="">
						<i class="icon icon-guests" area-hidden="true"></i>
						<span class="p-1">
							{{ $price_details['total_children'] }}
							{{ $price_details['total_children'] > 1 ? Lang::get('messages.children') : Lang::get('messages.child') }}
						</span>
					</div>
				</div>
			</div>
			<div class="line-divider w-100"></div>
			<div class="col-sm-12 p-0">
				<div class="hstack gap-3">
					<div>
						<i class="icon icon-calendar" area-hidden="true"></i>
					</div>
					<div>
						<span> {{ $payment_data['checkin_formatted'] }} - {{ $payment_data['checkout_formatted'] }} </span>
					</div>
				</div>
			</div>
			<div class="line-divider w-100"></div>
		</div>
	</div>
	<div class="pricing-details" :class="{'loading': isLoading}">
		<div v-for="(price_details,index) in pricing_form">
			<div class="row pricing-item mb-2" :class="price_details.class">
				<div class="col-8 d-flex flex-wrap flex-column" style="price_details.key_style">
					<div class="count-detail d-flex" data-bs-toggle="collapse" :data-bs-target="'#otherPrices_'+index">
						<span>@{{ price_details.key }}</span>
						<span v-if="price_details.count">x@{{ ' '+price_details.count }}</span>
						<i class="icon icon-info cursor-pointer ms-2" data-bs-toggle="tooltip" area-hidden="true"  :title="price_details.tooltip" v-if="price_details.tooltip != ''"></i>
						{{--
						<i class="icon icon-info cursor-pointer ms-2"  :aria-controls="'otherPrices_'+index" aria-expanded="false" aria-label="Other Prices"  v-if="price_details.dropdown"></i>
						</i>
						--}}
					</div>
					<div class="detail-persons">
						 @{{ price_details.description }}
					</div>
				</div>
				<div class="col-4 text-end" :style="price_details.value_style"> 
					@{{ price_details.value }} 
				</div>
			</div>
			<div class="collapse navbar-collapse show" v-if="price_details.dropdown">
				<div v-for="dropdown_value in price_details.dropdown_values">
					<div class="card-body box-shadow border">
						<div class="d-flex justify-content-between">
							<div class="">
								@{{ dropdown_value.key }}
							</div>
							<div class="">
								<span v-if="dropdown_value.prefix != ''">@{{dropdown_value.prefix}}</span>
								{{ session('currency_symbol')}} @{{ new Intl.NumberFormat().format(dropdown_value.value) }}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('hotel-rules')
<h2 class="mb-4 fw-bold"> @lang('messages.hotel_rules') </h2>
<div class="checkin-info">
	@if($hotel_rules != '' )
	<p class="fw-bold h5 mt-4 mb-2">
		@lang('messages.things_keep_in_mind')
	</p>
	<div class="row p-2">
		<p> {{ $hotel_rules }} </p>
	</div>
	@endif
</div>
<div class="line-divider w-100"></div>
@endsection
@section('whos-coming')
<div class="guests-info">
	<div class="host-welcome-message my-4 d-flex">
		<div>
			<p class="fw-bold h5"> @lang('messages.say_hello_to_host') </p>
			<span> @lang('messages.let_host_know_about_yourself',['replace_key_1' => $hotel->user->first_name]) </span>
		</div>
		<div class="host-profile ms-auto">
			<img src="{{ $hotel->user->profile_picture_src }}" class="img img-icon-medium img-rounded">
		</div>
	</div>
	<div class="send-host-message">
		<textarea name="message" class="w-100" placeholder="@lang('messages.cant_wait_to_spend_hours_in_your_place',['replace_key_1' => $hotel->user->first_name,'replace_key_2' => $price_details['total_days']])" rows="4" v-model="user_message"></textarea>
	</div>
</div>
<div class="line-divider w-100"></div>
@endsection
@section('instant_book')
{!! Form::open(['url' => resolveRoute('payment.complete'), 'class' => 'payment-form','id'=>'payment-form']) !!}
{!! Form::hidden('booking_attempt_id',$booking_attempt_id,['id' => 'booking_attempt_id']) !!}
{!! Form::hidden('hotel_id',$hotel_id) !!}
{!! Form::hidden('hotel_sub_room_id','') !!}
{!! Form::hidden('booking_type','instant_book',['id'=>"booking_type"]) !!}
{!! Form::hidden('payment_method',null,['v-model' => 'payment_method']) !!}
{!! Form::hidden('stripe_token','',['id' => 'stripe-token']) !!}
{!! Form::hidden('stripe_payment_intent',null,['id' => 'stripe-intent_id']) !!}
{!! Form::hidden('card_id','',['id'=>"card_id"]) !!}
{!! Form::hidden('pi_client_secret',session('payment.'.$booking_attempt_id.'.client_secret'),['id' => 'pi_client_secret']) !!}
{!! Form::hidden('message',null,['v-model' => 'user_message']) !!}
<ul class="nav nav-pills gap-4 payment-pills" id="pills-tab" role="tablist">
	@foreach($payment_methods as $payment)
	<li class="nav-item" role="presentation">
		<a href="#" class="px-4 py-2 d-inline-block rounded-3 border-0" id="pills-{{ $payment }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $payment }}" role="tab" aria-controls="pills-{{ $payment }}" aria-selected="true" v-on:click="payment_method = '{{ $payment }}'" :class="'{{ $payment }}' == payment_method ? 'active' : ''">@lang('messages.'.$payment)</a>
	</li>
	@endforeach
</ul>
<div class="tab-content my-3" id="pills-tabContent">
	<div class="tab-pane fade {{ $default_payment_method == 'pay_at_hotel' ? 'active show' : '' }}" id="pills-pay_at_hotel" role="tabpanel" aria-labelledby="pills-pay_at_hotel-tab" tabindex="0">
	</div>
	<div class="tab-pane fade {{ $default_payment_method == 'stripe' ? 'active show' : '' }}" id="pills-stripe" role="tabpanel" aria-labelledby="pills-stripe-tab" tabindex="0">
		<div v-show="saved_payment_method == ''">
			<div id="card-element"></div>
			<div id="card-errors" class="text-danger" role="alert"></div>
			<div class="form-check mt-3 ms-2">
				<input type="checkbox" name="save_for_future_use" id="save_for_future_use" value="1" class="form-check-input">
				<label class="form-check-label" for="save_for_future_use"> @lang('messages.save_card_for_future_use') </label>
			</div>
		</div>
	</div>
</div>
<div class="payment-info" v-show="!isLoading">
	<div v-show="payment_method == 'stripe'">
		@if($saved_cards->count())
		<select name="saved_payment_method" v-model="saved_payment_method" class="form-select mb-3">
			<option value=""> @lang('messages.enter_card_details') </option>
			@foreach($saved_cards as $saved_card)
				<option value="{{ $saved_card->payment_method }}"> {{ $saved_card->formatted_card }} </option>
			@endforeach
		</select>
		@endif
		
	</div>
</div>
<div class="line-divider w-100"></div>

<div class="coupon-code" v-if="coupon_code.couponApplied">
	<a href="javascript:;" class="ms-2" v-on:click="validateCoupon('remove')"> 
		<span class="text-success px-2">@{{ coupon_code.code }}! @lang('messages.applied')</span><span v-if="!isLoading">@lang('messages.remove')</span>
	</a>
</div>
<div class="form-group">
    <label for="special_request" class="form-label">Special Request</label>
    <textarea name="special_request" class="form-control" id="special_request" rows="4" placeholder="Enter Special Request"></textarea>
</div>
<div class="coupon-code" v-else>
	<div>
		<p class="mb-0"> @lang('messages.enter_a_coupon') </p>
		<div class="d-flex align-items-center">
			<input type="text" name="coupon_code" class="form-control w-50" v-model="coupon_code.code">
			<a href="javascript:;" class="mx-2 btn btn-primary" v-on:click="validateCoupon('apply')"> @lang('messages.apply')</a>
		</div>
		<p class="mx-1 text-danger"> @{{ coupon_code.status_message }} </p>
	</div>
	@foreach($available_coupons as $coupon_code)
	<div>
		<span class="px-1 fw-normal">{{ $coupon_code['display_text'] }}</span>
	</div>
	@endforeach
</div>
<div class="line-divider w-100"></div>
@if(displayCrendentials())
<div class="test-data">
	<p>
		Test Note : For Complete Booking Use Test Card 4000000000000077 with any cvv.
	</p>
</div>
@endif
<div class="hotel-policy">
	<p class="fw-bolder"> @lang('messages.hotel_policy'): </p>
	<p>
		{{ $hotel->hotel_policy }}
	</p>
</div>
<div class="cancellation-policy">
	<p>
		<span class="fw-bolder"> @lang('messages.cancellation_policy'): </span>
	</p>
	@foreach($hotel_rooms as $hotel_room)
		@lang('messages.room_name')
		:<span class="fw-bold">
		{{ $hotel_room->name }}</span>
		<div class="col-md-12">
			@foreach($hotel_room->cancellation_policies as $cancellation_policy)
				<p>{{$cancellation_policy->days}}
				   @lang('messages.days')
				   @lang('messages.before_checkin_date'):
				   {{$cancellation_policy->percentage}}
				   <span class="">%</span>
			    </p>
			@endforeach
		</div>
	@endforeach
</div>

<div class="line-divider w-100"></div>
<div class="terms-and-conditions">
	@lang('messages.by_selecting_buttons_below'),
	@lang('messages.i_agree_the')
	@foreach($agree_pages as $page)
		<a href="{{ $page->url }}" target="_blank" class="primary-link"> {{ $page->name }} </a>
		@if ($loop->last)
			<span>.</span>
		@else
			<span>,</span>
		@endif
	@endforeach
	<div v-show="payment_method == 'pay_at_hotel'">
		@if(checkEnabled('ReCaptcha') && credentials('version','ReCaptcha') == '2')
		<div class="recaptcha-container mt-2">
			<div class="g-recaptcha" data-sitekey="{{ credentials('site_key','ReCaptcha') }}"></div>
		</div>
		@endif
		@if($errors->has('g-recaptcha-response'))
		<span class="text-danger"> {{ $errors->first('g-recaptcha-response') }} </span>
		@endif
	</div>

	@lang('messages.by_clicking_the_agree_and_continue_button_below',['replace_key_1' => $site_name])
</div>
<div class="confirm-section mt-4" v-show="payment_method != 'paypal'">
	<button type="button" class="btn btn-primary" :disabled="isLoading" v-on:click="nextStep()"> @lang('messages.agree_and_continue') </button>
</div>
{!! Form::close() !!}
@endsection
@section('content')
<main role="main" class="main-container">
	<section class="container py-4 px-0 px-md-2">
		<div class="row mx-2 mx-lg-0 justify-content-between">
			<div class="col-lg-4 order-lg-2 px-0 px-lg-2 mb-3 mb-lg-0">
				@yield('sidebar')
			</div>
			<div class="col-lg-7 card px-3 px-md-5 py-4 order-lg-1">
				@yield('hotel-rules')
				<!-- @yield('whos-coming') -->
				@yield('instant_book')
			</div>
		</div>
	</section>
</main>
@endsection
@push('scripts')
	<script type="text/javascript">
		window.vueInitData = {!! json_encode([
			"payment_data" => $payment_data,
			"pricing_form" => $pricing_form,
			"paypal_purchase_data" => $paypal_purchase_data,
			"booking_type" => 'instant_book',
			"payment_method" => $default_payment_method,
		]) !!};
	</script>
	@if($price_details['coupon_price'] > 0)
	<script type="text/javascript">
		window.vueInitData['coupon_code'] = {!!
			json_encode([
				'showInput' => false,
	            'couponApplied' => true,
	            'code' => $price_details['coupon_code'],
	            'status_message' => '',
			]);
		!!}
	</script>
	@endif
	<script src="https://js.stripe.com/v3/"></script>
@endpush