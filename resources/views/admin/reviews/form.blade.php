<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="reservation_id" class="form-label"> @lang('admin_messages.reservation_id') </label>
			{!! Form::text('reservation_id', $result->reservation_id, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		<div class="form-group">
			<label for="hotel_name" class="form-label"> @lang('admin_messages.hotel_name') </label>
			{!! Form::text('room_name', $result->hotel->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		<div class="form-group">
			<label for="user_from" class="form-label"> @lang('admin_messages.user_name') </label>
			{!! Form::text('user_from', $result->user_from, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		<div class="form-group">
			<label for="user_to" class="form-label"> @lang('admin_messages.user_to') </label>
			{!! Form::text('user_to', $result->user_to, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		<div class="form-group">
			<label for="review_by" class="form-label"> @lang('admin_messages.review_by') </label>
			{!! Form::text('review_by', Lang::get('admin_messages.'.$result->review_by), ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		{{--
		<div class="form-group">
			<label for="private_comment"> @lang('admin_messages.private_comment') *</label>
			{!! Form::textarea('private_comment', old('private_comment',$result->private_comment), ['class' => 'form-control', 'readonly' => 'readonly','rows' => 5]) !!}
			<span class="text-danger">{{ $errors->first('private_comment') }}</span>
		</div>
		--}}
		<div class="form-group">
			<label for="public_comment"> @lang('admin_messages.public_comment') *</label>
			{!! Form::textarea('public_comment', old('public_comment',$result->public_comment), ['class' => 'form-control', 'id' => 'public_comment','rows' => 5]) !!}
			<span class="text-danger">{{ $errors->first('public_comment') }}</span>
		</div>
		<div class="form-group">
			<label for="rating"> @lang('admin_messages.review') @lang('messages.rating') <em class="text-danger"> * </em> </label>
			{!! Form::select('rating', array_combine(range(1,5), range(1,5)), old('rating',$result->rating), ['class' => 'form-select', 'id' => 'rating']) !!}
			<span class="text-danger">{{ $errors->first('rating') }}</span>
		</div>
		<div class="form-group">
			<label for="recommend"> @lang('admin_messages.recommend') <em class="text-danger"> * </em> </label>
			{!! Form::select('recommend', $yes_no_array, old('recommend',$result->recommend), ['class' => 'form-select', 'id' => 'recommend']) !!}
			<span class="text-danger">{{ $errors->first('recommend') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.reviews')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>