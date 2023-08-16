@extends('layouts.hostLayout.app')
@section('content')
<div class="content conversation">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $sub_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('host.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-itfem">
					<a href="{{ route('host.messages') }}">@lang("admin_messages.messages")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.conversation")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card text-center">
					<div class="card-body">
						@if($reservation->status == 'Accepted')
							<p class="h5"> @lang('messages.booking_confirmed_by_guest') @lang('messages.we_recommend_communication_in_site',['replace_key_1' => $site_name]) </p>
							<div class="mt-3">
								<a class="btn border border-dark" href="{{ $reservation->itinerary_link }}"> @lang('messages.view_itinerary') </a>
								<a class="btn border border-dark ms-2" href="{{ route('host.reservations.show',['id' => $reservation->id]) }}"> @lang('admin_messages.view_details') </a>
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
					</div>
				</div>	
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<div class="row px-3">
					<div class="card">
						<div class="card-body form-group">
							<textarea class="form-control" rows="3" name="message" v-model="message"></textarea>
							<p class="text-danger"> @{{ error_messages.inbox_message }} </p>
							<div class="mt-3 text-end">
								<button class="btn btn-primary" :disabled="message == ''" v-on:click="sendMessage()"> @lang('messages.send_message') </button>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="conversations">
						<div class="conversations-body">
							<div class="conversations-content bg-white">
								<div class="message-content-wrapper" v-for="conversation in chat_messages">
									<div class="message d-flex" :class="{'message-in': conversation.user_from != auth_user.id, 'message-out': conversation.user_from == auth_user.id}">
										<div class="avatar avatar-sm ms-auto" v-if="conversation.user_from != auth_user.id">
											<img :src="conversation.profile_picture_src" class="avatar-img rounded-circle border border-white">
										</div>
										<div class="message-body">
											<div class="message-content">
												<div class="name"> @{{ conversation['user_name'] }} </div>
												<div class="content">
													@{{ conversation['message'] }}
												</div>
											</div>
											<div class="date"> @{{ conversation.sent_at }} </div>
										</div>
										<div class="avatar avatar-sm ms-auto" v-if="conversation.user_from == auth_user.id">
											<img :src="conversation.profile_picture_src" class="avatar-img rounded-circle border border-white">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="card mx-3">
					<div class="card-header">
						<p class="h3 fw-bold"> @lang('messages.reserve_details') </p>
					</div>
					<div class="card-body">
						<div class="card-body card-img-top">
						<a href="{{ resolveRoute('view_profile',['id' => $user_details->id]) }}">
							<img src="{{ $user_details->profile_picture_src }}" class="mx-auto d-block rounded-circle img-fluid conversation-profile-image mt-1"/>
						</a>
						<div class="text-center">
							<h5 class="fw-bolder mt-4"> {{ $user_details->first_name }} </h5>
							<p> {{ $user_details->location }}
								<span class="d-block"> {{ $user_details->user_verification->verified_count }} <span class="text-muted"> @lang('messages.verifications') </span></span>
							</p>
						</div>
						<div class="reserve-details mt-2">
							<div class="dropdown-divider my-3"></div>
							<a href="{{ $list->link }}" class="link h5 list-{{ $result->list_type }}">
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
								@if($result->list_type == 'hotel')
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
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script>
	routeList['send_message'] = {!! json_encode(route('host.messages.update',['id' => $result->id])) !!}

    window.vueInitData = {!! json_encode([
    	'auth_user' => $auth_user,
    	'chat_messages' => $conversations,
        'message_id' => $result->id,
        'reservation_id' => $result->reservation_id,
		'list_type' => $list_type,
		'user_type' => $user_type,
		// 'offerDetails' => $offer_details ?? [],
    ]) !!};
</script>
@endpush