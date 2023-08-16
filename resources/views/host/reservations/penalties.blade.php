@extends('layouts.adminLayout.app')
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
					<a href="{{ route('host.penalties') }}">@lang("admin_messages.penalties")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.details")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="card-title"> @lang('admin_messages.penalty_details') </div>
					</div>
					<div class="card-body form-horizontal">
						<div class="row align-items-baseline">
							<label class="col-sm-4 col-form-label form-label"> @lang('admin_messages.username') </label>
							<div class="col-sm-8">
								<div class="form-group">
									<p class="form-text"> {{ $user->first_name }} </p>
								</div>
							</div>
						</div>
						@foreach($reservations as $reservation)
						<div class="row align-items-baseline">
							<label class="col-sm-4 col-form-label form-label">  @lang('admin_messages.reservation') <a href="{{ route('host.reservations.show',['id' => $reservation->id]) }}"> # {{ $reservation->id }}</a> </label>
							<div class="col-sm-8">
								<div class="form-group">
									<p class="form-text"> {{ $reservation->currency_symbol }} {{ $reservation->host_penalty }} </p>
								</div>
							</div>
						</div>
						@endforeach
					</div>
					<div class="card-action d-flex">
						<a href="{{ route('host.penalties')}}" class="btn btn-info"> @lang('admin_messages.back') </a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection