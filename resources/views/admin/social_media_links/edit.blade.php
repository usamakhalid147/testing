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
					<a href="javascript:;"> {{ $sub_title }} </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.social_media_links.update'), 'class' => 'form-horizontal','method' => "PUT"]) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						@foreach($social_media_links as $media)
						<div class="form-group">
							<label for="{{ $media->name }}"> @lang('admin_messages.'.$media->name) </label>
							{!! Form::text($media->name, old($media->name,$media->value), ['class' => 'form-control', 'id' => $media->name]) !!}
							<span class="text-danger">{{ $errors->first($media->name) }}</span>
						</div>
						@endforeach
					</div>
					<div class="card-action">
						<button type="reset" class="btn btn-danger"> @lang('admin_messages.reset') </button>
						<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection