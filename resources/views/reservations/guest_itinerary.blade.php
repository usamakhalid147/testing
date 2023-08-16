@extends('layouts.app')
@section('content')
<main role="main" id="receipt" class="main-container pt-4">
	<div class="container">
		<div class="mb-4">
			<h2 class="fw-bold">
			@lang('messages.you_are_going_to')
			{{ $reservation->hotel->hotel_address->city }}!
			</h2>
			<p class="mt-2">
				@lang('messages.confirmation_code') : <span class="fw-bold"> {{ $reservation->code }} </span>
			</p>
			<div class="row mt-4">
				<div class="col-md-8">
					<div class="card">
						<div class="card-body">
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.checkin') </strong>
								</div>
								<div class="col-md-8">
									<p> {{ $reservation->formatted_checkin }} <span class="text-gray"> {{ $reservation->getTimingText('checkin_at') }} </span> </p>
								</div>
							</div>
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.checkout') </strong>
								</div>
								<div class="col-md-8">
									<p> {{ $reservation->formatted_checkout }} <span class="text-gray"> {{ $reservation->getTimingText('checkout_at') }} </span> </p>
								</div>
							</div>
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.room') </strong>
								</div>
								<div class="col-md-8">
									@foreach($reservation->room_reservations->groupBy('room_id') as $key => $room)
									<p class="mb-0"> {{ $room->first()->hotel_room->name }} x  {{ $room->count() }} 
									</p>
									<p class="detail-persons mb-0">
										<span> {{ $room->sum('adults') }} {{ $room->sum('adults') > 1 ? Lang::get('messages.adults') : 
										Lang::get('messages.adult') }} </span>
										@if($room->sum('children') > 0)
											<span>, </span>
											<span> {{ $room->sum('children') }} {{ $room->sum('children') > 1 ? Lang::get('messages.children') : Lang::get('messages.child') }} </span>
										@endif
									</p>
									@endforeach
								</div>
							</div>
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.address') </strong>
								</div>
								<div class="col-md-8">
									{{ $reservation->hotel->hotel_address->full_address }}
								</div>
							</div>
							@if($reservation->hotel->hotel_address->guidance != '')
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.checkin_guidance') </strong>
								</div>
								<div class="col-md-8">
									{{ $reservation->hotel->hotel_address->guidance }}
								</div>
							</div>
							@endif
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<p class="fw-bold"> @lang('messages.contacts') </p>
								</div>
								<div class="col-md-5">
									<span class="fw-bolder"> {{ $reservation->hotel->name }} </span>
									<div class="d-print-none">
										@If($reservation->status == 'Accepted')
										<a class="primary-link d-block" href="mailto:{{ $reservation->hotel->contact_email }}">
											@lang('messages.contact_by_email')
										</a>
										<p class="d-block">
											{{ $reservation->hotel->contact_no }}
										</p>
										@endif
									</div>
									<div class="d-none d-print-block">
										{{ $reservation->host_user->email }}
									</div>
								</div>
							</div>
							<div class="row border-bottom py-4">
								<div class="col-md-4">
									<strong> @lang('messages.hotel_rules') </strong>
								</div>
								<div class="col-md-8">
									@if($reservation->hotel->hotel_rules != '')
										<p class="mb-2">
											{{ $reservation->hotel->hotel_rules }}
										</p>
									@endif
								</div>
							</div>
							<div class="row py-4">
								<div class="col-md-4">
									<strong> @lang('messages.billing') </strong>
								</div>
								<div class="col-md-8">
									<div class="row">
										<div class="col-md-6">
											{{ $reservation->total_nights }} @choice('messages.night',2) @lang('messages.total')
										</div>
										<div class="col-md-6">
											{{ $reservation->currency_symbol }} {{ $reservation->getTotalBookingAmount() }}
										</div>
									</div>
									@if($reservation->status == 'Accepted')
									<a href="{{ resolveRoute('view_receipt',[$reservation->code]) }}"> @lang('messages.get_receipt') </a>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<img class="img-fluid profile_image" v-lazy="'{{ $reservation->hotel->image_src }}'">
					<div class="hotel_address text-center mt-3">
						<a href="{{ resolveRoute('hotel_details',['id' => $reservation->hotel_id]) }}" class="h5"> {{ $reservation->hotel->name }} </a>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection