@extends('layouts.app')
@section('content')
<main role="main" class="main-container pt-4">
	<div class="container">
		<div class="col-md-12 status-btn px-0 mb-4">
			<button type="button" class="border-0 btn btn-white me-4 px-0" :disabled="active_tab == 'current'" v-on:click="switchTab('current');">
				@lang('messages.current_bookings')
			</button>
			<button type="button" class="border-0 btn btn-white me-4 px-0" :disabled="active_tab == 'upcoming'" v-on:click="switchTab('upcoming');">
				@lang('messages.upcoming_bookings')
			</button>
			<button type="button" class="border-0 btn btn-white me-4 px-0" :disabled="active_tab == 'past'" v-on:click="switchTab('past');">
				@lang('messages.past_bookings')
			</button>
			<button type="button" class="border-0 btn btn-white me-4 px-0" :disabled="active_tab == 'cancelled'" v-on:click="switchTab('cancelled');">
				@lang('messages.cancelled_bookings')
			</button>
		</div>
		<div class="row g-2 g-md-4" :class="{'loading': isLoading}">
			<div class="reservations-wrapper col-md-6 col-lg-4" v-for="(reservation,index) in reservations" :class="'list-'+reservation.list_type">
				<div class="rounded-xl overflow-hidden">
					<a :href="reservation.hotel_link" class="common-link listing-info">
						<div class="res-image position-relative">
							<div class="position-absolute list-type rounded-pill bg-white text-black px-3 py-1 box-shadow fs-6" v-if="reservation.list_type == 'experience'"> @lang('messages.experience') </div>
							<img :src="reservation.hotel_image_src" class="rounded-xl">
						</div>
					</a>
					<div class="reservation-description position-relative">
						<div class="position-absolute status-info top-0 start-50 translate-middle bg-dark text-white py-1 px-3 rounded-pill box-shadow border">
							<span>@{{ reservation.status }}</span>
						</div>
						<div class="d-flex justify-content-between mb-2 mt-3">
							<div class="res-date mb-1">
								@{{ reservation.dates }}
							</div>
							<div class="ms-2">
								<a v-if="reservation.status == 'Accepted' && reservation.phone_number != ''" class="common-link d-inline" :href="'tel:'+reservation.phone_number">
									<span class="icon icon-telephone me-1 align-middle"></span>
									@{{ reservation.phone_number }}
								</a>
							</div>
						</div>
						<div class="guest-info d-flex align-items-center mb-1">
							<div class="info flex-grow-1">
								<h3 class="m-0 text-truncate-1 mb-1"> @{{ reservation.company_name }} </h3>
								<div class="d-flex">
									<a v-if="reservation.status == 'Accepted'" class="theme-link d-block" :href="'mailto:'+reservation.contact_mail">
										@{{ reservation.contact_mail }}
									</a>
								</div>
							</div>
							<div class="profile">
								<img :src="reservation.company_logo_src">
							</div>
						</div>
						<a :href="reservation.hotel_link" class="res-head d-flex justify-content-between align-items-center mb-1 mt-2">
							<p class="m-0 text-truncate"> @{{ reservation.name }} </p>
							<p class="my-0 ms-2"><span class="icon icon-right-chevron"></span></p>
						</a>
					</div>
					<div class="payment-res d-flex trip-pay">
						<!-- <a :href="reservation.inbox_url" class="link flex-fill"> @lang('messages.message_history') </a> -->
						<a :href="reservation.itinerary_url" class="link flex-fill" v-if="reservation.status == 'Accepted'"> @lang('messages.view_itinerary') </a>
						<a :href="reservation.receipt_url" class="link flex-fill" v-if="reservation.status == 'Accepted' || reservation.status == 'Cancelled'"> @lang('messages.view_receipt') </a>
						<a href="javascript:;" class="link flex-fill" v-if="reservation.canCancelButtonShow" v-on:click="OpenCancelModal(index)"> @lang('messages.cancel_booking') </a>
						<a :href="reservation.review_url" class="link flex-fill" v-if="reservation.canReviewButtonShow"> @{{ reservation.review_text }} </a>
						<a :href="reservation.booking_url" class=" flex-fill" v-if="reservation.booking_url != ''"> @lang('messages.book_now') </a>
					</div>
				</div>
			</div>
			<div class="d-flex align-items-center justify-content-center" style="min-height: 200px" :class="{'d-none': reservations.length > 0 && !isContentLoading && !isLoading}">
				<h3>
					@lang('messages.no_bookings_found')
				</h3>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
	<script type="text/javascript">
		window.vueInitData = {!!
			json_encode([
				'active_tab' => $active_tab
			]);
		!!}
	</script>
@endpush