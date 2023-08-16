@extends('layouts.app')
@section('content')
<main role="main" id="main_container">
	<div class="container mt-4">
		<div class="row">
			{!! Form::open(['url' => route('create_listing'), 'method' => 'POST' ]) !!}
			<div class="col-md-8">
				<div class="mb-4">
					<h3>Create New Hotel</h3>
				</div>
				<div class="form-group col-md-12">
					<label for="property_type">
						@lang('messages.star_rating') <em class="text-danger">*</em>
					</label>
					<select name="star_rating" id="star_rating" class="form-select">
						<option value=""> @lang('messages.select') </option>
						@foreach($star_rating_array as $star_rating)
						<option value="{{ $star_rating['key'] }}" > {{ $star_rating['value'] }} </option>
						@endforeach
					</select>
					<p class="text-danger"> {{ $errors->first('star_rating') }} </p>
				</div>
				<div class="form-group col-md-12">
					<label for="property_type">
						@lang('messages.property_type') <em class="text-danger">*</em>
					</label>
					<select name="property_type" id="property_type" class="form-select">
						<option value=""> @lang('messages.select') </option>
						@foreach($property_types as $property_type)
						<option value="{{ $property_type->id }}" ng-selected="hotel.property_type == {{ $property_type->id }}"> {{ $property_type->name }} </option>
						@endforeach
					</select>
					<p class="text-danger"> {{ $errors->first('property_type') }} </p>
				</div>
				<div class="form-group col-md-12">
					<label for="name">
						@lang('messages.hotel_name')
						<em class="text-danger">*</em>
					</label>
					{!! Form::text('name','',['id' => 'name','class'=>'form-control','placeholder' => Lang::get('messages.hotel_name')]) !!}
					<p class="text-danger"> {{ $errors->first('name') }} </p>
				</div>
				<div class="form-group col-md-12">
					<label for="description">
						@lang('messages.description') <em class="text-danger">*</em></label>
					{!! Form::textarea('description','',['id' => 'description','class'=>'form-control','rows' => '4', 'placeholder' => Lang::get('messages.description')]) !!}
					<p class="text-danger"> {{ $errors->first('description') }} </p>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary float-end">@lang('messages.submit')</button>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection