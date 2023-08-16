<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		{{--
		<div class="form-group">
			<label for="public_comment"> @lang('admin_messages.private_comment')</label>
			{!! Form::textarea('public_comment', old('public_comment',$result->private_comment), ['class' => 'form-control', 'id' => 'public_comment','rows' => 5,'disabled' => true]) !!}
			<span class="text-danger">{{ $errors->first('public_comment') }}</span>
		</div>
		--}}
		<div class="form-group">
			<label for="hotel_name" class="form-label"> @lang('admin_messages.hotel_name') </label>
			{!! Form::text('room_name', $result->hotel->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
		</div>
		<div class="form-group">
			<label for="rating"> @lang('messages.rating') <em class="text-danger"> * </em> </label>
			{!! Form::text('rating',old('rating',$result->rating),['class' => 'form-control','disabled' => true, 'readonly' => true]) !!}
		</div>
		<div class="form-group">
			<label for="public_comment"> @lang('messages.review_about_property')</label>
			{!! Form::textarea('public_comment', old('public_comment',$result->public_comment), ['class' => 'form-control', 'id' => 'public_comment','rows' => 5,'disabled' => true, 'readonly' => true]) !!}
			<span class="text-danger">{{ $errors->first('public_comment') }}</span>
		</div>
		<div class="form-group">
			<label for="public_comment"> @lang('messages.write_a_response') <em class="text-danger"> *</em></label>
			{!! Form::textarea('public_reply', old('public_reply',$result->public_reply), ['class' => 'form-control', 'id' => 'public_reply','rows' => 5]) !!}
			<span class="text-danger">{{ $errors->first('public_reply') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('host.reviews')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-success float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>