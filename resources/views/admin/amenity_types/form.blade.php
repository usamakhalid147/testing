<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="name"> @lang('admin_messages.name') <em class="text-danger">*</em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>

		<div class="form-group">
			<label for="description"> @lang('admin_messages.description') <em class="text-danger">*</em> </label>
			{!! Form::text('description', old('description',$result->description), ['class' => 'form-control', 'id' => 'description']) !!}
			<span class="text-danger">{{ $errors->first('description') }}</span>
		</div>

		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, old('status',$result->status), ['class' => 'form-control', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>

	<div class="card-action">
		<a href="{{ route('admin.'.$active_menu) }}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>