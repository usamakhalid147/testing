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
					<a href="#">@lang("admin_messages.edit")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.hosts.update',['id' => $result->id]), 'class' => 'form-horizontal','method' => "PUT",'files' => true]) !!}
				<div class="row">
					<div class="col-md-8">
						<div class="card card-with-nav">
							<div class="card-header">
								<div class="row row-nav-line">
									<ul class="nav nav-tabs nav-line nav-color-secondary w-100 ps-3" id="pills-tab" role="tablist">
										<li class="nav-item submenu">
											<a class="nav-link active show" id="basics-tab" data-bs-toggle="tab" href="#basics" role="tab" aria-controls="basics" aria-selected="true"> @lang('messages.basics') </a>
										</li>
										<li class="nav-item submenu">
											<a class="nav-link" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab" aria-controls="transactions" aria-selected="false"> @lang('admin_messages.transactions') </a>
										</li>
										<li class="nav-item submenu">
											<a class="nav-link" id="reports-tab" data-bs-toggle="tab" href="#reports" role="tab" aria-controls="reports" aria-selected="false"> @lang('messages.reports') </a>
										</li>
									</ul>
								</div>
							</div>
							<div class="card-body">
								<div class="tab-content mt-2 mb-3">
									<div class="tab-pane fade active show" id="basics" role="tabpanel" aria-labelledby="basics-tab">
										@include('admin.hosts.form')
										<div class="card-footer px-2 pt-4">
											<a href="{{ route('admin.hosts')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
											<button type="submit" class="btn btn-primary float-end" id="add"> @lang('admin_messages.submit') </button>
										</div>
									</div>
									<div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
										<div class="card-body">
											@foreach($user_transactions as $transaction)
											<div class="d-flex">
												<div class="ms-4 pt-1">
													<h6 class="text-uppercase fw-bold mb-1">
														<a href="{{ $transaction['link'] }}"> #{{ $transaction['link_text'] }} </a>
													</h6>
													<a href="{{ $transaction['link'] }}">
														<span class="text-muted"> {{ $transaction['type'] }} </span>
													</a>
												</div>
												<div class="flex-1 ms-4 ms-md-5 pt-1">
													<div class="username"> {{ $transaction['payment_method'] }} </div>
													<div class="status"> {{ $transaction['transaction_id'] }} </div>
												</div>
												<div class="float-end pt-1">
													<h6 class="text-{{ $transaction['color'] }} fw-bold"> {{ $transaction['amount'] }} </h6>
												</div>
											</div>
											@if(!$loop->last)
											<div class="separator-dashed"></div>
											@endif
											@endforeach
										</div>
									</div>
									<div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
										<div class="card-header">
											<div class="row row-nav-line">
												<div class="card-body">
													@foreach($user_hotel_reports as $report)
														<div class="row">
															<div class="col-6">
																<h5 class="form-label"> {{ $report['display_text'] }} </h5>
															</div>
															<div class="col-6">
																<h5 class="text-black"> {{ $report['value'] }} </h5>
															</div>
														</div>
														@if(!$loop->last)
														<div class="separator-dashed"></div>
														@endif
													@endforeach
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card card-profile">
							<div class="card-header" style="background-image: url('{{ asset('images/default_cover.jpg')}}')">
								<div class="profile-picture">
									<div class="avatar avatar-xl">
										<img src="{{ $result->profile_picture_src }}" alt="{{ $result->first_name }}" class="avatar-img rounded-circle">
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="user-profile text-center">
									<div class="name"> {{ $result->first_name.' '.$result->last_name }}</div>
									<div class="fw-bold"> @lang('messages.joined_in',['replace_key_1' => $result->created_at->year]) </div>
									@if($result->user_verification->canShow())
									<div class="social-media">
										<ul class="list-unstyled mt-2" title="@lang('messages.verified_info')">
											@foreach(VERIFICATION_METHODS as $method)
											@if($result->user_verification->$method == 1)
											<li class="desc">
												<div>
													<i class="fas fa-check-circle me-2 text-primary" aria-hidden="true"></i>
													@lang('messages.'.$method)
												</div>
											</li>
											@endif
											@endforeach
										</ul>
									</div>
									@endif
									<div class="view-profile">
										<a href="{{ route('view_profile',['id' => $result->id]) }}" target="_blank" class="btn btn-secondary btn-block"> @lang('messages.view_profile') </a>
									</div>
								</div>
							</div>
							<div class="card-footer">
								<div class="row user-stats text-center">
									<div class="col">
										<div class="number fw-bold text-black"> {{ $total_bookings }} </div>
										<div class="title"> @lang('admin_messages.bookings') </div>
									</div>
									<div class="col">
										<div class="number fw-bold text-black"> {{ $total_hotels }} </div>
										<div class="title"> @lang('admin_messages.hotels') </div>
									</div>
									<div class="col">
										<div class="number fw-bold text-black"> {{ $total_reservations }} </div>
										<div class="title"> @lang('admin_messages.reservations') </div>
									</div>
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