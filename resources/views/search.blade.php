@extends('layouts.app')
@section('content')
<main role="main" class="main-container search_page mt-3">
	<div class="search_content">
		<div class="container">
			<div class="row g-3">
				<div class="col-6 col-md-3 m-0 pt-3">
					<input type="hidden" name="place_id" id="place_id" class="autocomplete_input autocomplete-place_id" value="{{ $searchFilter['place_id'] ?? '' }}">
					<div class="dropdown form-floating m-0">
						<input type="location" name="location" class="form-control" v-model="searchFilter.location" id="location" data-bs-toggle="dropdown" aria-expanded="false" v-on:keyup="getAutoCompleteResults()">
						<label for="locationInput" class="form-label">@lang('messages.where')</label>
						<ul class="dropdown-menu w-100" id="locationDropdown">
							<li v-for="(result,index) in search_results" class="dropdown-item" v-if="search_results.length">
								<a class="d-flex border-bottom" :href="result.link" v-on:click="setSearchLocation(index)">
									<div class="d-flex justify-content-between w-100 py-2">
										<div class="d-flex align-items-center w-100">
											<div class="map-circle me-2">
												<span class="icon icon-map" v-if="!result.is_hotel"></span>
												<span class="material-icons material-icons-filled" v-if="result.is_hotel">
													single_bed
												</span>
											</div>
											<div class="w-100">
												<p class="m-0">@{{ result.main_text }}</p>
												<p class="text-truncate-1 text-muted m-0 fs-7">@{{result.sub_text}}</p>
											</div>
										</div>
										<div v-if="result.is_hotel && result.hotel_address_count > 0">
											@{{result.hotel_address_count}} @lang('messages.hotels')
										</div>
										<div v-if="result.type == 'city'"> @lang('messages.city')
										</div>
										<div v-if="result.type == 'country'"> @lang('messages.country')
										</div>
									</div>
								</a>
							</li>
							<li v-else-if="!isLoading" class="dropdown-item-text">@lang('messages.no_result_found')</li>
						</ul>
					</div>
					<span class="text-danger location-error d-none"> @lang('messages.please_choose_from_dropdown') </span>					
				</div>
				<div class="col-6 col-md-3 m-0 pt-3">
					<input type="hidden" name="checkin" class="search_checkin" id="search_checkin" :value="searchFilter.checkin">
					<input type="hidden" name="checkout" class="search_checkout" id="search_checkout"  :value="searchFilter.checkout">
					<div class="form-floating m-0">
						<input type="text" id="search_date_picker" class="search_date_picker form-control bg-white" placeholder="@lang('messages.add_dates')" readonly value="{{ $searchFilter['checkin'].' to '.$searchFilter['checkout'] }}">
					  	<label for="search_date_picker"> @lang('messages.checkin') - @lang('messages.checkout') </label>
					</div>
				</div>
				<div class="col-9 col-md-3 m-0 pt-3">
					<div class="form-floating m-0 search-view">
						<button class="btn bg-white text-start form-control" data-bs-toggle="dropdown" data-bs-auto-close="outside">
							@{{getGuestText}}
						</button>
					  	<label for="search_date_picker"> @lang('messages.guests')</label>
						<ul class="dropdown-menu guest-menu w-100 p-3">
							<li class="mb-2">
								<div class="d-flex justify-content-between align-items-end">
									<div>
										<p class="h6">@lang('messages.rooms')</p>
									</div>
									<div class="d-flex justify-content-between align-items-center">
										<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="searchFilter.rooms--" :disabled="searchFilter.rooms == '1'">
											<span class="icon icon-minus"></span>
										</button>
										<input type="number" :value="searchFilter.rooms" class="form-control">
										<button type="button" class="btn btn-default rounded-circle ms-auto p-0" v-on:click="searchFilter.rooms++">
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
										<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="searchFilter.adults--" :disabled="searchFilter.adults == '1'">
											<span class="icon icon-minus"></span>
										</button>
										<input type="number" :value="searchFilter.adults" class="form-control">
										<button type="button" class="btn btn-default rounded-circle ms-auto p-0" v-on:click="searchFilter.adults++" :disabled="searchFilter.adults == searchFilter.max_guests">
											<span class="icon icon-plus"></span>
										</button>
									</div>
								</div>
							</li>
							<li class="mb-2">
								<div class="d-flex justify-content-between align-items-end">
									<div>
										<p class="h6 mb-0">@lang('messages.children')</p>
										<p class="m-0 text-gray fw-normal">@lang('messages.children_desc')</p>
									</div>
									<div class="d-flex justify-content-between align-items-center">
										<button type="button" class="btn btn-default me-auto p-0 rounded-circle" v-on:click="searchFilter.children--" :disabled="searchFilter.children == '0'">
											<span class="icon icon-minus"></span>
										</button>
										<input type="number" :value="searchFilter.children" class="form-control">
										<button type="button" class="btn btn-default rounded-circle ms-auto p-0" v-on:click="searchFilter.children++" :disabled="searchFilter.children == searchFilter.max_guests">
											<span class="icon icon-plus"></span>
										</button>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-3 col-md-3 d-flex align-items-center m-0 py-3">
					<button type="button" class="btn search-btn w-100 h-100" v-on:click="searchListings()">
						<i class="icon icon-search" aria-hidden="true"></i>
						<span class="ms-1 d-none d-md-inline-block"> @lang('messages.search') </span>
					</button>
				</div>
			</div>
		</div>
	</div>
	<section class="container-fluid main-contain mt-2 mt-md-5 mb-5" v-cloak>
		<div class="row">
			<div class="col-lg-3 d-none d-md-block">
				<div class="filter-side border bg-white">
					<h3 class="p-3 border-bottom">@lang('messages.filter_by')</h3>
					<div class="px-3 mb-3 border-bottom">
						<h5 class="mb-3 fw-bold">@lang('messages.rate')
							<span class="fs-6 fw-normal">(@lang('messages.per_night'))</span></h5>
						<div class="mb-3" :class="{'d-none' : isContentLoading}">
							<div class="price-amount my-2">
								<p class="h5"> {!! session("currency_symbol") !!} @{{ new Intl.NumberFormat().format(searchFilter.min_price) }} - {!! session("currency_symbol") !!} @{{ searchFilter.max_price == 1000 ? new Intl.NumberFormat().format(searchFilter.max_price)+'+' : new Intl.NumberFormat().format(searchFilter.max_price) }}</p>
							</div>
							<div id="price-slider" class="px-2"> </div>
						</div>
					</div>
					<div class="px-3 mb-3 border-bottom">
						<h5 class="mb-3 fw-bold"> @lang('messages.property_type') </h5>
						@foreach($property_types as $key => $property_type)
							@if($key < 5)
							<div class="form-check d-flex col-12 mb-2">
								<input type="checkbox" name="property_types[]" class="flex-shrink-0 form-check-input" id="property_type_{{ $property_type->id }}" value="{{ $property_type->id }}" v-model="searchFilter.property_type" v-on:change="applyFilter('property_type')">
								<label for="property_type_{{ $property_type->id }}" class="form-check-label ms-2 fw-normal"> {{ $property_type->name }} </label>
							</div>
							@else
							<div class="collapse" id="propertyCollapse">
								 <div class="form-check d-flex col-12 mb-2">
									<input type="checkbox" name="property_types[]" class="flex-shrink-0 form-check-input" id="property_type_{{ $property_type->id }}" value="{{ $property_type->id }}" v-model="searchFilter.property_type" v-on:change="applyFilter('property_type')">
									<label for="property_type_{{ $property_type->id }}" class="form-check-label ms-2 fw-normal"> {{ $property_type->name }} </label>
								</div>
							</div>
							@endif
						@endforeach
						<a data-bs-toggle="collapse" href="#propertyCollapse" role="button" aria-expanded="false" aria-controls="propertyCollapse" id="property-collapse">+ View More</a>
					</div>
					<div class="px-3 mb-3 border-bottom star-rate">
						<h5 class="mb-3 fw-bold">@lang('messages.property') @lang('messages.star_rating')</h5>
						<div class="d-flex flex-wrap mb-3">
							<button type="button" :id="'star_rate_'+n" v-for="n in 5" v-on:click="changeStarRateFilter(n)">@{{n}}
								<span class="material-icons-outlined">star</span>
							</button>
						</div>
					</div>
					<div class="px-3 mb-3">
						<h5 class="mb-3 fw-bold"> @lang('messages.amenities') </h5>
						@foreach($amenity_types as $key => $type)
							@if($type['amenities']->count() > 0)
								<h6> {{ $type['name'] }} </h6>
								@foreach($type['amenities'] as $index => $amenity)
									@if($index < 10)
										<div class="form-check d-flex col-12 mb-2">
											<input type="checkbox" name="amenities[]" class="flex-shrink-0 form-check-input" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" v-model="searchFilter.amenities" v-on:change="applyFilter('amenities')">
											<label for="amenity_{{ $amenity->id }}" class="form-check-label ms-2 fw-normal"> {{ $amenity->name }} </label>
										</div>
									@else
										<div class="collapse" id="amenityCollapse">
											<div class="form-check d-flex col-12 mb-2">
												<input type="checkbox" name="amenities[]" class="flex-shrink-0 form-check-input" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" v-model="searchFilter.amenities" v-on:change="applyFilter('amenities')">
												<label for="amenity_{{ $amenity->id }}" class="form-check-label ms-2 fw-normal"> {{ $amenity->name }} </label>
											</div>
										</div>
									@endif
								@endforeach
								@if($type['amenities']->count() > 10)
									<a data-bs-toggle="collapse" href="#amenityCollapse" role="button" aria-expanded="false" aria-controls="amenityCollapse" id="amenity-collapse" onclick="toggleButtonText(this)">+ View More</a>
								@endif
							@endif
						@endforeach
					</div>
				</div>
			</div>
			<div class="col-lg-9">
				<div class="search_hotel_main px-3 px-md-4">
					<div class="d-flex justify-content-between justify-content-md-start align-items-center mt-3">
						<section class="d-block d-md-none flex-shrink-0 ms-2 search_filters">
							<div class="filter_nav">
								<ul class="navbar-nav flex-wrap flex-row">
									<li class="nav-item filter-dropdown">
										<button class="btn btn-lite border-0 text-dark rounded-pill bg-black bg-opacity-10 fw-500" type="button" id="more_filter-dropdown-menu" data-bs-toggle="modal" data-bs-target="#moreFiltersModal">
											<span class="material-icons align-middle me-2">
												filter_alt
											</span>
											Sort & Filters
										</button>
									</li>
								</ul>
							</div>
						</section>
					</div>
					<section class="search_hotel mx--8">
						<div class="result-container" :class="{'loading': isContentLoading || hotelLoading}">
							<div class="travelers-choice" v-if="hotels.top_picks_hotels.length">
								<h2 class="fw-bold tl-xl">
									@lang('messages.top_picks')
								</h2>
								<div class="row recommended_hotel gx-4">
									<div class="" v-for="hotel in hotels.top_picks_hotels">
										<div class="rounded-xl">
											<a class="common-link hotel-info" target="_blank" :href="hotel.url">
												<div class="hotel-recommended-image position-relative">
													<img class="hotel-img hover-card tns-lazy-img lazy-img-fadein" :data-src="r_photo.image_src" :src="r_photo.image_src" :alt="hotel.name" v-for="r_photo in hotel.photos_list">
												</div>
											</a>
											<div class="px-2 pb-2 pt-1">
												<div class="hotel-details">
													<a class="common-link hotel-info" target="_blank" :href="hotel.url">
														<h2 class="ellipsis block-xs-center" title="hotel.name"> @{{hotel.name}} </h2>
													</a>
													<p class="h4">
														<span class="text-black"> @{{hotel.price_text}} </span> 
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
							<section class="mt-4">
								<div class="hotel_detail">
									<span v-show="hotels.total == 1"> @{{ hotels.total }} @lang('messages.property') </span>
									<span v-show="hotels.total > 1"> @{{ hotels.total }} @lang('messages.properties') </span>
								</div>
								<div class="heading_list fs-5 fw-normal text-truncate-1" v-if="searchFilter.location != ''"> 	@lang('messages.properties') @lang('messages.in') @{{ searchFilter.location }} 
								</div>
							</section>
							<hotel-details :hotels="hotels.data" :active_hotel_id="active_hotel_id" @get_all_wishlists="getAllWishlists($event)" @remove_from_wishlist="removeFromWishlist($event,'hotel')"></hotel-details>
							<pagination :pagination="hotels" @paginate="changePage()" v-if="hotels.data.length"></pagination>
							<div class="text-center" v-show="hotels.data.length == 0 && !isContentLoading && !isLoading">
								@lang('messages.no_hotels_found')
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</section>
</main>
@endsection
@push('scripts')
<script>
	window.vueInitData = {!!
		json_encode([
			'searchFilter' => $searchFilter,
			'search_latitude' => $search_latitude,
			'search_longitude' => $search_longitude,
			'search_viewport' => $search_viewport,
			'autocomplete_used' => isset($searchFilter['location']) && $searchFilter['location'] != '',
			'default_data' => [
				'min_price' => $default_min_price,
				'max_price' => $default_max_price,
			],
			'is_enabled_map' => credentials('is_enabled' ,'googleMap') == 1,
		]) 
	!!};
</script>
@endpush