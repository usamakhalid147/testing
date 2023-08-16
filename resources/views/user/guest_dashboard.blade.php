@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="card mt-4">
					<div class="card-body card-img-top p-0">
						<a href="{{ resolveRoute('view_profile',['id' => Auth::id()]) }}">
							<img src="{{ Auth::user()->profile_picture_src }}" class="my-4 img-fluid mx-auto d-block profile_image"/>
						</a>
					</div>
					<div class="card-footer text-center">
						<h3 class="text-muted"> {{ Auth::user()->first_name }} </h3>
						<a class="h6 d-block" href="{{ resolveRoute('view_profile',['id' => Auth::id()]) }}">
							@lang('messages.view_profile')
						</a>
						<a class="h6 d-block" href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}">
							@lang('messages.edit_profile')
						</a>
					</div>
				</div>
				<div class="card mt-4">
					<div class="card-header bg-light h6">
						@lang('messages.verified_info')
					</div>
					<div class="card-body">
						@if(Auth::user()->user_verification->canShow())
						<ul class="list-unstyled" title="@lang('messages.verified_info')">
							@foreach(VERIFICATION_METHODS as $method)
							@if(Auth::user()->user_verification->$method == 1)
							<li class="row">
								<div class="col-9 mt-1">
									@lang('messages.'.$method)
								</div>
								<div class="col-3">
									<i class="icon icon-tick" area-hidden="true"></i>
								</div>
							</li>
							@endif
							@endforeach
						</ul>
						@endif
						<a href="{{ resolveRoute('update_account_settings',['page' => 'login-and-security']) }}">
							@lang('messages.verify_more')
						</a>
					</div>
				</div>
				<div class="card mt-4">
					<div class="card-header bg-light h6">
						@lang('messages.quick_links')
					</div>
					<div class="card-body">
						<a class="h6 d-block" href="{{ resolveRoute('bookings') }}">
							<span class="icon me-2 opacity-75 icon-bookings"></span>
							@lang('messages.bookings') 
						</a>
						<a class="h6 d-block" href="{{ resolveRoute('reviews') }}"> 
							<span class="icon me-2 opacity-75 icon-reviews icon-outlined"></span>@lang('messages.reviews') 	
						</a>
					</div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="card mt-4">
					<div class="card-header bg-light h3">
						@lang('messages.reservations')
					</div>
					<div class="card-body">
						@if($reservations->count() > 0)
						<ul class="inbox-thread px-0">
							@foreach($reservations as $reservtion)
							<li class="d-flex py-2 py-md-3 border-bottom">
								<div class="col-2 col-md-1">
									<a href="{{ $reservtion['hotel_link'] }}">
										<img class="rounded-profile-image-normal" title="{{ $reservtion['name'] }}" src="{{ $reservtion['hotel_image_src'] }}">
									</a>
								</div>
								<div class="col-10 col-md-11 p-0 d-md-flex mt-1">
									<div class="ps-md-0 ps-lg-2 col-12 col-md-6">
										<a href="{{ $reservtion['hotel_link'] }}">
											<p class="text-truncate mb-2"> {{ $reservtion['name'] }} </p>
										</a>
										<p class="text-small h6 text-truncate"> {{ $reservtion['address'] }} </p>
									</div>
									<div class="col-12 col-md-4 my-1 my-md-0">
										<p class="mb-1 fw-bold"> {{ $reservtion['dates'] }} </p>
									</div>
									<div class="list-status col-12 col-md-3 my-1 my-md-0">
										<span class="d-block fw-bold">
											{{ $reservtion['status'] }}
										</span>
										<span class="total-price">
											{{ $reservtion['currency_symbol'] }} {{ $reservtion['total'] }}
										</span>
									</div>
								</div>
							</li>
							@endforeach
							<li class="d-flex pt-1 pt-md-2 justify-content-center">
								<a href="{{ resolveRoute('bookings') }}"> @lang('messages.see_all') </a>
							</li>
						</ul>
						@else
						<p> @lang('messages.no_reservations') </p>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection