<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="name"> @lang('admin_messages.city_name') <em class="text-danger"> * </em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			<label for="roman_number">@lang('admin_messages.alternate_number') <em class="text-danger"> * </em> </label>
			{!! Form::text('roman_number', old('roman_number',$result->roman_number), ['class' => 'form-control', 'id' => 'roman_number']) !!}
			<span class="text-danger">{{ $errors->first('roman_number') }}</span>
		</div>
		<div class="form-group">
			<label for="">@lang('admin_messages.full_country_name') <em class="text-danger"> *</em></label>
			{!! Form::select('country',$country_list,old('country',$result->country),['class' => 'form-control', 'id' => 'country']) !!}
			<span class="text-danger">{{ $errors->first('country') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, old('status',$result->status), ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.cities')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>