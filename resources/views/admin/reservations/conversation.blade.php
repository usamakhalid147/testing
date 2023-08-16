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
					<a href="{{ route('admin.reservations') }}">@lang("admin_messages.reservations")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.booking_conversation")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<div class="card-title"> @lang('admin_messages.booking_details') </div>
					</div>
					<div class="card-body">
						<div class="row px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.hotel_name') </label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->hotel->name }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.checkin')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->checkin }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.checkout')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->checkout }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.number_of_adults')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->adults }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.number_of_children')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->children }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.total_rooms')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->total_rooms }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.guest_name')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->user->full_name }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.host_name')</label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->host_user->full_name }} </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label"> @lang('messages.cancellation_policy') </label>
							<div class="col-sm-6">
								<p class="form-text"> @lang('messages.'.$result->cancellation_policy) </p>
							</div>
						</div>
						<div class="row my-4 px-4">
							<label class="col-sm-6 form-label">@lang('admin_messages.status') </label>
							<div class="col-sm-6">
								<p class="form-text"> {{ $result->status }} </p>
							</div>
						</div>
						<table class="table border table-striped">
							<thead>
								<tr>
									<th>@lang('admin_messages.room_name')</th>
									<th>@lang('admin_messages.adults')</th>
									<th>@lang('admin_messages.children')</th>
									<th>@lang('admin_messages.price')</th>
									<th>@lang('admin_messages.status')</th>
								</tr>
							</thead>
							<tbody>
								@foreach($result->room_reservations as $room)
								<tr>
									<td>{{ $room->hotel_room->name }}</td>
									<td>{{ $room->adults }}</td>
									<td>{{ $room->children }}</td>
									<td>{{ $room->currency_symbol.$room->total_price }}</td>
									<td>{{ $room->status }}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="card-action d-flex">
						<a href="{{ route('admin.reservations')}}" class="btn btn-info text-white"> @lang('admin_messages.back') </a>
						<a href="{{ route('admin.reservations.show',['id' => $result->id]) }}" class="btn btn-success ms-auto"> @lang('admin_messages.view_details') </a>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body p-0">
						<div class="conversations">
							<div class="conversations-body">
								<div class="conversations-content bg-white">
									@foreach($messages as $message)
									<div class="message-content-wrapper">
										<div class="message d-flex {{ ($message['user_from'] !=  $host_id) ? 'message-in' : 'message-out' }}">
											@if($message['user_from'] !=  $host_id)
											<div class="avatar avatar-sm ms-auto">
												<img src="{{ $message['profile_picture_src'] }}" class="avatar-img rounded-circle border border-white">
											</div>
											@endif
											<div class="message-body">
												<div class="message-content">
													<div class="name"> {{ $message['user_name'] }} </div>
													<div class="content">
														{{ $message['message'] }}
													</div>
												</div>
												<div class="date d-none"> {{ $message['sent_at'] }} </div>
											</div>
											@if($message['user_from'] == $host_id)
												<div class="avatar avatar-sm me-auto">
													<img src="{{ $message['profile_picture_src'] }}" class="avatar-img rounded-circle border border-white">
												</div>
											@endif
										</div>
									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection