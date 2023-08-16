@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container mt-4 pt-4" >
		<div class="row">
			<div class="col-md-3">
				<div class="border mt-4 rounded-4 overflow-hidden box-shadow-light">
					<div class="text-center card-img-top p-0 position-relative">
						<img src="{{ $user->profile_picture_src }}" class="d-block img-fluid mx-auto mb-4 profile_image"/>
						<span v-if="user.verification_status == 'Verified'" class="icon icon-verify-batch"></span>
						@checkUser($user->id)
						<a class="d-inline-block fw-light mb-3 text-center text-decoration-underline text-muted" href="{{ resolveRoute('update_account_settings',['page' => 'personal-information']) }}">
							@lang('messages.update_photo')
						</a>
						@endcheckUser
					</div>
					@if($user->user_verification->canShow())
					<div class="card-footer">
						<h3 class="mb-3"> @lang('messages.user_provided',['replace_key_1' => $user->first_name]) </h3>
						<ul class="list-unstyled mt-2" title="@lang('messages.verified_info')">
							@foreach(VERIFICATION_METHODS as $method)
							@if($user->user_verification->$method == 1)
							<li class="row g-3">
								<div class="col flex-grow-0">
									<i class="icon icon-tick" aria-hidden="true"></i>
								</div>
								<div class="col">
									@lang('messages.'.$method)
								</div>
							</li>
							@endif
							@endforeach
						</ul>
						@if(Auth::id() == $user->id)
						<a href="{{ resolveRoute('update_account_settings',['page' => 'login-and-security']) }}" class="d-inline-block mt-2">
							@lang('messages.verify_more')
						</a>
						@endif
					</div>
					@endif
				</div>
			</div>
			<div class="offset-md-1 col-md-8 mt-4">
				<h1 class="text-dark-gray"> @lang('messages.hi_iam',['replace_key_1' => $user->first_name]) </h1>
				<p class="text-truncate">
					@lang('messages.joined_in',['replace_key_1' => $user->created_at->year])
					@checkUser($user->id)
					<span class="dot">·</span>
					<a href="javascript:;" v-on:click="showUpdateOptions = true;"> @lang('messages.edit_profile') </a>
					@endcheckUser
				</p>
				<div class="profile_details mt-2" v-show="!showUpdateOptions">
					<div class="about_section my-4">
						<blockquote class="blockquote">
							<svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 409.294 409.294" height="20" viewBox="0 0 409.294 409.294" width="20" style="fill:#484848;"><path d="m0 204.647v175.412h175.412v-175.412h-116.941c0-64.48 52.461-116.941 116.941-116.941v-58.471c-96.728 0-175.412 78.684-175.412 175.412z"/><path d="m409.294 87.706v-58.471c-96.728 0-175.412 78.684-175.412 175.412v175.412h175.412v-175.412h-116.941c0-64.48 52.461-116.941 116.941-116.941z"/></svg>
							<p class="mb-0 text-justify">
								@{{user.user_information.about}}
							</p>
							<div class="dropdown-divider"></div>
						</blockquote>
					</div>
					<p class="">
						<i class="icon icon-map icon-outlined" area-hidden="true"></i> <span> @lang('messages.lives_in') @{{user.user_information.location}} </span>
					</p>
					<p v-show="original_user.user_information.language_array.display_list != ''">
						<i class="icon icon-chat icon-outlined" area-hidden="true"></i> <span> @lang('messages.speaks') </span>
						@{{ original_user.user_information.language_array.display_list }}
					</p>
					<p class="">
						<i class="icon icon-work" area-hidden="true"></i> <span> @lang('messages.work') @{{user.user_information.work}} </span>
					</p>
				</div>
				<div class="update_form mt-2" v-show="showUpdateOptions">
					<div class="form-group">
						<label class="form-label">
							@lang('messages.about')
						</label>
						{!! Form::textarea('about', null, ['class' => 'form-control', 'rows' => 4, 'cols' => 25, 'v-model' => 'user.user_information.about']) !!}
					</div>
					<div class="form-group">
						<label for="location" class="form-label">
							@lang('messages.location')
						</label>
						{!! Form::text('location',null, ['class' => 'form-control', 'v-model' => 'user.user_information.location']) !!}
					</div>
					<input type="hidden" name="language" value="USD">
					{{--<div class="form-group">
						<label for="work" class="form-label">
							@lang('messages.known_languages')
						</label>
						{!! Form::select('language',$language_list,'', ['class' => 'w-100 language select-picker', 'multiple' => 'true', 'v-model' => 'user.user_information.language_array.code']) !!}
					</div>--}}
					<div class="form-group">
						<label for="work" class="form-label">
							@lang('messages.work')
						</label>
						{!! Form::text('work',null, ['class' => 'form-control', 'v-model' => 'user.user_information.work']) !!}
					</div>
					<div class="form-group mt-4">
						<button type="button" class="btn btn-primary justify-content-center" v-on:click="updateUserProfile();" :class="{'loading' : isLoading}">
						@lang('messages.save')
						</button>
						<button type="button" class="btn btn-default ms-2 justify-content-center" v-on:click="showUpdateOptions = false;user = original_user">
						@lang('messages.cancel')
						</button>
					</div>
				</div>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="pills-hotels" role="tabpanel" aria-labelledby="pills-hotels-tab">
						@if($hotel_wishlists->count() > 0)
						<div class="mt-4">
							@foreach($hotel_wishlists as $wishlist)
							<h4 class="title">
							{{ $wishlist['name'] }}
							@if($wishlist['privacy'] == 0)
							<i class="icon icon-lock"></i>
							@endif
							</h4>
							<ul id="wishlist-slider_{{ $wishlist['id'] }}" class="room-preview wishlist-slider m-2">
								@foreach($wishlist['wishlist_lists'] as $list)
								<div class="listing-wrapper mb-3">
									<div class="card">
										<a class="common-link listing-info" href="{{ $list['link'] }}">
											<div class="listing-image">
												<img class="tns-lazy-img listing-img hover-card" data-src="{{ $list['image_src'] }}" alt="{{ $list['name'] }}">
											</div>
										</a>
										<div class="p-2">
											<div class="listing-type-header d-flex align-items-center">
												<div class="listing-info">
													<h6 class="text-truncate m-0">
													<span> {{ $list['room_type_name'] }} </span>
													<span>·</span>
													<span> {{ $list['city'] }} </span>
													</h6>
												</div>
												@if($list['total_rating'] > 0)
												<div class="review-header d-flex ms-auto">
													<div class="review-star"> <span class="icon icon-star"></span> </div>
													<div class="review-count"> {{ $list['rating'] }} <span class="text-gray"> ({{ $list['total_rating'] }}) </span> </div>
												</div>
												@endif
											</div>
											<div class="listing-details">
												<a class="common-link listing-info" href="{{ $list['link'] }}">
													<h2 class="ellipsis block-xs-center" title="{{ $list['name'] }}"> {{ $list['name'] }} </h2>
												</a>
												<p class="h5">
													<span class="h4"> {{ $list['currency_symbol'].' '.$list['price'] }} </span>
													<span class="text-gray"> / @choice('messages.night',1) </span>
												</p>
											</div>
										</div>
									</div>
								</div>
								@endforeach
							</ul>
							@endforeach
						</div>
						@endif
						@checkUser($user->id)
						<div class="mb-4">
							<a href="{{ resolveRoute('reviews') }}"> @lang('messages.reviews_by_you') </a>
						</div>
						@endcheckUser
						@if($user->reviews->count() > 0)
						<div class="dropdown-divider"></div>
						<div class="my-4">
							<div class="review-container">
								<h2 class="title"> {{ $review_count }} @choice('messages.review',$review_count) </h2>
								@foreach($user->reviews as $review)
								<div class="user-review mt-4">
									<div class="mt-4 d-flex">
										<div class="review-user">
											<a class="media-photo media-round align-top" href="{{ resolveRoute('view_profile',['id' => $review->user_from])}}">
												<img class="profile-image" src="{{ $review->user->profile_picture_src }}" title="{{ $review->user->first_name }}">
											</a>
										</div>
										<div class="ms-2 ps-2">
											<h5 class="text-black d-block"> {{ $review->user->first_name }}, {{ $review->user->location }} </h5>
											<h6 class="text-black d-block"> @lang('messages.joined_in',['replace_key_1' => $review->user->created_at->format('Y') ]) </h6>
										</div>
									</div>
									<div class="mb-2">
										<p class="h6 text-gray d-block"> {{ $review->created_at->format('F Y') }} </p>
										<p class="fs-6 fw-lighter text-justify"> {{ $review->public_comment }} </p>
									</div>
								</div>
								@endforeach
							</div>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script>
	window.vueInitData = {!! json_encode([
		'user'	=> $user,
		'original_user'	=> $user,
		'user_birthday' => [
			'month' => $user->user_information->dob->format('n'),
			'year' => $user->user_information->dob->format('Y'),
			'day'	=> $user->user_information->dob->format('j'),
		],
	]) !!};
	
	document.addEventListener('DOMContentLoaded', function() {
		updateSlider('#profile-slider','profile');
		updateSlider('#profile-sliders','profile');
		document.querySelectorAll('.wishlist-slider').forEach(slider => {
			updateSlider('#'+slider.id,'wishlist');
		});
		document.querySelectorAll('.wishlist-slider1').forEach(slider => {
			updateSlider('#'+slider.id,'wishlist');
		});
	});
</script>
@endpush