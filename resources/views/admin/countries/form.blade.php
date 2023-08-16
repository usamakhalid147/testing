<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="name"> @lang('admin_messages.country_code_desc') <em class="text-danger"> * </em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			<label for="full_name"> @lang('admin_messages.country_full_name') <em class="text-danger"> * </em> </label>
			{!! Form::text('full_name', old('full_name',$result->full_name), ['class' => 'form-control', 'id' => 'full_name']) !!}
			<span class="text-danger">{{ $errors->first('full_name') }}</span>
		</div>
		<div class="form-group">
			<label for="iso3"> @lang('admin_messages.iso3') <em class="text-danger"> * </em> </label>
			{!! Form::text('iso3', old('iso3',$result->iso3), ['class' => 'form-control', 'id' => 'iso3']) !!}
			<span class="text-danger">{{ $errors->first('iso3') }}</span>
		</div>
		<div class="form-group">
			<label for="numcode"> @lang('admin_messages.numcode') <em class="text-danger"> * </em> </label>
			{!! Form::text('numcode', old('numcode',$result->numcode), ['class' => 'form-control', 'id' => 'numcode']) !!}
			<span class="text-danger">{{ $errors->first('numcode') }}</span>
		</div>
		<div class="form-group">
			<label for="phone_code"> @lang('admin_messages.phone_code') <em class="text-danger"> * </em> </label>
			{!! Form::text('phone_code', old('phone_code',$result->phone_code), ['class' => 'form-control', 'id' => 'phone_code']) !!}
			<span class="text-danger">{{ $errors->first('phone_code') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.countries')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>