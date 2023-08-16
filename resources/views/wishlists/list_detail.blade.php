@extends('layouts.app')
@section('content')
<main role="main" class="main-container search_page">
	<section class="container-fluid main-contain mt-2 mt-md-5" v-cloak>
		<div class="row">
			<div class="col-lg-9">
				<div class="search_hotel_main px-3 px-md-4">
					<section class="pt-4 d-flex align-items-center">
						<div class="wishlist-title">
							<h2 class="h1 text-black mt-2"> @{{ wishlist.saved_name }} </h2>
						</div>
						<div class="wishlist-options ms-auto">
							<button type="button" class="btn btn-icon" data-bs-toggle="modal" data-bs-target="#editWishlistModal">
							  <i class="icon icon-vertical-dots" area-hidden="true"></i>
							</button>
						</div>
					</section>
					<section class="search_hotel mx--8">
						<div class="result-container" :class="{'loading': isContentLoading || isLoading}">
							<div class="hotel-results" v-show="list_type == 'hotel'">
								<div class="hotel-details my-3 d-md-flex justify-content-between" v-for="hotel in wishlist_list.hotels">
									<div class="thumbnail col-md-4">
										<ul class="hotel-slider">
											<li class="hotel-image" v-for="photo in hotel.photos_list">
												<img class="tns-lazy-img" :data-src="photo.image_src">
											</li>
										</ul>
										<div class="fav_list">
											<button>
											<span class="icon icon-wishlist-fill theme-color" area-hidden="true" v-if="hotel.is_saved" v-on:click="removeFromWishlist(hotel.id,'hotel')" :disabled="isLoading" :class="{'disabled pointer-none' : isLoading}"></span>
											<span class="icon icon-wishlist theme-color" area-hidden="true" v-else v-on:click="getAllWishlists(hotel.id);" data-bs-toggle="modal" data-bs-target="#saveToListModal"></span>
											</button>
										</div>
									</div>
									<div class="desc_list d-flex flex-column ps-3 col-md-8">
										<div class="room_type mt-2 mt-md-0 w-100 d-inline-flex align-items-center ">
											<div class="hotel-title h5 font-weight-bold mb-0">
												<a :href="hotel.url" target="_blank" class="common-link text-capitalize"> @{{ hotel.name }} </a>
												<span class="sub-name sr-only"> @{{ hotel.sub_name }} </span>
											</div>
											<span class="ms-auto fs-6" v-for="no in hotel.hotel_star_rating">
												<i class="icon icon-star text-dark me-1"></i>
											</span>
											<span class="ms-auto fs-6" v-for="no in (5 - hotel.hotel_star_rating)">
												<i class="icon icon-star-empty text-dark me-1"></i>
											</span>
										</div>
										<div class="d-flex">
											<div class="hotel-info mt-0 mt-md-2">
												<i class="icon icon-location text-dark me-2" area-hidden="true"></i>@{{ hotel.location }}
											</div>
										</div>
										<div class="mt-2 fs-6">
											<div class="">
												<span class="me-2">@{{ hotel.checkin_text }}:</span>
												<span class="fw-normal">@{{ hotel.checkin }}</span>
											</div>
											<div class="">
												<span class="me-2">@{{ hotel.checkout_text }}:</span>
												<span class="fw-normal">@{{ hotel.checkout }}</span>
											</div>
										</div>
										<div class="d-flex flex-wrap border-top mt-2">
											<div class="col-md-4 col-6 mt-2 amenity-detail amenity-wrap" v-for="amenity in hotel.amenities">
												<img class="me-2" :src="amenity.image_src">
												<span class="fw-normal" data-toggle="tooltip" data-placement="top" :title="amenity.name"> @{{ amenity.name }}</span>
											</div>
											<div class="col-md-4 col-6 mt-2 amenity-detail">
												<a :href="hotel.url" class="text-decoration-underline">
													+ More	
												</a>
											</div>
										</div>
										<div class="d-flex align-items-end justify-content-end hotel-price mt-2 border-top">
											<div class="d-flex justify-content-between align-items-center w-100">
												<div class='hotel-info mt-0 mt-md-2'>
													<span class="ms-auto" v-show="hotel.total_rating > 0">
														<span class="me-1" v-for="no in hotel.rating">
															<i class="icon icon-circle-fill"></i>
														</span>
														<span class="me-1" v-for="no in (5 - hotel.rating)">
															<i class="icon icon-circle-empty"></i>
														</span>
														<span class="reviewers">&nbsp;(@{{ hotel.total_rating }} @{{ hotel.review_text }})</span>
													</span>
												</div>
												<span class="price primary"> @{{ hotel.price_text }} </span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="text-center" v-show="wishlist_list.hotels.length == 0">
								@lang('messages.no_wishlist_created')
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
	window.vueInitData = {!! json_encode([
		'wishlist_list' => $wishlist_lists,
		'wishlist' => $wishlist,
	]) !!};
</script>
@endpush