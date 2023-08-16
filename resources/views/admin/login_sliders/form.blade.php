<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="order_id"> @lang('admin_messages.order_id') <em class="text-danger">*</em> </label>
			{!! Form::text('order_id', $result->order_id, ['class' => 'form-control', 'id' => 'order_id']) !!}
			<span class="text-danger">{{ $errors->first('order_id') }}</span>
		</div>
		<div class="form-group input-file input-file-image">
			<img class="img-upload-preview" src="{{ $result->image_src ?? asset('images/preview_thumbnail.png') }}">
			<input type="file" class="form-control form-control-file" id="image" name="image" accept="image/*">
			<label for="image" class="label-input-file btn btn-default btn-round">
				<span class="btn-label"><i class="fa fa-file-image"></i></span>
				@lang('admin_messages.choose_file')
			</label>
			<span class="text-danger d-block">{{ $errors->first('image') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.login_sliders')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>