<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="title"> @lang('admin_messages.title') <em class="text-danger">*</em> </label>
			{!! Form::text('title', $result->title, ['class' => 'form-control', 'id' => 'title']) !!}
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
			<label for="category"> @lang('admin_messages.category') <em class="text-danger">*</em> </label>
			{!! Form::select('category', $categories, $result->category_id, ['class' => 'form-select', 'id' => 'category', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('category') }}</span>
		</div>

		<div class="form-group">
			<label for="content"> @lang('admin_messages.content') <em class="text-danger">*</em> </label>
			<textarea name="content" class="form-control rich-text-editor" id="content">{{ $result->content }}</textarea>
			<span class="text-danger">{{ $errors->first('content') }}</span>
		</div>

		<div class="form-group">
			<label for="is_popular"> @lang('admin_messages.is_popular') <em class="text-danger">*</em> </label>
			{!! Form::select('is_popular', $yes_no_array, $result->is_popular, ['class' => 'form-select', 'id' => 'is_popular', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('is_popular') }}</span>
		</div>
		
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>

	<div class="card-action">
		<a href="{{ route('admin.blogs')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>