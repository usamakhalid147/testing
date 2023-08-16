<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="code"> @lang('admin_messages.code') <em class="text-danger"> * </em> </label>
			{!! Form::text('code', old('code',$result->code), ['class' => 'form-control', 'id' => 'code']) !!}
			<span class="text-danger">{{ $errors->first('code') }}</span>
		</div>
		<div class="form-group">
			<label for="name"> @lang('admin_messages.name') <em class="text-danger"> * </em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			<label for="is_translatable"> @lang('admin_messages.is_translatable') <em class="text-danger"> * </em> </label>
			{!! Form::select('is_translatable', $yes_no_array, old('is_translatable',$result->is_translatable), ['class' => 'form-select', 'id' => 'is_translatable']) !!}
			<span class="text-danger">{{ $errors->first('is_translatable') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
			{!! Form::select('status', $status_array, old('status',$result->status), ['class' => 'form-select', 'id' => 'status']) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.languages')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>