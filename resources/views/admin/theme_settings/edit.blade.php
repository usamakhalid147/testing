@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
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
					<a href="#"> {{ $sub_title }} </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.theme_settings.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div class="form-group">
							<label for="font_family"> @lang('admin_messages.font_family') <em class="text-danger"> * </em> </label>
							{!! Form::text('font_family', old('font_family',global_settings('font_family')), ['class' => 'form-control', 'id' => 'font_family']) !!}
							<span class="text-danger">{{ $errors->first('font_family') }}</span>
						</div>
						<div class="form-group">
							<label for="font_script_url"> @lang('admin_messages.font_script_url') <em class="text-danger"> * </em> </label>
							{!! Form::text('font_script_url', old('font_script_url',global_settings('font_script_url')), ['class' => 'form-control', 'id' => 'font_script_url']) !!}
							<span class="text-danger">{{ $errors->first('font_script_url') }}</span>
						</div>
					</div>
					<div class="card-action">
						<a href="{{ route('admin.theme_settings') }}" class="btn btn-danger"> @lang('admin_messages.cancel') </a>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection