@extends('layouts.app')
@section('content')
<main role="main">
	<div class="custom-fade"></div>
	<div class="slider-wrapper">
		<div id="home-carousel" class="home-carousel">
			@foreach($sliders as $slider)
			<div class="carousel-img">
				{{--
				<div class="slider-title">
					<h3> {{ $slider->title }} </h3>
				</div>
				<div class="slider-description">
					@if($slider->description != '')
					<p>"{{ $slider->description }}"</p>
					@endif
				</div>
				--}}
				<img class="tns-lazy-img" data-src="{{ $slider->image_src }}" />
			</div>
			@endforeach
		</div> 
		<div class="search-wrapper d-flex align-items-center px-2 px-md-5">
			<div class="col-12 col-md-5">
				<ul class="nav nav-pills gap-1" id="pills-tab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link text-uppercase rounded-top rounded-0 active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
							<div class="hstack gap-2 tab-click">
								<div class="icon-rounded d-flex justify-content-center align-items-center flex-shrink-0">
									<span class="material-icons-outlined">
										meeting_room
									</span>
								</div>
								<div>
									@lang('messages.hotels')
								</div>
							</div>
						</button>
					</li>
					{{--
					<li class="nav-item" role="presentation">
						<button class="nav-link text-uppercase rounded-top rounded-0" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
							<div class="hstack gap-2 tab-click">
								<div class="icon-rounded d-flex justify-content-center align-items-center flex-shrink-0">
									<span class="material-icons-outlined">
										meeting_room
									</span>
								</div>
								<div>
									@lang('messages.home_stay')
								</div>                        		
							</div>
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link text-uppercase rounded-top rounded-0" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">
							<div class="hstack gap-2 tab-click">
								<div class="icon-rounded d-flex justify-content-center align-items-center flex-shrink-0">
									<span class="material-icons-outlined">
										meeting_room
									</span>
								</div>
								<div>
									Tours & travels
								</div>
							</div>
						</button>
					</li>
					--}}
				</ul>
				<div class="tab-content" id="pills-tabContent">
					<div class="tab-pane show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
						<form action="{{ resolveRoute('search') }}" class="search-form m-0">
							<div class="px-3 py-4 bg-white rounded-custom">
								<h2 class="mb-4 fw-500">
									@lang('messages.search_save_hotels')
								</h2>
								<div class="row mx-0 mb-4">
									<input type="hidden" name="place_id" class="autocomplete_input autocomplete-place_id" id="place_id">
									<div class="col-8">
										<label class="form-label">@lang('messages.where_are_you_going')</label>
										<div class="input-group flex-nowrap position-relative location-section">
											<span class="input-group-text bg-white">
												<span class="icon icon-location text-muted"></span>
											</span>
											<div class="dropdown flex-grow-1">
												<input type="text" name="location" data-flip="false" placeholder="@lang('messages.where_are_you_going')" class="d-none rounded-end d-md-block form-control py-2 rounded-0 pe-5" v-model="location" id="location" v-on:keyup="getAutoCompleteResults()" autocomplete="off" data-bs-offset="-20,30">
												<input type="text" class="form-control rounded-2 d-block d-md-none" data-bs-toggle="modal" data-bs-target="#searchKey" v-model="location" autocomplete="off">
												<ul class="dropdown-menu w-100 p-0" id="locationDropdown">
													<li v-for="(result,index) in search_results" class="dropdown-item border-bottom" v-if="search_results.length">
														<a class="d-flex" :href="result.link" v-on:click="setSearchLocation(index)">
															<div class="d-flex justify-content-between w-100 py-2 align-items-center">
																<div class="d-flex align-items-center w-100">
																	<div class="map-circle me-2">
																		<span class="icon icon-map" v-if="!result.is_hotel"></span>
																		<span class="material-icons material-icons-filled" v-if="result.is_hotel">
																			single_bed
																		</span>
																	</div>
																	<div class="w-100">
																		<p class="m-0 text-truncate m-w-165">@{{ result.main_text }}</p>
																		<p class="text-truncate text-muted m-0 fs-7 m-w-165">@{{result.sub_text}}</p>
																	</div>
																</div>
																<div class="text-muted fw-normal fs-7" v-if="result.is_hotel && result.hotel_address_count > 0">
																	@{{result.hotel_address_count}} @lang('messages.hotels')
																</div>
																<div class="text-muted fw-normal fs-7" v-if="result.type == 'city'"> @lang('messages.city')</div>
																<div class="text-muted fw-normal fs-7" v-if="result.type == 'country'"> @lang('messages.country')</div>
															</div>
														</a>
													</li>
													<li v-else-if="!isLoading" class="dropdown-item-text">@lang('messages.no_result_found')</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="col-4">
										<label class="form-label">@lang('messages.rooms')</label>
										<input type="number" name="rooms" class="form-control" placeholder="Rooms" min="1" value="1">
									</div>
								</div>
								<div class="row mx-0">
									<div class="col-md-8 d-flex flex-column flex-wrap justify-content-between">
										<div class="row gx-4">
											<div class="col-6">
												<label class="form-label">@lang('messages.checkin')</label>
												<div class="input-group flex-nowrap">
													<span class="input-group-text bg-white">
														<span class="icon icon-calendar text-muted"></span>
													</span>
													<input type="text" name="checkin" class="home_checkin px-0 form-control border-start-0" placeholder="{{ Lang::get('messages.checkin') }}">
												</div>
											</div>
											<div class="col-6">
												<label class="form-label">@lang('messages.checkout')</label>
												<div class="input-group flex-nowrap">
													<span class="input-group-text bg-white">
														<span class="icon icon-calendar text-muted"></span>
													</span>
													<input type="text" name="checkout" class="home_checkout px-0 form-control border-start-0" placeholder="{{ Lang::get('messages.checkout') }}">
												</div>
											</div>
										</div>
										<button type="submit" class="btn btn-lg btn-secondary col-md-8 col-12 rounded-1 d-none d-md-block fw-normal text-white border-0">@lang('messages.search_for_hotels')</button>
									</div>
									<div class="col-md-4 mt-2 mt-md-0">
										<div class="d-flex gap-2 flex-md-column">
											<div>
												<label class="form-label">@lang('messages.guest')</label>
												<input type="number" name="adults" class="form-control mb-0 mb-md-3" placeholder="{{ Lang::get('messages.adults') }}" min="1" value="2">
											</div>
											<div>
												<label class="form-label">@lang('messages.children')</label>
												<input type="number" name="children" class="form-control mb-0" placeholder="{{ Lang::get('messages.children') }}" min="0" value="0">
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-lg btn-secondary col-8 mt-4 mx-3 rounded-1 d-block d-md-none fw-normal text-white border-0">Search for hotels</button>
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
						<div class="px-3 py-4 bg-white">
							<h2 class="mb-4">
								profile
							</h2>
						</div>
					</div>
					<div class="tab-pane" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
						<div class="px-3 py-4 bg-white">
							<h2 class="mb-4">
								contact
							</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="home_content">

		<!-- Offer Block -->
		@if($pre_footers->count())
		<section class="offer-block my-4 mx-2 mx-md-0">
			<div class="col-12 py-4 py-md-5">
				<div class="flex-column flex-md-row gy-5 gy-md-0 row  gx-0 gx-md-6">
					@foreach($pre_footers as $key => $pre_footer)
					<div class="col">
						<div class="" :class="{'text-md-end': {{$key}} == 0}">
							<h3 class="mb-2">
								{{ $pre_footer->title }}
							</h3>
							<p>{{ $pre_footer->description }}</p>
							<div class="hstack gap-3" :class="{'justify-content-md-end': {{$key}} == 0}">
								<button class="btn btn-outline-secondary fw-normal rounded-pill text-muted">
									Get offer
								</button>
								<button class="btn btn-outline-secondary fw-normal rounded-pill text-muted">
									Learn more
								</button>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</section>
		@endif

		<!-- Travelers Choice -->
		<section class="travelers-choice" v-if="recommended_hotels.length">
			<div class="py-4 bg-teal text-center">
				<h2 class="fw-bold tl-xl">
					@lang('messages.traveler_choice')
				</h2>
				<p class="mb-0 fs-4">@lang('messages.traveler_choice_desc')</p>
			</div>
			<div class="col-11 col-md-9 mx-auto my-5 py-4">
				<div class="row recommended_hotel gx-4">
					<div class="col-md-3" v-for="hotel in recommended_hotels">
						<div class="rounded-xl">
							<a class="common-link hotel-info" :href="hotel.link">
								<div class="hotel-image position-relative">
									<img class="hotel-img hover-card tns-lazy-img lazy-img-fadein" :data-src="hotel.image_src" :alt="hotel.name">
								</div>
							</a>
							<div class="px-2 pb-2 pt-1">
								<div class="hotel-details">
									<a class="common-link hotel-info" :href="hotel.link">
										<h2 class="ellipsis block-xs-center" :title="hotel.name"> @{{ hotel.name }} </h2>
									</a>
									<p class="h4">
										<span class="text-black"> @{{ hotel.price_text }}</span> 
									</p>
								</div>
								<div class="hotel-type-header d-flex align-items-center" v-if="hotel.total_rating > 0">
									<div class="review-header d-flex ">
										<span v-html="hotel.review_stars"> </span>
										<span class="text-gray"> (@{{ hotel.total_rating }} @lang('messages.reviews') ) </span> 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Offer Section -->
		@if($discount_banners->count() > 0)
		<section class="col-12 offer-section">
			<div class="row mx-0 gy-4 gy-md-0">
				@foreach($discount_banners as $banner)
				<div class="col-md-6 p-0">
					<div class="d-flex align-items-center offer-banner position-relative">
						<img src="{{ $banner->image_src }}" class="w-100 h-100">
					</div>
				</div>
				@endforeach
			</div>
		</section>
		@endif

		<!-- Top Destinations -->
		<section class="top-destinations my-5" v-if="featured_cities.length">
			<div class="container text-center">
				<h3 class="tl-xl">@lang('messages.top_destinations')</h3>
				<p class="mb-4 fs-4">@lang('messages.top_destination_desc')</p>
				<div id="featured-cities" class="row featured-cities py-3 g-4">
					<div class="grid-container">
						<div class="grid-item  overflow-hidden" :data-grid-id="index + 1" v-for="(featured,index) in featured_cities">
							<div class="featured-city position-relative w-100">
								<a :href="featured.search_url" class="common-link d-block">
									<img class="featured-city-img m-0 shadow-lg border-0 w-100 border-0 lazy-img-fadein" :src="featured.image_src">
									<div class="featured-city-name d-flex align-items-end h-100 text-white justify-content-center py-6 py-lg-7">
										<h3 class="text-shadow text-uppercase h2 fw-bold mb-0"> @{{featured.display_name}} </h3>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</main>
<!-- modal -->
<div class="modal fade" id="searchKey" tabindex="-1" aria-labelledby="searchKeyLabel" aria-hidden="true">
	<div class="modal-dialog modal-fullscreen">
		<div class="modal-content rounded-0">
			<div class="modal-header">
				<button type="button" class="btn-link border-0 bg-white text-secondary ms-auto fw-bold" data-bs-dismiss="modal">cancel</button>
			</div>
			<div class="modal-body">
				<div>
					<div class="position-relative mb-3">
						<input type="hidden" name="place_id" class="autocomplete_input autocomplete-place_id">
						<input type="text" name="location" data-flip="false" placeholder="@lang('messages.where_are_you_going')" v-model="location" type="button" id="popup-location" aria-expanded="false" v-on:keyup="getAutoCompleteResults()" class="form-control py-2 rounded-0 rounded-start pe-4 border w-100" autocomplete="off">
					</div>
					<ul class="w-100 p-0" aria-labelledby="location-dd">
						<li>
							<ul class="search-location" :class="location == '' ? 'd-none' : 'd-block'">
								{{--<li class="fw-bold mx-2 p-1 mb-4" v-if="popular_text_show">@lang('messages.popular_cities')</li>--}}
								<li v-for="(result,index) in search_results" class="d-flex mx--15">
									<a class="dropdown-item d-flex justify-content-between border-bottom align-items-center py-0" :href="result.link" v-on:click="setSearchLocation(index)">
										<div class="d-flex justify-content-between w-100 py-2">
											<div class="d-flex align-items-center w-100">
												<div class="map-circle me-2">
													<span class="icon icon-map" v-if="!result.is_hotel"></span>
													<span class="material-icons material-icons-filled" v-if="result.is_hotel">
														single_bed
													</span>
												</div>
												<div class="w-100">
													<p class="m-0 text-truncate-1">@{{ result.main_text }}</p>
													<p class="text-muted m-0 fs-7 text-truncate-1">@{{result.sub_text}}</p>
												</div>
											</div>
											<div v-if="result.type == 'city'">
												@lang('messages.city')
											</div>
											<div v-if="result.type == 'country'">
												@lang('messages.country')
											</div>
										</div>
									</a>
								</li>
							</ul>
							<ul class="flex-wrap py-2 recent_searches" :class="location == '' ? 'd-flex' : 'd-none'">
								{{--<li class="fw-bold mb-4 fs-6 w-100">@lang('messages.popular_cities')</li>--}}
								<li>
									<ul class="hstack gap-2 flex-wrap">
										<li v-for="(result,index) in search_results" class="search-results px-0">
											<a class="align-items-center d-flex justify-content-between px-3 py-2 rounded-3" href="javascript:;" v-on:click="setSearchLocation(index)">
												@{{ result.main_text }}
											</a>
										</li>
									</ul>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- modal -->
@endsection