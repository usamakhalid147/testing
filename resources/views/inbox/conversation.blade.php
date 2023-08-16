@extends('layouts.app')
@section('guest_header')
	@if($reservation->status == 'Accepted')
		<p class="h4"> @lang('messages.booking_is_confirmed') {{ $list_location->address_line_display }}. </p>
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ $reservation->itinerary_link }}"> @lang('messages.view_itinerary') </a>
			<a class="btn border border-dark ms-2" href="javascript:;" v-on:click="can_share_itinerary=true;" v-show="!can_share_itinerary"> @lang('messages.share_itinerary') </a>
			<a class="btn border border-dark ms-2" href="javascript:;" v-on:click="can_share_itinerary=false;" v-show="can_share_itinerary"> @lang('messages.cancel') </a>
			<div v-show="can_share_itinerary">
				<div class="card-body m-3">
					<div class="form-group d-flex">
						<label> @lang('messages.email') <span class="text-danger">*</span> </label>
						<input type="text" class="ms-3 form-control" name="email" id="email" v-model="email">
						<input type="hidden" name="reservation_id" id="reservation_id" v-model="reservation_id">
						<span class="text-danger" v-show="error_messages.email">@lang('messages.email_field_is_required')
						</span>
					</div>
				</div>
				<div class="card-action">
					<button class="btn btn-primary float-end" v-on:click="shareItinerary()">@lang('messages.submit')</button>
				</div>
			</div>
		</div>
	@elseif($reservation->status == 'Cancelled')
		@if($reservation->cancelled_by == 'Guest')
			<p class="h4"> @lang('messages.you_cancelled_this_booking') </p>
		@else
			<p class="h4"> @lang('messages.host_cancelled_this_booking') </p>
		@endif
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ resolveRoute('search',['list_type' => $message->list_type]) }}"> @lang('messages.keep_searching') </a>
		</div>
	@elseif($reservation->status == 'Pending')
		<p class="h4"> @lang('messages.request_sent_to',['replace_key_1' => ucfirst($reservation->host_user->first_name)]) </p>
		<div class="mt-3">
			<button type="button" class="btn btn-primary" v-on:click="openRequestPopup('cancelRequest');"> @lang('messages.cancel_request') </button>
		</div>
	@elseif($reservation->status == 'Inquiry')
		<p class="h4"> @lang('messages.inquiry_sent_to',['replace_key_1' => ucfirst($reservation->host_user->first_name)]) </p>
	@elseif($reservation->status == 'Expired')
		@if($reservation->expired_on == 'Host')
			<p class="h4"> @lang('messages.host_not_responds_to_your_request') </p>
		@else
			<p class="h4"> @lang('messages.you_unable_to_complete_this_booking') </p>
		@endif
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ resolveRoute('search',['list_type' => $message->list_type]) }}"> @lang('messages.keep_searching') </a>
		</div>
	@elseif($reservation->status == 'Declined')
		<p class="h4"> @lang('messages.declined_your_request') </p>
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ resolveRoute('search',['list_type' => $message->list_type]) }}"> @lang('messages.keep_searching') </a>
		</div>
	@elseif($message->hasValidSpecialOffer())
		<p class="h4"> @lang('messages.your_host_sent_special_offer_to_you',['replace_key_1' => $reservation->host_user->first_name,'replace_key_2' => $message->special_offer->currency_symbol.''.round($message->special_offer->day_price)]) </p>
		<div class="my-3 row">
			<div class="col-md-4">
				<div class="my-1">
					<span> @lang('messages.listing_name') </span>
				</div>
				<a class="" href="{{ resolveRoute('room_details',['id' => $message->special_offer->room_id]) }}">
					{{ $message->special_offer->room->name }}
				</a>
			</div>

			<div class="col-md-4">
				<div class="my-1">
					<span> @lang('messages.dates') </span>
				</div>
				<h5> {{ $message->special_offer->dates }} </h5>
			</div>

			<div class="col-md-2">
				<div class="my-1">
					<span> @lang('messages.guests') </span>
				</div>
				<h5> {{ $message->special_offer->guests }} </h5>
			</div>

			<div class="col-md-2">
				<div class="my-1">
					<span> @lang('messages.special_offer_price') </span>
				</div>
				<h5> {{ $message->special_offer->currency_symbol.''.$message->special_offer->price }} </h5>
			</div>
		</div>
		<div class="mt-3">
			<a href="{{ resolveRoute('confirm_reserve',['reservation_id' => $reservation->id,'special_offer_id' => $message->special_offer_id]) }}" class="btn btn-primary"> @lang('messages.accept_special_offer') </a>
		</div>
	@elseif($reservation->status == 'Pre-Approved')
		<p class="h4"> @lang('messages.pre_approved_your_request',['replace_key_1' => $reservation->host_user->first_name]) </p>
		<div class="mt-3">
			@if($list_type == 'experience')
			<a href="{{ resolveRoute('choose_experience_date',['reservation_id' => $reservation->id]) }}" class="btn btn-primary"> @lang('messages.book_now') </a>
			@endif
			@if($list_type == 'hotel')
			<a href="{{ resolveRoute('confirm_reserve',['reservation_id' => $reservation->id]) }}" class="btn btn-primary"> @lang('messages.book_now') </a>
			@endif
		</div>
	@elseif($reservation->not_available)
		<p class="h4"> @lang('messages.someone_booked_already') </p>
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ resolveRoute('search',['list_type' => $message->list_type]) }}"> @lang('messages.keep_searching') </a>
		</div>
	@elseif($reservation->status == 'Pre-Accepted')
		<p class="h4"> @lang('messages.pre_accepted_your_request',['replace_key_1' => $reservation->host_user->first_name]) </p>
		<div class="mt-3">
			<a href="{{ resolveRoute('confirm_reserve',['reservation_id' => $reservation->id]) }}" class="btn btn-primary"> @lang('messages.book_now') </a>
		</div>
	@elseif($reservation->status == 'Completed')
		<p class="h4"> @lang('messages.this_booking_is_completed') </p>
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ resolveRoute('search',['list_type' => $message->list_type]) }}"> @lang('messages.find_another') </a>
		</div>
	@endif
@endsection
@section('host_header')
	@if($reservation->status == 'Accepted')
		<p class="h5"> @lang('messages.booking_confirmed_by_guest') @lang('messages.we_recommend_communication_in_site',['replace_key_1' => $site_name]) </p>
		<div class="mt-3">
			<a class="btn border border-dark" href="{{ $reservation->itinerary_link }}"> @lang('messages.view_itinerary') </a>
		</div>
	@elseif($reservation->status == 'Cancelled')
		@if($reservation->cancelled_by == 'Host')
			<p class="h4"> @lang('messages.you_cancelled_this_booking') </p>
		@else
			<p class="h4"> @lang('messages.guest_cancelled_this_booking') </p>
		@endif
	@elseif($reservation->status == 'Pending')
		<p class="h4"> @lang('messages.requested_to_book',['replace_key_1' => ucfirst($reservation->user->first_name)]) </p>
		<p class="h6"> @lang('messages.your_calendar_is_still_open') </p>
		<p class="h6">
			@lang('messages.respond_within')
			<timer class="mx-1" deadline="{{ $reservation->expired_at }}"></timer>@lang('messages.to_maintain_your_response_rate')
		</p>
		<div class="mt-3">
			<button type="button" class="btn btn-primary me-2" v-on:click="openRequestPopup('preAccept');"> @lang('messages.pre_accept') </button>
			<button type="button" class="btn btn-default me-2" v-on:click="openRequestPopup('decline');"> @lang('messages.decline') </button>
			@if($list_type != 'experience')
			<button type="button" class="btn btn-success me-2" v-on:click="showOfferForm=true;"> @lang('messages.send_special_offer') </button>
			@endif
		</div>
	@elseif($reservation->status == 'Inquiry')
		<p class="h4"> @lang('messages.invite_guest_to_book',['replace_key_1' => $reservation->user->first_name]) </p>
		<p> @lang('messages.your_calendar_is_still_open') </p>
		<div class="mt-3">
			<button type="button" class="btn btn-primary me-2" v-on:click="openRequestPopup('preApprove');"> @lang('messages.pre_approve') </button>
			@if($list_type != 'experience')
			<button type="button" class="btn btn-success me-2" v-on:click="showOfferForm=true;"> @lang('messages.send_special_offer') </button>
			@endif
		</div>
	@elseif($reservation->status == 'Pre-Accepted')
		<p class="h4"> @lang('messages.you_pre_accepted_request',['replace_key_1' => $reservation->user->first_name]) </p>
		<p> @lang('messages.your_calendar_is_still_open') </p>
		<div class="mt-3">
			@if($list_type != 'experience')
			<button type="button" class="btn btn-success" v-on:click="showOfferForm=true;"> @lang('messages.send_special_offer') </button>
			@endif
			@if($message->special_offer_id != null)
			<button type="button" class="btn btn-danger" v-on:click="removeSpecialOffer();">@lang('messages.remove') @lang('messages.special_offer_price')</button>
			@endif
		</div>
	@elseif($reservation->status == 'Pre-Approved')
		<p class="h4"> @lang('messages.you_pre_approved_request',['replace_key_1' => $reservation->user->first_name]) </p>
		<p> @lang('messages.your_calendar_is_still_open') </p>
		<div class="mt-3">
			<button type="button" class="btn btn-primary" v-on:click="requestAction('decline')"> @lang('messages.remove_pre_approval') </button>
			@if($list_type != 'experience')
			<button type="button" class="btn btn-success" v-on:click="showOfferForm=true;"> @lang('messages.send_special_offer') </button>
			@endif
			@if($message->special_offer_id != null)
			<button type="button" class="btn btn-danger" v-on:click="removeSpecialOffer();">@lang('messages.remove') @lang('messages.special_offer_price')</button>
			@endif
		</div>
	@elseif($reservation->status == 'Expired')
		@if($reservation->expired_on == 'Host')
			<p class="h4"> @lang('messages.request_expired_host',['replace_key_1' => $reservation->user->first_name]) </p>
		@else
			<p class="h4"> @lang('messages.request_expired_guest',['replace_key_1' => $reservation->user->first_name]) </p>
		@endif
	@elseif($reservation->status == 'Declined')
		<p class="h4"> @lang('messages.you_declined_this_request') </p>
	@elseif($reservation->status == 'Completed')
		<p class="h4"> @lang('messages.this_booking_is_completed') </p>
	@endif
	<!-- Special Offer Form -->	
	@if($list_type == 'hotel' && !in_array($reservation->status,['Accepted','Completed']))
	<div class="card mt-3 text-start" v-show="showOfferForm">
		<div class="card-header">
			<h4> @lang('messages.send_user_a_special_offer',['replace_key_1' => $reservation->user->first_name]) </h4>
			<p class="text-muted"> @lang('messages.your_calendar_will_remain_open',['replace_key_1' => $reservation->user->first_name]) </p>
		</div>
		<div class="card-body" :class="{'loading':isLoading}">
			<div class="form-group">
				<label class="form-label"> @lang('messages.listing') </label>
				<select class="form-select" name="listing" v-model="offerDetails.listing">
					<option value="{{ $reservation->room_id }}"> {{ $reservation->room->name }} </option>
				</select>
			</div>
			<div class="row">
				<div class="col-6 form-group">
					<label class="form-label"> @lang('messages.checkin') </label>
					<input type="text" name="checkin" id="offer_checkin" class="form-control" v-model="offerDetails.checkin" readonly>
				</div>
				<div class="col-6 form-group">
					<label class="form-label"> @lang('messages.checkout') </label>
					<input type="text" name="checkout" id="offer_checkout" class="form-control" v-model="offerDetails.checkout" readonly>
				</div>
			</div>
			<div class="form-group">
				<label class="form-label"> @lang('messages.guests') </label>
				<select class="form-select" name="guests" v-model="offerDetails.guests">
					<option value="{{ $reservation->guests }}"> {{ $reservation->guests }} </option>
				</select>
			</div>
			<div class="row border mx-1 my-3 px-3 py-4 align-items-end">
				<div class="col-md-3">
					<div class="form-group">
						<label class="form-label"> @lang('messages.sub_total') </label>
						<div class="input-group mb-3">
						  	<span class="input-group-text"> {{ $reservation->currency_symbol }} </span>
							<input type="text" name="price" class="form-control" v-model="offerDetails.price" v-on:change="calculatePrice()">
							<p class="text-danger"> @{{ status_message }}</p>
							<p class="text-danger"> @{{ this.offerDetails.error_message }}</p>
						</div>
					</div>
				</div>
				<div class="col-md-9">
					<p> @lang('messages.enter_a_subtotal_includes_any_extra_fee')@lang('messages.this_wont_include_service_fees') </p>
				</div>
			</div>
			<div class="pricing-details my-4 py-3 border-bottom">
				<div class="row pricing-item" v-if="offerDetails.show_price_details" v-for="price_details in offerDetails.pricing_form" :class="price_details.class">
					<div class="col-8" :style="price_details.key_style">
						@{{ price_details.key }}
						<i class="icon icon-info cursor-pointer" data-bs-toggle="tooltip" area-hidden="true" :title="price_details.tooltip" v-if="price_details.tooltip != ''"></i>
					</div>
					<div class="col-4 text-end" :style="price_details.value_style"> @{{ price_details.value }} </div>
				</div>
			</div>
			<div class="d-flex">
				<a href="javascript:;" class="cancel-offer" v-on:click="showOfferForm=false;"> @lang('messages.back') </a>
				@if($list_type != 'experience')
				<button class="btn btn-primary ms-auto" v-on:click="sendSpecialOffer();" :disabled="!offerDetails.is_available"> @lang('messages.send_special_offer') </button>
				@endif
			</div>
		</div>		
	</div>
	@endif
	<!-- Special Offer Form -->	
@endsection
@section('content')
<main id="conversation" role="main" class="main-container pt-4">
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<div class="card mt-1">
					<div class="card-body card-img-top">
						<a href="{{ resolveRoute('view_profile',['id' => $user_details->id]) }}">
							<img src="{{ $user_details->profile_picture_src }}" class="mx-auto d-block img-rounded img-fluid conversation-profile-image mt-1"/>
						</a>
						<div class="text-center">
							<h5 class="fw-bolder mt-4"> {{ $user_details->first_name }} </h5>
							<p> {{ $user_details->location }}
								<span class="d-block"> {{ $user_details->user_verification->verified_count }} <span class="text-muted"> @lang('messages.verifications') </span></span>
							</p>
						</div>
						<div class="reserve-details mt-2">
							<p class="h6 fw-bold"> @lang('messages.reserve_details') </p>
							<div class="dropdown-divider my-3"></div>
							<a href="{{ $list->link }}" class="link h5 list-{{ $message->list_type }}">
								{{ $list->name ?? $list->title }}
							</a>
							<div class="dropdown-divider my-2"></div>
							<div class="reserve-dates row">
								<div class="col-md-6">
									<span class="text-muted"> @lang('messages.checkin') </span>
									<div class="fw-bold">
										<div class="d-block">
											<span> {{ $reservation->formatted_checkin }} </span>
										</div>
									</div>
								</div>
								@if($message->list_type == 'hotel')
								<div class="col-md-6">
									<span class="text-muted"> @lang('messages.checkout') </span>
									<div class="fw-bold">
										<div class="d-block">
											<span> {{ $reservation->formatted_checkout }} </span>
										</div>
									</div>
								</div>
								@else
								<div class="col-md-6">
									<span class="text-muted"> @lang('messages.listing.timing') </span>
									<div class="fw-bold">
										<div class="d-block">
											<span> {{ $reservation->times }} </span>
										</div>
									</div>
								</div>
								@endif
							</div>
							<div class="dropdown-divider my-2"></div>
							<div class="reserve-guests">
								<div class="d-flex justify-content-between">
									<div>
										<span class="text-muted"> @lang('messages.rooms') </span>
										<div class="fw-bold text-center">
											<span> {{ $reservation->total_rooms }} </span>
										</div>
									</div>
									<div>
										<span class="text-muted"> @lang('messages.adults') </span>
										<div class="fw-bold text-center">
											<span> {{ $reservation->adults }} </span>
										</div>
									</div>
									<div>
										<span class="text-muted"> @lang('messages.children') </span>
										<div class="fw-bold text-center">
											<span> {{ $reservation->children }} </span>
										</div>
									</div>
								</div>
							</div>
							<div class="dropdown-divider my-2"></div>
							<div class="payment-details">
								<span class="text-muted"> @lang('messages.payments') </span>
								@foreach($pricing_data as $price_details)
								<div class="row pricing-item mb-2 {{ $price_details['class'] }}">
									<div class="col-8 mt-1 {{ $price_details['key_style'] }}" style="{{ $price_details['key_style'] }}">
										<div class="count-detail d-flex">
											<span>{{ $price_details['key'] }}</span>
											@if($price_details['count'])
											<span>x {{ $price_details['count'] }}</span>
											@endif
											@if($price_details['tooltip'])
												<i class="icon icon-info cursor-pointer d-print-none ms-1" data-bs-toggle="tooltip" title="{{ $price_details['tooltip'] }}" area-hidden="true"></i>
											@endif
										</div>
										@if($price_details['description'])
										<div class="detail-persons">
											 {{ $price_details['description'] }}
										</div>
										@endif
									</div>
									<div class="col-4 text-end" style="{{ $price_details['value_style']}}"> {{ $price_details['value'] }} </div>
								</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="border bg-white p-4 mt-md-1 mt-3 text-center">
					@yield(strtolower($user_type).'_header')
					{{--DisputeCommentStart--}}
					@if($message->list_type == 'hotel' && $reservation->canApplyToDispute())
					<div class="mt-3">
						<button class="btn btn-dark btn-outline-light border" data-bs-toggle="modal" data-bs-target="#applyDisputeModal"> @lang('messages.dispute.apply_for_dispute') </button>
					</div>
					@endif
					{{--DisputeCommentEnd--}}
				</div>
				<div class="row my-3 mt-4">
					<div class="col-9">
						<div class="card" :class="{'loading': isLoading}">
							<div class="card-body form-group">
								<textarea class="form-control" rows="3" name="message" v-model="message"></textarea>
								<p class="text-danger"> @{{ error_messages.inbox_message }} </p>
								<div class="mt-3 text-end">
									<button class="btn btn-primary" :disabled="message == ''" v-on:click="sendMessage"> @lang('messages.send_message') </button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-3 ps-0">
						<img class="img-fluid rounded-profile-image" src="{{ Auth::user()->profile_picture_src }}">
					</div>
				</div>
				<inbox-chat :messages="chat_messages" :user="{{ $auth_user }}" :other_user="{{ $user_details }}"> </inbox-chat>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script>
    window.vueInitData = {!! json_encode([
        'user_type' => $user_type,
        'chat_messages' => $messages,
        'message_id' => $message->id,
        'reservation_id' => $message->reservation_id,
		'offerDetails' => $offer_details ?? [],
		'list_type' => $list_type,
    ]) !!};
    @if($list_type == 'experience')
    routeList['request_action'] = "{!! resolveRoute('experience_request_action') !!}";
    @endif
</script>
@endpush