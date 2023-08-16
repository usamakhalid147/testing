@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.manage_rooms") </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('admin.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('admin.hotels.edit',['id' => $hotel->id]) }}">@lang("admin_messages.hotel") : {{$hotel->name}}</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('admin.rooms',['id' => $hotel->id]) }}">@lang("admin_messages.rooms")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.add")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.rooms.store'), 'class' => 'form-horizontal','id'=>'hotel_form','method' => "POST", 'files' => true]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<input type="hidden" name="hotel_id" value="{{$hotel->id}}">
						<input type="hidden" name="user_id" value="{{$hotel->user_id}}">
						<div class="form-group">
							<label for="name">@lang('admin_messages.name')<em class="text-danger">*</em>
							</label>
							{!! Form::text('name',old('name',$result->name),['id' => 'name','class'=>'form-control','placeholder' => Lang::get('admin_messages.name')]) !!}
							<p class="text-danger"> {{ $errors->first('name') }} </p>
						</div>
						<div class="form-group">
							<label for="description">@lang('messages.description') <em class="text-danger">*</em></label>
							{!! Form::textarea('description',old('description',$result->description),['id' => 'description','class'=>'form-control','rows' => '4', 'placeholder' => Lang::get('messages.description')]) !!}
							<p class="text-danger"> {{ $errors->first('description') }} </p>
						</div>
						<div class="form-group">
							<label for="bed_type">@lang('admin_messages.bed_type') <em class="text-danger">*</em>
							</label>
							<select name="bed_type" id="bed_type" class="form-select" value="{{ old('bed_type',$result->bed_type) }}">
								<option value=""> @lang('messages.select') </option>
								@foreach($bed_types as $bed_type)
								<option value="{{ $bed_type->id }}"> {{ $bed_type->name }} </option>
								@endforeach
							</select>
							<p class="text-danger"> {{ $errors->first('bed_type') }} </p>
						</div>
						<div class="form-group">
							<label for="beds">@lang('admin_messages.beds') <em class="text-danger">*</em>
							</label>
							<input type="number" name="beds" class="form-control" placeholder="{{ Lang::get('admin_messages.beds') }}" value="{{ old('beds',$result->beds) }}">
							<p class="text-danger"> {{ $errors->first('beds') }} </p>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.rooms',['id' => $hotel->id])}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection