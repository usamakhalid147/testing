@extends('layouts.app')
@section('content')
<main role="main" id="main_container">
	<div class="hotel_image-container pt-3 mb-4">
		<ul id="image-gallery" class="image-gallery p-0 w-md-100 d-md-flex flex-wrap flex-column">
			@foreach($hotel->hotel_photos as $hotel_photo)
			<li data-thumb="{{ $hotel_photo->image_src }}" data-src="{{ $hotel_photo->image_src }}" >
				<img src="{{ $hotel_photo->image_src }}" title="{{ $hotel_photo->description }}">
				<div class="caption_{{ $hotel_photo->id }}">
					<p> {{ $hotel_photo->description }} </p>
				</div>
			</li>
			@endforeach
		</ul>
		{{--
		@if($coupon != '')
		<h3>
			<span class="p-1">@lang('messages.apply_coupon')</span>
			<span class="p-1">{{ $coupon->code }}</span>
			<span class="p-1">@lang('messages.to_get')</span>
			@if($coupon->type == 'percentage')
			<span class="p-1">{{ $coupon->value }}%</span>
			@else
			<span class="p-1">{{ $coupon->currency_symbol.(int)$coupon->amount }}</span>
			@endif
			<span class="p-1">@lang('messages.off')</span>
		</h3>
		@endif
		--}}
	</div>
	<div class="row">
		<div class="col me-0 me-md-5">
			<div class="hotel-title-header mt-2 mb-3 hotel_rooms_form">
				<div class="d-flex justify-content-between align-items-center">
					<h3 id="conveythis-do-not-translate-1" class="text-truncate-2 text-black fw-bold mb-0"> {{ $hotel->name }} </h3>
					<div class="review-star flex-shrink-0 ms-2"> {!! $hotel->getHotelRatings() !!}</div>
				</div>
				<div class="align-items-center d-flex mt-1 mt-md-0">
					<div class="hotel-subheader d-flex align-items-center">
						<div class="text-muted"> {{ $hotel->hotel_address->address_line_display }} </div>
					</div>
					<div class="d-flex ms-auto">
						<a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#shareModal">
							<i class="icon icon-share" area-hidden="true"></i>
							<span class="ms-2 d-none d-md-inline-block"> @lang('messages.share') </span>
						</a>
						<a href="javascript:;" class="ms-2" v-if="is_saved" v-on:click="removeFromWishlist(hotel_id,'hotel')" :disabled="isLoading" :class="{'disabled pointer-none' : isLoading}">
							<i class="icon icon-wishlist-fill" area-hidden="true"></i>
							<span class="ms-2 d-none d-md-inline-block"> @lang('messages.saved') </span>
						</a>
						<a href="#" class="ms-2" data-bs-toggle="modal" data-bs-target="#saveToListModal" v-else v-on:click="getAllWishlists(hotel_id);">
							<i class="icon icon-wishlist" area-hidden="true"></i>
							<span class="ms-2 d-none d-md-inline-block"> @lang('messages.save') </span>
						</a>
					</div>
				</div>
			</div>
			<div class="col-lg-12">
				<div class="sticky-navigation border-bottom fixed-top d-none">
					<div class="full-width d-flex justify-content-between py-3 py-md-0">
						<ul class="list-unstyled m-0 align-items-center d-none d-md-flex">
							<li>
								<a href="#rooms" class="navigation-links"> @lang('messages.rooms') </a>
							</li>
							<li class="mx-2"> <span> · </span>
								<a href="#overview" class="navigation-links"> @lang('messages.overview') </a>
							</li>
							<li class="mx-2"> <span> · </span> </li>
							@if($reviews->count() > 0)
							<li>
								<a href="#reviews" class="navigation-links"> @lang('messages.reviews') </a>
							</li>
							<li class="mx-2"> <span> · </span> </li>
							@endif
							<li>
								<a href="#location" class="navigation-links"> @lang('messages.location') </a>
							</li>
							<li class="mx-2"> <span> · </span> </li>
							<li>
								<a href="#the_host" class="navigation-links"> @lang('messages.the_host') </a>
							</li>
							<li class="mx-2"> <span> · </span> </li>
							<li>
								<a href="#things_to_know" class="navigation-links"> @lang('messages.things_to_know') </a>
							</li>
						</ul>
						<div class="d-block d-md-none">
							<h4 class="text-black fw-bold mb-0"> {{ $hotel->name }} </h4>
						</div>
						<div class="d-flex ms-auto align-items-center">
							<a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#shareModal">
								<i class="icon icon-share me-1" area-hidden="true"></i>
								<span class="d-none d-md-inline-block"> @lang('messages.share') </span>
							</a>

							<a href="javascript:;" class="ms-2" v-if="is_saved" v-on:click="removeFromWishlist(hotel_id)" :disabled="isLoading" :class="{'disabled pointer-none' : isLoading}">
								<i class="icon icon-wishlist-fill me-1" area-hidden="true"></i>
								<span class="d-none d-md-inline-block fw-normal"> @lang('messages.saved') </span>
							</a>
							<a href="#" class="ms-2" data-bs-toggle="modal" data-bs-target="#saveToListModal" v-else v-on:click="getAllWishlists(hotel_id);">
								<i class="icon icon-wishlist me-1" area-hidden="true"></i>
								<span class="d-none d-md-inline-block fw-normal"> @lang('messages.save') </span>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="full-width">
				<div class="detail-wrap flex-column d-flex flex-wrap justify-content-between">
					<div class="col-lg-12 col-12">
						<div id="overview" class="hotel_details-section col-12">
							<div class="overview-list_description mt-3 border-bottom">
								<h4 class="listing_desc_head"> @lang('messages.about') @lang('messages.property') </h4>
								<p class="hotel-content text-justify">
									@if(strlen($hotel->description) > 40)
										<em>
										<span id="descriptionShort">{!! substr($hotel->description, 0, 40) !!}</span>
										<span id="descriptionFull" style="display: none;">{!! $hotel->description !!}</span>
										<a href="#" onclick="toggleDescription()">... View More</a>
										</em>
									@else
										<em>{!! $hotel->description !!}</em>
									@endif
								</p>

								{{--
								@if($hotel->your_space)
								<p class="fw-bold border-top pt-2"> @lang('messages.the_space') </p>
								<p class="hotel-content fw-normal text-justify"> {!! $hotel->your_space !!} </p>
								@endif
								@if($hotel->interaction_with_guests)
								<p class="fw-bold border-top pt-2"> @lang('messages.interaction_with_guests') </p>
								<p class="hotel-content fw-normal text-justify"> {!! $hotel->interaction_with_guests !!} </p>
								@endif
								@if($hotel->your_neighborhood)
								<p class="fw-bold border-top pt-2"> @lang('messages.your_neighborhood') </p>
								<p class="hotel-content fw-normal text-justify"> {!! $hotel->your_neighborhood !!} </p>
								@endif
								@if($hotel->getting_around)
								<p class="fw-bold border-top pt-2"> @lang('messages.getting_around') </p>
								<p class="hotel-content fw-normal text-justify"> {!! $hotel->getting_around !!} </p>
								@endif
								@if($hotel->other_things_to_note)
								<p class="fw-bold border-top pt-2"> @lang('messages.other_things_to_note') </p>
								<p class="hotel-content fw-normal text-justify"> {!! $hotel->other_things_to_note !!} </p>
								@endif
								--}}
							</div>

							<!-- ALL ROOMS -->
							<div class="room-select mt-3 pb-3" id="rooms" :class="{'loading': isLoading || isContentLoading}">
								<h5 class="listing_desc_head"> @lang('messages.choose_your_room')</h5>
								<div class="text-danger my-3 fs-6" role="alert" v-if="all_rooms.length == 0 && !isLoading && !isContentLoading && error_message">
									@{{error_message}}
								</div>
								<div class="choose-room" :class="{'active' : room.is_selected && room.tmp_number > 0}" v-for="(room,index) in all_rooms" v-else>
									<div class="choose-detail">
										<div class="d-flex flex-column-reverse flex-md-row justify-content-between">
											<div class="me-2 d-flex flex-column justify-content-between flex-grow-1 mt-3 mt-md-0"> 
												<div class="d-flex justify-content-between">
													<div>
														<h3 class="text-truncate-1 conveythis-do-not-translate">@{{room.name}}</h3>
														<p class="mb-2">
															@{{ room.room_type }}
															<span v-if="room.room_size">
																<span class="divider mx-2"> · </span>
																@{{room.room_size_text}}
															</span>
														</p>
													</div>
												</div>
												<div class="d-flex justify-content-between">
													<div class="">
														<span class="material-icons-outlined align-middle">
															single_bed
														</span>
														<span>@{{room.bed_text}}</span>
													</div>
													<div class="">
														<span class="material-icons-outlined align-middle">
															meeting_room
														</span>
														<span>@{{room.number_text}}</span>
													</div>
													<div class="me-2">
														<span class="material-icons-outlined align-middle">
															person_outline
														</span>
														<span>@{{room.adult_text}}</span>
													</div>
													<div class="me-2">
														<span class="material-icons-outlined align-middle">
															face
														</span>
														<span>@{{room.children_text}}</span>
													</div>
												</div>
											</div>
											<div class="choose-img cursor-pointer flex-shrink-0 mx-auto mx-md-0">
												<div class="hotel-subrooms " :class="'subroom-slider-'+index" v-on:click="initSliderGalary(room.hotel_room_photos,index)">
													<div v-for="photo in room.hotel_room_photos" :data-src="photo.image_src" :data-thumb="photo.image_src">
														<img :src="photo.image_src">
													</div>
												</div>
											</div>
										</div>
										@if(count($cancellation_policies) > 0)
											<div class="row mt-4">
												<h5 class="fw-bold response-tl fw-normal">
													@lang('messages.cancellation_policy')
												</h5>
												<div class="pb-2">
													@foreach($cancellation_policies as $policy)
														<template v-if="room.name === '{{ $policy['room_name'] }}'">
															<p class="d-block mb-1 text-justify text-gray response-para">
																@lang('messages.room_name'):
																<span class="fw-bold">{{ $policy['room_name'] }}</span>
															</p>
															<div class="col-md-6">
																@foreach($policy['policies'] as $policy)
																	<p class="mb-1">
																		{{ $policy->days }}
																		@lang('messages.days') @lang('messages.before_checkin_date'):
																		{{ $policy->percentage }}<span class="">%</span>
																	</p>
																@endforeach
															</div>
															<hr>
														</template>
													@endforeach
												</div>
											</div>
										@endif
										<div class="row g-3 border-top mt-3">
											<div class="overview-amenities mt-3 pb-3" v-if="room.amenity_types.length > 0">
												<h5 class="listing_desc_head"> @lang('messages.amenities') </h5>
												<div class="listing-content p-2">
													<div v-for="(amenity_type,index) in room.amenity_types">
														<div v-if="index == 0">
															<div class="mt-2 mb-2">
																<span class="fw-bold response-tl"> @{{ amenity_type.name }} </span>
															</div>
															<div class="row mt-3">
																<div class="col-md-4 amenity-wrap mb-2" v-for="amenity in amenity_type.amenities">
																	<img class="img-icon me-2" :src="amenity.image_src">
																	<span class="fw-normal" data-toggle="tooltip" data-placement="top" :title="amenity.name ">
																		@{{ amenity.name }}
																	</span>
																</div>
															</div>
														</div>
														<div v-else>
															<div class="collapse" id="amenityCollapse">
																<div class="mt-2 mb-2">
																	<span class="fw-bold response-tl"> @{{ amenity_type.name }} </span>
																</div>
																<div class="row mt-3">
																	<div class="col-md-4 amenity-wrap mb-2" v-for="amenity in amenity_type.amenities">
																		<img class="img-icon me-2" :src="amenity.image_src">
																		<span class="fw-normal" data-toggle="tooltip" data-placement="top" :title="amenity.name ">
																			@{{ amenity.name }}
																		</span>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<a class="text-decoration-underline float-end" data-bs-toggle="collapse" href="#amenityCollapse" role="button" aria-expanded="false" aria-controls="amenityCollapse" id="amenity-collapse">+ View More</a>
												</div>
											</div>
										</div>
									</div>
									<div class="select-room d-flex justify-content-between flex-row">
										<div>
											<div class="d-flex">
												<div class="me-2 price-choose">
													<span class="text-small fw-normal text-decoration-line-through" v-if="room.price != room.original_price">
														{{session('currency_symbol')}} @{{new Intl.NumberFormat().format(room.original_price + room.bed_price + room.meal_plan_price)}}
													</span>
													<span>
														{{ session('currency_symbol')}} @{{new Intl.NumberFormat().format(room.price + room.bed_price + room.meal_plan_price)}}
													</span>
												</div>
												<div class="">
													/ @{{room.total_nights_text}} 
												</div>
											</div>
											<div v-if="room.adults != room.max_adults">
												<div class="detail-persons" v-if="room.adult_price > 0 ">
													<span class="mr-2">
														@lang('messages.adult_price')
													</span>
													<span>:</span>
													<span>
														@{{room.adult_price_text}}
													</span>
												</div>
												<div class="detail-persons" v-if="room.child_price > 0 ">
													<span class="mr-2">
														@lang('messages.children_price')
													</span>
													<span>:</span>
													<span>
														@{{room.child_price_text}}
													</span>
												</div>
											</div>
										</div>
										<div>
											<button class="select-btn-room selected" v-if="room.is_selected && room.tmp_number > 0">
												<span class="icon icon-check-circle me-1 fs-6"></span>@lang('messages.selected')
											</button>
											<button class="select-btn-room" v-else v-on:click="addRoom(index)">@lang('messages.select')</button>
										</div>
									</div>
									<div class="select-items select-room justify-content-between flex-row" v-if="room.is_selected && room.meal_plans.length > 0">
										<p class="fw-500">@lang('messages.add_your_meal') </p>
										<div class="d-flex gap-1 flex-wrap" :id="'show_meal_'+index">
											<div v-for="(plan,key) in room.meal_plans" class="d-flex meal_plan">
												<input type="checkbox" class="btn-check" :id="'meal_plan_'+index+'_'+key" v-on:click="choosePlan(key,index)">
												<label class="btn rounded-pill" :for="'meal_plan_'+index+'_'+key">
													<span class=""> @{{ plan.name }} </span>
													<span> {{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(plan.price) }} </span>
												</label>
											</div>
										</div>
									</div>
									<div class="select-items select-room justify-content-between flex-row" v-if="room.is_selected && room.bed_types.length > 0">
										<p class="fw-500"> @lang('messages.add_your_extra_bed')</p>
										<div class="d-flex gap-1 flex-wrap" :id="'show_bed_'+index">
											<div v-for="(bed,key) in room.bed_types" class="d-flex meal_plan">
												<input type="checkbox" class="btn-check" :id="'bedType_'+index+'_'+key" v-on:click="chooseBeds(key,index)">
												<label class="btn rounded-pill" :for="'bedType_'+index+'_'+key">
													<span class=""> @{{ bed.name }}</span>
													<span> {{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(bed.price) }} </span>
												</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- mobile view form -->
							<div class="booking-box d-block d-md-none" :class="{'loading': isContentLoading}">
								<div class="hotel-book-info border">
									<div class="d-flex align-items-center">
										<p class="mb-0 de-val">{{ session('currency_symbol') }}@{{ new Intl.NumberFormat().format(total_price) }}</p>
										<p class="mb-0">
											/ @lang('messages.per') @{{total_nights}} @lang('messages.nights')
										</p>
									</div>
									<div class="info-pay">
										@lang('messages.inclusive_of_all_taxes')
									</div>

									<!-- CHECK AVAILABILITY -->
									<div class="d-flex date-room flex-wrap justify-content-center justify-content-md-start flex-md-nowrap">
										<div class="date-view">
											<input type="hidden" name="search_checkin" class="search_checkin" v-model="checkin">
											<input type="hidden" name="search_checkout" class="search_checkout" v-model="checkout">
											<input type="text" class="date_picker form-control" placeholder="{{ Lang::get('messages.checkin').' to '.Lang::get('messages.checkout') }}" readonly value="{{ $checkin.' to '.$checkout }}">
										</div>
										<div class="align-items-center d-flex guest-view justify-content-center">
											<div class="align-items-center d-flex dropdown dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="mob-occupancy">
												<span>@{{get_occupancy_text}}</span>
												<span class="icon icon-down align-middle ms-2"></span>
											</div>
											<ul class="dropdown-menu guest-menu w-100 p-3">
												<li class="mb-3">
													<div class="d-flex justify-content-between align-items-end">
														<div>
															<p class="h6">@lang('messages.rooms')</p>
														</div>
														<div class="d-flex justify-content-between align-items-center">
															<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="rooms--" :disabled="rooms == '1'">
																<span class="icon icon-minus"></span>
															</button>
															<p class="m-0">@{{ rooms }}</p>
															<button type="button" class="btn btn-default rounded-circle ms-auto p-0"v-on:click="rooms++">
																<span class="icon icon-plus"></span>
															</button>
														</div>
													</div>
												</li>
												<li class="mb-3">
													<div class="d-flex justify-content-between align-items-end">
														<div>
															<p class="h6">@lang('messages.adults')</p>
														</div>
														<div class="d-flex justify-content-between align-items-center">
															<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="adults--" :disabled="adults == '1'">
																<span class="icon icon-minus"></span>
															</button>
															<p class="m-0">@{{ adults }}</p>
															<button type="button" class="btn btn-default rounded-circle ms-auto p-0" v-on:click="adults++" :disabled="adults == max_guests">
																<span class="icon icon-plus"></span>
															</button>
														</div>
													</div>
												</li>
												<li class="mb-3">
													<div class="d-flex justify-content-between align-items-center align-items-end">
														<div>
															<p class="h6 mb-0">@lang('messages.children')</p>
															<p class="m-0 text-gray fs-6 fw-normal">@lang('messages.children_desc')</p>
														</div>
														<div class="d-flex justify-content-between align-items-center">
															<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="children--" :disabled="children == '0'">
																<span class="icon icon-minus"></span>
															</button>
															<p class="m-0">@{{ children }}</p>
															<button type="button" class="btn btn-default rounded-circle ms-auto p-0"v-on:click="children++" :disabled="children == max_guests">
																<span class="icon icon-plus"></span>
															</button>
														</div>
													</div>
												</li>
												<li class="apply-li">
													<div class="d-flex justify-content-end">
														<button class="btn button-link border-0 p-0 text-decoration-underline text-secondary" v-on:click="validateBookingDetails()">
															@lang('messages.apply')
														</button>
													</div>
												</li>
											</ul>
										</div>
									</div>
									<!-- Mobile View SELECTED ROOMS -->
									<div v-for="(room,index) in selected_rooms" class="my-2" :class="{'loading': isLoading}">
										<div class="room-type d-flex align-items-center justify-content-between align-items-center" v-if="room.selected_count > 0">
											<div class="dropdown d-flex justify-content-between align-items-center w-100">
												<div class="d-flex flex-wrap">
													<div class="w-100 d-flex">
														<span class="count-detail d-flex">
															<span class="me-1">@{{ room.name }} </span>
															<span> x @{{ room.selected_count }}</span>
														</span>
														<span class="ms-2">(@{{ room.currency_symbol }} @{{new Intl.NumberFormat().format(room.total_price)}})</span>
													</div>
													<div class="detail-persons">
														<span class="">
															@{{getRoomOccupancyText(room.total_adults,'adults')}}
														</span>
														<span class="" v-if="room.total_children > 0">
															<span>,</span>
															@{{getRoomOccupancyText(room.total_children,'children')}}
														</span>
													</div>
												</div>
												<div class="d-flex">
													<a href="#" role="button" class="dropdown text-end me-2" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownMenuLink" data-bs-auto-close="outside">
														<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
															<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
														</svg>
													</a>
													<a role="button" class="text-end" v-on:click="removeRoom(index)">
														<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
															<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
															<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
														</svg>
													</a>
													<div class="dropdown-menu">
														<div class="room-guest-dd">
															<div class="d-flex justify-content-between border-bottom room-table p-2">
																<div class="flex-fill text-center">
																	@lang('messages.rooms')
																</div>
																<div class="flex-fill text-center">
																	@lang('messages.adults')
																</div>
																<div class="flex-fill text-center">
																	@lang('messages.children')
																</div>
															</div>
															<div class="room-table p-2">
																<div class="room-tr d-flex align-items-center" v-for="(add_room,n) in room.add_rooms">
																	<div class="flex-fill mt-2">
																		@lang('messages.room') @{{n+1}}
																	</div>
																	<div class="flex-fill text-center mt-2">
																		<select class="form-select" v-model="add_room.adults" style="width: auto" v-on:change="calcRoomPrice(index,n)">
																			<option v-for="n in room.max_adults">@{{n}}</option>
																		</select>
																	</div>
																	<div class="flex-fill text-center mt-2">
																		<select class="form-select" v-model="add_room.children" style="width: auto" v-on:change="calcRoomPrice(index,n)">
																			<option v-for="n in room.max_children + 1" :value="n-1">@{{n - 1}}</option>
																		</select>
																	</div>
																</div>
																<div class="room-tr d-flex mt-2">
																	<div class="flex-fill">
																		<button class="bg-white button-link border-0 py-2 px-0 text-start" v-on:click="removeExtraRoom(index)" :disabled="room.add_rooms.length < 2">@lang('messages.delete')</button>
																	</div>
																	<div class="flex-fill">
																		<button class="text-end bg-white button-link border-0 py-2 px-0" v-on:click="addExtraRoom(index)" :disabled="room.add_rooms.length >= room.tmp_number">@lang('messages.add_room')</button>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="text-center" v-if="all_rooms.length == 0 && !isLoading && !isContentLoading && error_message">
										<div class="text-danger my-3 fs-6" role="alert">
											@{{error_message}}
										</div>
										<button class="cont-book mt-4 room-select mt-3 pb-3 btn-danger" v-if="error_type == 'soldout'">
											<span class="px-2">@lang('messages.sold_out')</span>
										</button>
									</div>
									<div class="text-center" v-else-if="selected_rooms.length == 0 && !isLoading && !isContentLoading">
										<div class="text-danger my-3 fs-6" role="alert">
											@lang('messages.choose_room_message')
										</div>
									</div>
									<div v-else>
										<div class="border-top pt-2 px-1 d-flex justify-content-between total-price">
											<div>
												@lang('messages.total_price'): 
											</div>
											<div class="value-price">
												{{ session('currency_symbol') }} @{{new Intl.NumberFormat().format(total_price)}}
											</div>
										</div>
										<button class="cont-book bg-secondary mt-4 room-select mt-3 pb-3 btn-secondary" :disabled="isLoading" v-on:click="confirmReserve()">
											<span class="px-2">@lang('messages.continue_to_book')</span>
										</button>
									</div>
								</div>
							</div>

							<!-- AMENITY -->
							@if($selected_amenity_types->count())
							<div class="overview-amenities mt-3 pb-3">
								<h5 class="listing_desc_head"> @lang('messages.amenities') </h5>
								<div class="listing-content p-2">
									@foreach($selected_amenity_types as $amenity_type)
									<div class="mt-2 mb-2">
										<span class="h5 fw-bold response-tl"> {{ $amenity_type->name }} </span>
									</div>
									<div class="row mt-3">
										@foreach($amenity_type->amenities as $amenity)
										<div class="col-md-4 amenity-wrap mb-2">
											<img class="img-icon me-2" src="{{ $amenity->image_src }}">
											<span class="fw-normal" data-toggle="tooltip" data-placement="top" title="{{ $amenity->name }}">
												{{ $amenity->name }}
												@if($amenity->description != '')
												@endif
											</span>
										</div>
										@endforeach
									</div>
									@endforeach
								</div>
							</div>
							@endif

							<!-- GUEST ACCESS -->
							@if($selected_guest_accesses->count())
							<div class="overview-guest_accesses mt-3 pb-3">
								<h5 class="listing_desc_head"> @lang('messages.guest_accesses') </h5>
								<div class="listing-content p-3">
									<div class="row">
										@foreach($selected_guest_accesses as $guest_access)
										<div class="col-md-4 amenity-wrap mb-2">
											<span class="icon icon-check-circle"></span>
											<span class="fw-normal" data-toggle="tooltip" data-placement="top" title="{{ $guest_access->name }}">
												{{ $guest_access->name }}
												@if($guest_access->description != '')
												<i class="icon icon-info" data-bs-toggle="tooltip" title="{{ $guest_access->description }}" area-hidden="true"></i>
												@endif
											</span>
										</div>
										@endforeach
									</div>
								</div>
							</div>
							@endif

							<!-- VIDEO URL -->
							@if($hotel->video_url != '')
							<div class="overview-video mt-3 pb-3 border-bottom">
								<h5 class="hotel_desc_head"> @lang('messages.video') </h5>
								<div class="hotel-content">
									<iframe class="w-100" height="350" src="{{ $hotel->video_url }}?showinfo=0" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>
								</div>
							</div>
							@endif
						</div>
						<!-- HOTEL POLICY -->
						@if(!empty($hotel->hotel_policy))
						<div class="hotel-policies pt-4 border-top">
							<h4 class="listing_desc_head">@lang('messages.hotel_policies') </h4>
							<div class="p-3 text-gray">
								{!! $hotel->hotel_policy !!}
							</div>
						</div>
						@endif

						<!-- REVIEWS -->
						<div id="reviews" class="mt-3">
							@if($reviews->count() > 0)
							<div class="d-flex justify-content-between">
								<h4 class="fw-bold"> @lang('messages.user') @lang('messages.reviews') </h4>
								<div class="review-header d-flex">
									<div class="reviewers">{!! $hotel->getReviewStars() !!} ({{ $hotel->total_rating}} @lang('messages.reviews') ) </div>
								</div>
							</div>
							<div class="review-container p-3">
								@foreach($reviews->take(5) as $review)
								<div class="user-review">
									<div class="col-12 my-2 d-flex px-1">
										<div class="review-user">
											<a class="media-photo media-round align-top" href="{{ resolveRoute('view_profile',['id' => $review->user_from])}}">
												<img class="profile-image" src="{{ $review->user->profile_picture_src }}" title="{{ $review->user->first_name }}">
											</a>
										</div>
										<div class="ms-2 ps-2">
											<h5 class="text-black d-block"> {{ $review->user->first_name }} </h5>
											<h6 class="text-gray d-block"> {{ $review->created_at->format('F Y') }} </h6>
											{!! $review->getReviewStars() !!}
										</div>
									</div>
									<div class="col-12 text-justify">
										<p class="text-gray"> {{ $review->public_comment }} </p>
									</div>
									@if($review->review_photos->count() > 0)
									<div class="col-12 text-justify">
										<div class="choose-img cursor-pointer flex-shrink-0 mx-auto mx-md-0">
											<div class="row p-0">
												@foreach($review->review_photos as $review_photo)
													<img src="{{ $review_photo->image_src }}" class="review-subrooms col-md-2 img hotel-thumb" data-src="{{ $review_photo->image_src }}" v-on:click="ReviewSliderGalary({{$review->review_photos}})">
												@endforeach
											</div>
										</div>
									</div>
									@endif
									@if($review->public_reply != '')
									<div class="col-12 text-justify ps-4">
										<i class="icon icon-message-box"></i> {{ $review->public_reply }}
									</div>
									@endif
								</div>
								@endforeach
								@if($reviews->count() > 5)
								<div class="col-12 col-md-9 mb-2 mt-md-2">
									<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewsModal"> @lang('messages.show_all_reviews') </button>
								</div>
								@endif
							</div>
							@endif
						</div>

						<!-- THINGS TO KNOW -->
						<div id="things_to_know" class="mt-3">
							<h4 class="fw-bold hotel_desc_head">  @lang('messages.things_to_know') </h4>
							<div class="row p-3">
								<div class="row">
									<h5 class="fw-bold response-tl fw-normal">@lang('messages.hotel_rules') 
									</h5>
									@if($selected_hotel_rules->count())
									@foreach($selected_hotel_rules as $hotel_rule)
									<div class="col-md-4 amenity-wrap mb-2">
										<span class="icon icon-close me-2 align-middle text-danger"></span>
										<span class="text-gray response-para" data-toggle="tooltip" data-placement="top" title="{{ $hotel_rule->name }}"> {{ $hotel_rule->name }} </span>
									</div>
									@endforeach
									@endif
								</div>
								<div class="row mt-4">
									<div class="col-md-6 check-bg border-end p-3 border-white">
										<h5 class="fw-bold response-tl fw-normal"><span class="icon icon-time icon-outlined"></span>   @lang('messages.checkin_at') </h5>
										<p class="d-block mb-1 text-justify text-gray response-para">
											@if($hotel->checkin_time == 'flexible')
											@lang('messages.usual_checkin_time',['key' => $hotel->
											checkin_time])
											@else 
											@lang('messages.usual_checkin_time',['key' => getTimeInFormat($hotel->
											checkin_time)])
											@endif
										</p>
									</div>
									<div class="col-md-6 check-bg p-3">
										<h5 class="fw-bold response-tl fw-normal"><span class="icon icon-time icon-outlined"></span>   @lang('messages.checkout_at') </h5>
										<p class="d-block mb-1 text-justify text-gray response-para">
											@if($hotel->checkout_time == 'flexible')
											@lang('messages.usual_checkout_time',['key' => $hotel->
											checkout_time])
											@else 
											@lang('messages.usual_checkout_time',['key' => getTimeInFormat($hotel->
											checkout_time)])
											@endif
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- web view form -->
		<div class="col-md-5 ms-auto d-none d-md-inline-block">
			<div class="booking-box" :class="{'loading': isContentLoading}">
				<div class="hotel-book-info border">
					<div class="d-flex align-items-center">
						<p class="mb-0 de-val">{{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(total_price) }}</p>
						<p class="mb-0">
							/ @lang('messages.per') @{{total_nights}} @lang('messages.nights')
						</p>
					</div>
					<div class="info-pay">
						@lang('messages.inclusive_of_all_taxes')
					</div>

					<!-- CHECK AVAILABILITY -->
					<div class="d-flex date-room flex-wrap justify-content-center justify-content-md-start flex-md-nowrap">
						<div class="date-view w-100">
							@lang('messages.checkin_to_checkout')
						</div>
						<div class="date-view w-100">
							<input type="hidden" name="search_checkin" class="search_checkin" v-model="checkin">
							<input type="hidden" name="search_checkout" class="search_checkout" v-model="checkout">
							<input type="text" class="date_picker form-control p-1 mx-4" placeholder="{{ Lang::get('messages.checkin').' to '.Lang::get('messages.checkout') }}" readonly value="{{ $checkin.' to '.$checkout }}">
						</div>
					</div>

					<!-- SELECTED ROOMS -->
					<div v-for="(room,index) in selected_rooms" class="my-2" :class="{'loading': isLoading}">
						<div class="room-type d-flex align-items-center justify-content-between align-items-center" v-if="room.selected_count > 0">
							<div class="dropdown d-flex justify-content-between align-items-center w-100">
								<div class="d-flex flex-wrap">
									<div class="w-100 d-flex">
										<span class="count-detail d-flex">
											<span class="me-1">@{{ room.name }} </span>
											<span> x @{{ room.selected_count }}</span>
										</span>
										<span class="ms-2">(@{{ room.currency_symbol }} @{{new Intl.NumberFormat().format(room.total_price)}})</span>
									</div>
									<div class="detail-persons">
										<span class="">
											@{{getRoomOccupancyText(room.total_adults,'adults')}}
										</span>
										<span class="" v-if="room.total_children > 0">
											<span>,</span>
											@{{getRoomOccupancyText(room.total_children,'children')}}
										</span>
									</div>
								</div>
								<div class="d-flex">
									<a href="#" role="button" class="dropdown text-end me-2" data-bs-toggle="dropdown" aria-expanded="false" id="dropdownMenuLink" data-bs-auto-close="outside">
										<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
											<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
										</svg>
									</a>
									<a role="button" class="text-end" v-on:click="removeRoom(index)">
										<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
											<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
											<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
										</svg>
									</a>
									<div class="dropdown-menu">
										<div class="room-guest-dd">
											<div class="d-flex justify-content-between border-bottom room-table p-2">
												<div class="flex-fill text-center">
													@lang('messages.room')
												</div>
												<div class="flex-fill text-center">
													@lang('messages.adults')
												</div>
												<div class="flex-fill text-center">
													@lang('messages.children')
												</div>
											</div>
											<div class="room-table p-2">
												<div class="room-tr d-flex align-items-center" v-for="(add_room,n) in room.add_rooms">
													<div class="flex-fill mt-2">
														@lang('messages.room') @{{n+1}}
													</div>
													<div class="flex-fill text-center mt-2">
														<select class="form-select" v-model="add_room.adults" style="width: auto" v-on:change="calcRoomPrice(index,n)">
															<option v-for="n in room.max_adults">@{{n}}</option>
														</select>
													</div>
													<div class="flex-fill text-center mt-2">
														<select class="form-select" v-model="add_room.children" style="width: auto" v-on:change="calcRoomPrice(index,n)">
															<option v-for="n in room.max_children + 1" :value="n-1">@{{n - 1}}</option>
														</select>
													</div>
												</div>
												<div class="room-tr d-flex mt-2">
													<div class="flex-fill">
														<button class="bg-white w-100 button-link border-0 py-2 px-0 text-start" v-on:click="removeExtraRoom(index)" :disabled="room.add_rooms.length < 2">@lang('messages.delete')</button>
													</div>
													<div class="flex-fill">
														<button class="bg-white text-end w-100 button-link border-0 py-2 px-0" v-on:click="addExtraRoom(index)" :disabled="room.add_rooms.length >= room.tmp_number">@lang('messages.add_room')</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="text-center" v-if="all_rooms.length == 0 && !isLoading && !isContentLoading && error_message">
						<div class="text-danger my-3 fs-6" role="alert">
							@{{error_message}}
						</div>
						<button class="cont-book mt-4 room-select mt-3 pb-3 btn-danger" v-if="error_type == 'soldout'">
							<span class="px-2">@lang('messages.sold_out')</span>
						</button>
					</div>
					<div class="text-center" v-else-if="selected_rooms.length == 0 && !isLoading && !isContentLoading">
						<div class="text-danger my-3 fs-6" role="alert">
							@lang('messages.choose_room_message')
						</div>
					</div>
					<div v-else>
						<div class="pt-2 px-1 d-flex justify-content-between total-price" v-if="fee.service_charge > 0 || fee.property_tax > 0">
							<span>
								@lang('messages.sub_total'): 
							</span>
							<span class="value-price">
								{{ session('currency_symbol') }} @{{new Intl.NumberFormat().format(total_price)}}
							</span>
						</div>
						<div class="border-top pt-2 px-1 d-flex justify-content-between total-price" v-if="service_fee > 0">
							<span>
								@lang('messages.service_fee'): 
							</span>
							<span class="value-price">
								{{ session('currency_symbol') }} @{{new Intl.NumberFormat().format(service_fee) }}
							</span>
						</div>
						<div class="pt-2 px-1 d-flex justify-content-between total-price" v-if="fee.service_charge > 0">
							<span class="text-success">
								@lang('messages.service_charge'): 
							</span>
							<span class="value-price text-success">
								{{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(service_charge) }}
							</span>
						</div>
						<div class="pt-2 px-1 d-flex justify-content-between total-price" v-if="fee.property_tax > 0">
							<span class="text-success">
								@lang('messages.property_tax'): 
							</span>
							<span class="value-price text-success">
								{{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(property_tax) }}
							</span>
						</div>
						<div class="border-top pt-2 px-1 d-flex justify-content-between total-price">
							<span>
								@lang('messages.total_price'): 
							</span>
							<span class="value-price">
								{{ session('currency_symbol') }} @{{ new Intl.NumberFormat().format(total_price_with_tax) }}
							</span>
						</div>
						@if($hotel->user_id != getCurrentUserId())
						<button class="cont-book mt-4 room-select mt-3 pb-3 btn-secondary bg-secondary" :disabled="isLoading" v-on:click="confirmReserve()">
							<span class="px-2">@lang('messages.continue_to_book')</span>
						</button>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="col-12">
			@if($similar_hotels->count())
			<div id="more_places" class="mt-4 pt-4 p-2 border-top recommended_hotel-container">
				<h4 class="fw-bold"> @lang('messages.more_places_to_book') </h4>
				<div id="similar_hotels" class="recommended_hotel">
					@foreach($similar_hotels as $similar_hotel)
					<div class="hotel-wrapper">
						<div class="rounded-xl">
							<a class="common-link hotel-info" href="{{ resolveRoute('hotel_details',[$similar_hotel->id]) }}">
								<div class="hotel-image position-relative">
									<img class="hotel-img hover-card tns-lazy-img lazy-img-fadein" data-src="{{ $similar_hotel->image_src }}" alt="{{ $similar_hotel->name }}">
									<div class="position-absolute location-room">
										<p><span>{{ $similar_hotel->property_type_name }}</span></p>
										<h5><span class="material-icons material-icons-outlined align-middle">place</span>{{ $similar_hotel->hotel_address->city }}</h5>
									</div>
								</div>
							</a>
							<div class="px-2 pb-2 pt-1">
								<div class="hotel-details">
									<a class="common-link hotel-info" href="{{ resolveRoute('hotel_details',[$similar_hotel->id]) }}">
										<h2 class="ellipsis block-xs-center" title="{{ $similar_hotel->name }}"> {{ $similar_hotel->name }} </h2>
									</a>
									<p class="h4">
										<span class="text-black"> {{ $similar_hotel->currency_symbol }} {{ $similar_hotel->hotel_rooms->first()->hotel_room_price->price }} /</span> @lang('messages.per_night',['key' => 1])
									</p>
								</div>
								@if($similar_hotel->total_rating > 0)
								<div class="hotel-type-header d-flex align-items-center">
									<div class="review-header d-flex ">
										{!! $similar_hotel->getReviewStars() !!} <span class="text-gray"> ({{ $similar_hotel->total_rating }} @lang('messages.reviews') ) </span> 
									</div>
								</div>
								@endif
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
			@endif
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		'hotel_id' => $hotel->id,
		'host_id' => $hotel->user_id,
		'is_saved' => $is_saved,
		'checkin' => $checkin,
		'checkout' => $checkout,
		'adults' => $adults,
		'children' => $children,
		'max_guests' => $max_guests,
		'rooms' => $rooms,
		'messages' => [
		'more' => Lang::get('messages.more'),
		'less' => Lang::get('messages.less'),
		],
	]) !!};
	function toggleDescription() {
		var shortDescription = document.getElementById('descriptionShort');
		var fullDescription = document.getElementById('descriptionFull');
		var viewMoreLink = document.querySelector('.hotel-content a');

		if (shortDescription.style.display === 'none') {
		shortDescription.style.display = 'inline';
		fullDescription.style.display = 'none';
		viewMoreLink.innerHTML = '... View More';
		} else {
		shortDescription.style.display = 'none';
		fullDescription.style.display = 'inline';
		viewMoreLink.innerHTML = '... View Less';
		}
	}
</script>

@endpush