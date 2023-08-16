<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		{!! Form::hidden('type','amount') !!}
		<div class="form-group">
			<label for="code"> @lang('admin_messages.discount_title') <em class="text-danger">*</em> </label>
			{!! Form::text('code', old('code',$result->code), ['class' => 'form-control', 'id' => 'code']) !!}
			<span class="text-danger">{{ $errors->first('code') }}</span>
		</div>
		
		<div class="form-group">
			<label for="currency_code"> @lang('admin_messages.currency_code') <em class="text-danger">*</em> </label>
			{!! Form::select('currency_code', $currencies, old('currency_code',$result->getRawOriginal('currency_code')), ['class' => 'form-select', 'id' => 'currency_code']) !!}
			<span class="text-danger">{{ $errors->first('currency_code') }}</span>
		</div>
		
		<div class="form-group">
			<label for="value"> @lang('admin_messages.value') <em class="text-danger">*</em> </label>
			{!! Form::text('value', old('value',$result->value), ['class' => 'form-control', 'id' => 'value']) !!}
			<span class="text-danger">{{ $errors->first('value') }}</span>
		</div>

		<div class="form-group">
			<label for="min_amount"> @lang('admin_messages.minimum_amount_to_apply') <em class="text-danger">*</em> </label>
			{!! Form::text('min_amount', old('min_amount',$result->min_amount), ['class' => 'form-control', 'id' => 'min_amount']) !!}
			<span class="text-danger">{{ $errors->first('min_amount') }}</span>
		</div>

		<div class="form-group">
			<label for="per_user_limit"> @lang('admin_messages.per_user_limit') <em class="text-danger">*</em> </label>
			{!! Form::text('per_user_limit', old('per_user_limit',$result->per_user_limit), ['class' => 'form-control', 'id' => 'per_user_limit']) !!}
			<span class="text-danger">{{ $errors->first('per_user_limit') }}</span>
		</div>

		<div class="form-group">
			<label for="per_list_limit"> @lang('admin_messages.per_list_limit') <em class="text-danger">*</em> </label>
			{!! Form::text('per_list_limit', old('per_list_limit',$result->per_list_limit), ['class' => 'form-control', 'id' => 'per_list_limit']) !!}
			<span class="text-danger">{{ $errors->first('per_list_limit') }}</span>
		</div>
		
		<div class="form-group">
			<label for="start_date"> @lang('admin_messages.start_date') <em class="text-danger">*</em> </label>
			{!! Form::text('start_date', old('start_date',$result->start_date), ['class' => 'form-control', 'id' => 'start_date','readonly' => 'readonly']) !!}
			<span class="text-danger">{{ $errors->first('start_date') }}</span>
		</div>

		<div class="form-group">
			<label for="end_date"> @lang('admin_messages.end_date') <em class="text-danger">*</em> </label>
			{!! Form::text('end_date', old('end_date',$result->end_date), ['class' => 'form-control', 'id' => 'end_date','readonly' => 'readonly']) !!}
			<span class="text-danger">{{ $errors->first('end_date') }}</span>
		</div>

		<div class="form-group">
			<label for="visible_on_public"> @lang('admin_messages.visible_on_public') <em class="text-danger">*</em> </label>
			{!! Form::select('visible_on_public', $yes_no_array, old('visible_on_public',$result->visible_on_public), ['class' => 'form-select', 'id' => 'visible_on_public', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('visible_on_public') }}</span>
		</div>

		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, old('status',$result->status), ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.'.$active_menu) }}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>