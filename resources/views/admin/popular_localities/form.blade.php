<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label class="form-label" for="popular_city_id"> @lang('admin_messages.popular_city') <em class="text-danger">*</em> </label>
			{!! Form::select('popular_city_id',$popular_cities,$result->popular_city_id,['class'=>'form-select','placeholder' => Lang::get('messages.select')]) !!}
			<span class="text-danger"> {{ $errors->first('popular_city_id') }} </span>
		</div>
		<div class="form-group">
			<label for="name"> @lang('admin_messages.display_name') <em class="text-danger">*</em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'name','placeholder' => Lang::get('admin_messages.name')]) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			{!! Form::hidden('place_id',null,['id' => 'place_id','v-model' => 'place_id']) !!}
			{!! Form::hidden('latitude',null,['id' => 'latitude','v-model' => 'latitude']) !!}
			{!! Form::hidden('longitude',null,['id' => 'longitude','v-model' => 'longitude']) !!}
			<label for="address"> @lang('admin_messages.address') <em class="text-danger">*</em> </label>
			{!! Form::text('address', old('address',$result->address), ['class' => 'form-control', 'id' => 'address', 'autocomplete' => 'off']) !!}
			<span class="text-danger"> {{ $errors->first('address') }} </span>
			@if($errors->first('latitude') || $errors->first('longitude') || $errors->first('place_id'))
				<span class="text-danger"> @lang('admin_messages.choose_from_autocomplete') </span>
			@endif
		</div>
		<div class="form-group">
			<label class="form-label" for="country_code"> @lang('admin_messages.country_code') </label>
			{!! Form::select('country_code',$country_list,$result->country_code ?? '',['class'=>'form-select','placeholder' => Lang::get('messages.select'),'v-model' => 'country_code']) !!}
			<span class="text-danger"> {{ $errors->first('country_code') }} </span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, old('status',$result->status), ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.popular_localities')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-success float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		"country_code" => old('country_code',$result->country_code ?? ''),
		"place_id" => old('place_id',$result->place_id ?? ''),
		"latitude" => old('latitude',$result->latitude ?? ''),
		"longitude" => old('longitude',$result->longitude ?? ''),
	]) !!};
</script>
@endpush