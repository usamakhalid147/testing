<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
		{!! Form::hidden('place_id',null,['id' => 'place_id','v-model' => 'place_id']) !!}
		{!! Form::hidden('latitude',null,['id' => 'latitude','v-model' => 'latitude']) !!}
		{!! Form::hidden('longitude',null,['id' => 'longitude','v-model' => 'longitude']) !!}
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="city_name"> @lang('admin_messages.city_name') <em class="text-danger">*</em> </label>
			{!! Form::text('city_name', $result->city_name, ['class' => 'form-control', 'id' => 'city_name','autocomplete' => 'off']) !!}
			<span class="text-danger">{{ $errors->first('city_name') }}</span>
			@if($errors->first('latitude') || $errors->first('longitude') || $errors->first('place_id'))
				<span class="text-danger"> @lang('admin_messages.choose_from_autocomplete') </span>
			@endif
		</div>
		<div class="form-group">
			<label for="display_name"> @lang('admin_messages.display_name') <em class="text-danger">*</em> </label>
			{!! Form::text('display_name', $result->display_name, ['class' => 'form-control', 'id' => 'display_name']) !!}
			<span class="text-danger">{{ $errors->first('display_name') }}</span>
		</div>
		<div class="form-group">
			<label for="order_id"> @lang('admin_messages.order_id') <em class="text-danger">*</em> </label>
			{!! Form::text('order_id', $result->order_id, ['class' => 'form-control', 'id' => 'order_id']) !!}
			<span class="text-danger">{{ $errors->first('order_id') }}</span>
		</div>
		<div class="form-group input-file input-file-image">
			<img class="img-upload-preview" src="{{ $result->image_src ?? asset('images/preview_thumbnail.png') }}">
			<input type="file" class="form-control form-control-file" id="image" name="image" accept="image/*">
			<label for="image" class="label-input-file btn btn-default btn-round">
				<span class="btn-label"><i class="fa fa-file-image"></i></span>
				@lang('admin_messages.choose_file')
			</label>
			<span class="text-danger d-block">{{ $errors->first('image') }}</span>
		</div>
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">*</em> </label>
			{!! Form::select('status', $status_array, $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get("admin_messages.select")]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.featured_cities')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		"place_id" => old('place_id',$result->place_id ?? ''),
		"latitude" => old('latitude',$result->latitude ?? ''),
		"longitude" => old('longitude',$result->longitude ?? ''),
	]) !!};
</script>
@endpush