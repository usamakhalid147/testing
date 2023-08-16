@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container mt-4 pt-4">
		<div class="row">
			<div class="col-12">
				<h6 class="d-block">
				<a class="common-link" href="{{ resolveRoute('view_profile',['id' => Auth::id()]) }}"> @lang('messages.profile') </a>
				<i class="icon icon-arrow-right" area-hidden="true"></i>
				<a class="site-color" href="#"> @lang('messages.reviews') </a>
				</h6>
				<h3 class="text-dark-gray fw-bold"> @lang('messages.reviews_by_you') </h3>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-lg-9 mt-4 mt-md-0">
            	<div class="card mt-4">
					<div class="card-header fw-bolder"> @lang('messages.reviews_to_write') </div>
					<div class="card-body">
						<p>@lang('messages.reviews_are_written_after_checkout') </p>
						@forelse($reviews_to_write as $reservation)
						<li class="list-unstyled review mt-3 d-flex">
							<div class="review-header text-center pe-3 pe-md-4">
								<a href="{{ resolveRoute('view_profile',['id' => $reservation['user_id']]) }}">
									<img class="img img-thumbnail" width="100" height="100" title="{{ $reservation['first_name'] }}" src="{{ $reservation['profile_picture'] }}" alt="{{ $reservation['first_name'] }}">
								</a>
							</div>
							<div class="review-body">
								<p>
									@lang('messages.you_have') <b> {{ $reservation['review_days'] }} @choice('messages.days',$reservation['review_days']) </b> @lang('messages.to_submit_public_review')
									<a href="{{ resolveRoute('view_profile',['id' => $reservation['user_id']]) }}"> {{ $reservation['full_name'] }} </a>
								</p>
								<a class="d-block" href="{{ resolveRoute('edit_review',['id' => $reservation['id']]) }}">
									@lang('messages.write_review')
								</a>
								<a class="d-block" href="{{ $reservation['itinerary_url'] }}">
									@lang('messages.view_itinerary')
								</a>
							</div>
						</li>
						@empty
						<p> @lang('messages.nobody_to_review_right_now') </p>
						@endforelse
					</div>
				</div>
				<div class="card mt-4">
					<div class="card-header fw-bolder"> @lang('messages.past_reviews_written') </div>
					<div class="card-body">
						@forelse($past_reviews as $review)
						<li class="list-unstyled review mt-3 d-flex">
							<div class="review-header text-center pe-3 pe-md-4">
								<a  href="{{ resolveRoute('view_profile',['id' => $review['user_id']]) }}">
									<img width="100" height="100" class="img img-thumbnail" title="{{ $review['first_name'] }}" src="{{ $review['profile_picture'] }}" alt="{{ $review['first_name'] }}">
								</a>
								<div class="review_user-name">
									<a href="{{ resolveRoute('view_profile',['id' => $review['user_id']]) }}">
										{{ $review['full_name'] }}
									</a>
								</div>
							</div>
							<div class="review-body">
								<p class="mb-0"> {{ $review['public_comment'] }} </p>
								@if($review['review_days'] > 0)
								<p>
									<a href="{{ resolveRoute('view_profile',['id' => $review['user_id']]) }}"></a>
								</p>
								@endif
								<a class="d-block" href="{{ $review['itinerary_url'] }}">
									@lang('messages.view_itinerary')
								</a>
								@if($review['public_reply'])
								<a data-bs-toggle="collapse" href="#replyCollapse_{{$review['id']}}">
									View Reply
								</a>
								<div class="collapse" id="replyCollapse_{{$review['id']}}">
									<p class="text-justify">
										{{ $review['public_reply'] }}
									</p>
								</div>
								@endif
							</div>
						</li>
						@empty
						<p> @lang('messages.you_have_not_written_reviews') </p>
						@endforelse
					</div>
				</div>
				@if($expired_reviews->count())
				<div class="card mt-4">
					<div class="card-header fw-bolder">
						@lang('messages.expired_reviews')
					</div>
					<div class="card-body">
						@foreach($expired_reviews as $reservation)
						<li class="list-unstyled review mt-3 d-flex">
							<div class="review-header text-center pe-3 pe-md-4">
								<a href="{{ resolveRoute('view_profile',['id' => $reservation['user_id']]) }}">
									<img class="img img-thumbnail" width="100" height="100" title="{{ $reservation['first_name'] }}" src="{{ $reservation['profile_picture'] }}" alt="{{ $reservation['first_name'] }}">
								</a>
							</div>
							<div class="review-body">
								<p>
									@lang('messages.your_time_to_write_review')
									<a href="{{ resolveRoute('view_profile',['id' => $reservation['user_id']]) }}"> {{ $reservation['full_name'] }} </a>
									@lang('messages.has_expired')
								</p>
								<a class="d-block" href="{{ $reservation['itinerary_url'] }}">
									@lang('messages.view_itinerary')
								</a>
							</div>
						</li>
						@endforeach
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
</main>
@endsection