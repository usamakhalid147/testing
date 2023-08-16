<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="name"> @lang('admin_messages.name') <em class="text-danger"> * </em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			<label for="code"> @lang('admin_messages.code') <em class="text-danger"> * </em> </label>
			{!! Form::text('code', old('code',$result->code), ['class' => 'form-control', 'id' => 'code']) !!}
			<span class="text-danger">{{ $errors->first('code') }}</span>
		</div>
		<div class="form-group">
			<label for="symbol"> @lang('admin_messages.symbol') <em class="text-danger"> * </em> </label>
			{!! Form::text('symbol', old('symbol',$result->symbol), ['class' => 'form-control', 'id' => 'symbol']) !!}
			<span class="text-danger">{{ $errors->first('symbol') }}</span>
		</div>
		<div class="form-group">
			<label for="rate"> @lang('admin_messages.rate') <em class="text-danger"> * </em> </label>
			{!! Form::text('rate', old('rate',$result->rate), ['class' => 'form-control', 'id' => 'rate']) !!}
			<span class="text-danger">{{ $errors->first('rate') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get('admin_messages.select')]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.currencies')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>