@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $main_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('host.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('host.users') }}">@lang("admin_messages.agents")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.edit")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('host.users.update',['id' => $result->id]), 'class' => 'form-horizontal','method' => "PUT",'files' => true]) !!}
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-body">
								@include('host.users.form')
								<div class="card-footer px-2 pt-4">
									<a href="{{ route('host.users')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
									<button type="submit" class="btn btn-primary float-end" id="add"> @lang('admin_messages.submit') </button>
								</div>
							</div>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection