<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="code"> @lang('admin_messages.promotion_title') <em class="text-danger">*</em> </label>
			{!! Form::text('code', old('code',$result->code), ['class' => 'form-control', 'id' => 'code']) !!}
			<span class="text-danger">{{ $errors->first('code') }}</span>
		</div>
		
		{!! Form::hidden('currency_code',session('currency')) !!}
		<div class="form-group">
			<label class="form-label d-block"> @lang('admin_messages.discount_type') </label>
			<div class="form-check-inline">
				<input class="form-check-input" type="radio" name="type" id="fixed_coupon" value="amount" v-model="type">
				<label class="form-check-label ms-2" for="fixed_coupon">
					@lang('admin_messages.fixed')
				</label>
			</div>
			<div class="form-check-inline">
				<input class="form-check-input" type="radio" name="type" id="percentage_coupon" value="percentage" v-model="type">
				<label class="form-check-label ms-2" for="percentage_coupon">
					@lang('admin_messages.percentage')
				</label>
			</div>
		</div>
		<div class="form-group">
			<label for="value"> @lang('admin_messages.discount_value') <em class="text-danger">*</em> </label>
			<div class="input-group">
				<div class="input-group-prepend" v-show="type == 'amount'">
				    <span class="input-group-text">{{ session('currency') }} </span>
			    </div>
				{!! Form::text('value',old('value',$result->value),['class'=>'form-control','id' => 'value']) !!}
				<div class="input-group-append" v-show="type == 'percentage'">
					<span class="input-group-text">%</span>
			    </div>
		    </div>
		    <span class="text-danger">{{ $errors->first('value') }} </span>
		</div>

		<div class="form-group">
			<label for="min_amount"> @lang('admin_messages.min_amount_to_apply') <em class="text-danger">*</em> </label>
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
			{!! Form::text('start_date', old('start_date',$result->start_date), ['class' => 'form-control', 'id' => 'date','readonly' => 'readonly','placeholder' => Lang::get('admin_messages.start_date')]) !!}
			<span class="text-danger">{{ $errors->first('start_date') }}</span>
		</div>

		<div class="form-group">
			<label for="end_date"> @lang('admin_messages.end_date') <em class="text-danger">*</em> </label>
			{!! Form::text('end_date', old('end_date',$result->end_date), ['class' => 'form-control', 'id' => 'date','readonly' => 'readonly','placeholder' => Lang::get('admin_messages.end_date')]) !!}
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
		<a href="{{ route('host.'.$active_menu) }}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		'type' => old("type",$result->type ?? 'amount'),
	]) !!}
</script>
@endpush