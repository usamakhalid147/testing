@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.manage_hotels") </h4>
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
					<a href="{{ route('admin.hotels') }}">@lang("admin_messages.hotels")</a>
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
				{!! Form::open(['url' => route('admin.hotels.store'), 'class' => 'form-horizontal','id'=>'hotel_form','method' => "POST", 'files' => true]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="user"> @lang('admin_messages.manager_name') <em class="text-danger">*</em> </label>
							{!! Form::select('user_id', $users,'', ['class' => 'form-select', 'id' => 'user', 'placeholder' => Lang::get("admin_messages.select")]) !!}
							<span class="text-danger">{{ $errors->first('user_id') }}</span>
						</div>
						<div class="form-group col-md-12">
							<label for="property_type">
								@lang('admin_messages.property') @lang('messages.star_rating') <em class="text-danger">*</em>
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
							<label for="name">
								@lang('admin_messages.property') @lang('messages.name')
								<em class="text-danger">*</em>
							</label>
							{!! Form::text('name','',['id' => 'name','class'=>'form-control','placeholder' => Lang::get('admin_messages.property').' '.Lang::get('messages.name')]) !!}
							<p class="text-danger"> {{ $errors->first('name') }} </p>
						</div>
						<div class="form-group col-md-12">
							<label for="description">
								@lang('messages.about') @lang('admin_messages.property') <em class="text-danger">*</em></label>
							{!! Form::textarea('description','',['id' => 'description','class'=>'form-control','rows' => '4', 'placeholder' => Lang::get('messages.about').' '.Lang::get('admin_messages.property')]) !!}
							<p class="text-danger"> {{ $errors->first('description') }} </p>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.hotels')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
						<button type="submit" class="btn btn-success float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection