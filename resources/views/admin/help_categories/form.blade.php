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
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>

	<div class="card-action">
		<a href="{{ route('admin.help_categories')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>