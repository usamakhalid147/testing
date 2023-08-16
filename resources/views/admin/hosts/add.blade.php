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
					<a href="{{ route('admin.hosts') }}">@lang("admin_messages.hosts")</a>
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
				{!! Form::open(['url' => route('admin.hosts.store'), 'class' => 'form-horizontal','files' => true]) !!}
				<div class="card">
				    <div class="card-header">
				        <div class="card-title"> {{ $sub_title }} </div>
				    </div>
				    <div class="card-body">
						@include('admin.hosts.form')
					</div>
					<div class="card-action">
				        <a href="{{ route('admin.hosts')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
				        <button type="submit" class="btn btn-primary float-end" id="add"> @lang('admin_messages.submit') </button>
				    </div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection