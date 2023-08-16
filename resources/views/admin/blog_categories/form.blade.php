<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="title"> @lang('admin_messages.title') <em class="text-danger"> * </em> </label>
			{!! Form::text('title', old('title',$result->title), ['class' => 'form-control', 'id' => 'title']) !!}
			<span class="text-danger">{{ $errors->first('title') }}</span>
		</div>
		
		<div class="form-group">
			<label for="slug">
				@lang('admin_messages.slug')
				<span class="d-block help-block text-muted"> (@lang('admin_messages.leave_empty_to_use_default')) </span>
			</label>
			{!! Form::text('slug', $result->slug, ['class' => 'form-control', 'id' => 'slug']) !!}
			<span class="text-danger">{{ $errors->first('slug') }}</span>
		</div>

		<div class="form-group">
			<label for="description"> @lang('admin_messages.description') </label>
			{!! Form::textarea('description', $result->description, ['rows' => 3, 'class' => 'form-control', 'id' => 'description']) !!}
			<span class="text-danger">{{ $errors->first('description') }}</span>
		</div>
		<div class="form-group input-file input-file-image">
			<img class="img-upload-preview" src="{{ ($result->image != '') ? $result->image_src : asset('images/preview_thumbnail.png') }}">
			<input type="file" class="form-control form-control-file" id="image" name="image" accept="image/*">
			<label for="image" class="label-input-file btn btn-default btn-round">
				<span class="btn-label"><i class="fa fa-file-image"></i></span>
				@lang('admin_messages.choose_file')
			</label>
			<span class="text-danger d-block">{{ $errors->first('image') }}</span>
		</div>
		<div class="form-group">
			<label for="is_popular"> @lang('admin_messages.is_popular') <em class="text-danger"> * </em> </label>
			{!! Form::select('is_popular', $yes_no_array, $result->is_popular, ['class' => 'form-select', 'id' => 'is_popular', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('is_popular') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>

	<div class="card-action">
		<a href="{{ route('admin.blog_categories')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>