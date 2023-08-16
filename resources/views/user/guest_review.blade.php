@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container">
		<div class="d-md-flex row py-4">
			{!! Form::open(['id' => 'review_form']) !!}
			<div class="col-md-12 review-details">
				<h2> @lang('messages.how_was_your_booking_at',['replace_key_1' => $review_user->first_name]) </h2>
				{!! Form::hidden('review_id',null,['id' => 'rating','v-model' => 'review.id']) !!}
				{!! Form::hidden('id',$reservation->id,['id' => 'reservation_id']) !!}
				<p> @lang('messages.we_wont_share_your_response') </p>
				<div class="rating-container">
					<div class="form-group">
						{!! Form::hidden('rating',null,['id' => 'rating','v-model' => 'review.rating']) !!}
						<div id="rating-stars"></div>
						<p class="text-danger"> @{{ (error_messages.rating) ? error_messages.rating[0] : '' }} </p>
					</div>
					{{--
					<div class="form-group">
						<p class="h4"> @lang('messages.add_a_private_note_to') {{ $review_user->full_name }} </p>
						<textarea name="private_comment" rows="5" class="form-control" placeholder="@lang('messages.add_a_private_note')"> @{{ review.private_comment }} </textarea>
						<p class="text-danger"> @{{ (error_messages.private_comment) ? error_messages.private_comment[0] : '' }} </p>
					</div>
					--}}
					<div class="form-group">
						<p class="h4"> @lang('messages.write_a_review') </p>
						<p class="h6"> @lang('messages.tell_future_travelers_about_this_listing') </p>
						<textarea name="public_comment" rows="5" class="form-control" placeholder="@lang('messages.write_a_public_review')" v-model="review.public_comment"></textarea>
						<p class="text-danger"> @{{ (error_messages.public_comment) ? error_messages.public_comment[0] : '' }} </p>
					</div>
					<div class="form-group row">
						<div class="photos-section d-flex align-items-center mb-2">
							<div class="add_photos-section">
								<div class="d-flex px-2">
									<input type="file" ref="file" class="d-none" name="photos[]" multiple="true" id="upload_photos" accept="image/*" v-on:change="previewPhoto($event);">
									<button type="button" class="btn btn-default" onclick="$('#upload_photos').trigger('click');">
										@lang('messages.add_photos')
										<i class="fa fa-upload ml-1" aria-hidden="true"></i>
									</button>
								</div>
								<p class="text-danger"> @{{ (error_messages.photos) ? error_messages.photos : '' }} </p>
							</div>
							<div class="ms-auto" v-show="review_photos.length > 0">
								@{{ review_photos.length }}
								<span v-show="!review_photos.length > 1"> @choice('messages.photo',1) </span>
								<span v-show="review_photos.length > 1"> @choice('messages.photo',2) </span>
							</div>
						</div>
						<div class="hotel_image-container mt-4 mx-0">
							<ul class="row list-unstyled hotel_image-row">
								{!! Form::hidden('removed_photos',null,[':value' => 'removed_photos.toString()']) !!}
								<li v-for="(image,index) in review_photos" class="image-wrapper col-md-4 mb-5" id="@{{ 'review_photo_'+image.id }}">
									{!! Form::hidden('image_ids',null,['class'=>'image_id',':value' => ' image.id']) !!}
									<p class="text-danger" v-if="image.is_error"> This Image Can't exceed 5 mb. </p>
									<div class="card">
										<img :src="image.image_src" class="card-img-top rounded hotel_image">
										<button type="button" class="hotel-delete_icon icon icon-delete" v-on:click="deletePhoto(index)" v-show="review_photos.length > 1">
										</button>
										<div class="card-body d-none">
											<p class="card-text">
												{!! Form::textarea('photos_description',null,['id' => 'description','class'=>'form-control','rows' => '2', 'v-model' => 'image.description']) !!}
											</p>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="form-group mt-5">
						<p class="h4"> @lang('messages.would_you_recommend_this_host') </p>
						<div class="thumbs-container">
							<input class="form-check-input" type="radio" value="0" name="recommend" id="not_recommend" :checked="review.recommend != '1'">
							<label class="form-check-label" for="not_recommend"> <i class="text-danger icon icon-thumbs-down"></i> </label>
							<input class="form-check-input" type="radio" value="1" name="recommend" id="recommend" :checked="review.recommend == '1'">
							<label class="form-check-label" for="recommend"> <i class="text-success icon icon-thumbs-up"></i> </label>
						</div>
						<span class="text-danger"> {{ $errors->first('recommend') }} </span>
						<p class="text-danger"> @{{ (error_messages.recommend) ? error_messages.recommend[0] : '' }} </p>
					</div>
				</div>
				<div class="col-md-12 border-top bg-light">
					<button type="button" class="btn btn-lg btn-primary float-end mt-4" v-on:click="writeReview()">
					@lang('messages.submit')
					</button>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
	$("#rating-stars").rateYo({
		fullStar: true,
		rating: '{!! $review->rating ?? 1 !!}',
		spacing: "10px",
		onSet: function (rating, instance) {
			$('#rating').val(rating);
		}
	});
	window.vueInitData = {!! json_encode([
		'review' => $review,
		'review_photos' => $review_photos ?? [],
	]) !!};
</script>
@endpush