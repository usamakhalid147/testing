@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> {{ $sub_title }} </h4>
			<ul class="breadcrumbs">
				<li class="nav-home">
					<a href="{{ route('host.dashboard') }}">
						<i class="flaticon-home"></i>
					</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-itfem">
					<a href="{{ route('host.reservations') }}">@lang("admin_messages.reservations")</a>
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
			<div class="col-md-7">
				{!! Form::open(['url' => '#', 'class' => 'form-horizontal']) !!}
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
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
								@foreach($cancellation_policy as $cancellation)
								@lang('messages.room_name'):<span class="fw-bold"> {{ $cancellation['room_name'] }}</span>
								<table class="table table-hover">
									<tr>
										<td>@lang('messages.days')</td>
										<td>@lang('messages.percentage')</td>
									</tr>
									<tbody>
										@foreach($cancellation['policies'] as $policy)
										<tr>
											<td>{{ $policy['days'] }}</td>
											<td>{{ $policy['percentage'] }}
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
								@endforeach
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
					<div class="card-action">
						<a href="{{ route('host.reservations')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
			<div class="col-md-5">
				<div class="row">
					<div class="card">
						<div class="card-header justify-content-between d-flex">
							<div class="card-title">@lang('admin_messages.price_details')</div>
							@if($result->checkin >= date("Y-m-d") && $result->status == 'Accepted' && $cancellation_policy < $date_diff)
							<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal"> @lang('messages.cancel') </button>
							@endif
						</div>
						<div class="card-body">
							@foreach($pricing_details as $index => $pricing_detail)
							<div class="row mb-2 {{ $pricing_detail['class'] }}">
								<div class="col-sm-8 {{ $pricing_detail['key_style'] }}" style="{{ $pricing_detail['key_style'] }}">
									<div class="count-detail d-flex">
										<span>{{ $pricing_detail['key'] }}</span>
										@if($pricing_detail['count'])
										<span class="ms-1"> x {{ $pricing_detail['count'] }}</span>
										@endif
										@if($pricing_detail['tooltip'])
										<i class="icon icon-info cursor-pointer d-print-none ms-1" data-bs-toggle="tooltip" title="{{ $pricing_detail['tooltip'] }}" area-hidden="true"></i>
										@endif
									</div>
									<div class="detail-persons">
										{{ $pricing_detail['description'] }}
									</div>
								</div>
								<div class="col-sm-4 text-end" style="{{ $pricing_detail['value_style']}}"> {{ $pricing_detail['value'] }} </div>
							</div>
							@if($pricing_detail['dropdown'])
							<div class="collapse navbar-collapse show">
								@foreach($pricing_detail['dropdown_values'] as $dropdown_value)
								<div class="card-body box-shadow border">
									<div class="d-flex justify-content-between">
										<div class="">
											{{ $dropdown_value['key'] }}
										</div>
										<div class="">
											{{--@if($dropdown_value['prefix'] != '')
											<span>@{{dropdown_value.prefix}}</span>
											@endif--}}
											{{ session('currency_symbol')}} {{ $dropdown_value['value'] }}
										</div>
									</div>
								</div>
								@endforeach
							</div>
							@endif
							@endforeach
						</div>
					</div>
				</div>
				<div class="row">
					<div class="card">
						<div class="card-header">
							<div class="card-title"> @lang('admin_messages.transaction_details') </div>
						</div>
						<div class="card-body">
							<div class="row">
								<label class="col-sm-6 form-label">@lang('admin_messages.status') </label>
								<div class="col-sm-6">
									<p class="form-text"> {{ $result->status }} </p>
								</div>
							</div>
							@if($host_payout && $host_payout->amount > 0)
							<div class="row">
								<label class="col-sm-6 form-label">
									@lang('admin_messages.payout_amount')
									@if($host_payout->transaction_id != '')
									<p class="detail-persons">({{$host_payout->transaction_id}})</p>
									@endif
								</label>
								<div class="col-sm-6">
									<p class="form-text"> {{ $host_payout->currency_symbol.$host_payout->amount }} <span class="badge bg-primary ms-2"> {{ $host_payout->status }} </span> <span><a href="{{ route('host.reservations.report',['id' => $result->id]) }}"><i class="fa fa-share"></i></a></span> </p>
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="cancelModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">@lang('messages.cancel_your_booking')</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="form">
					<p>@lang('messages.why_are_you_cancel')</p>
					<p>@lang('messages.info_not_shared_with_guest')</p>
					{!! Form::open(['url' => route('host.reservations.cancel',['id' => $result->id]),'id' => "classModal"]) !!}
						{!! Form::hidden('reservation_id',$result->id) !!}
						<div class="form-group">
							<label for="selected_rooms" class="form-label"> @lang('messages.rooms') </label>
							<select name="room_reservations" class="form-select">
								<option value="all" selected> @lang('messages.all') </option>
								@foreach($result->room_reservations->where('status','Accepted') as $room_reservation)
								<option value="{{ $room_reservation->id }}"> {{ $room_reservation->hotel_room->name }} </option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<select name="cancel_reason" class="form-select cancel-reason">
								<option value="" selected >@lang('messages.select')</option>
								@foreach(HOST_CANCEL_REASONS as $reason)
								<option value="{{$reason}}">@lang('messages.'.$reason)</option>
								@endforeach
							</select>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-primary float-end cancel-btn" >Cancel Booking</button>
						</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		var cancelModalEl = document.getElementById('cancelModal');
		if(cancelModalEl !== null) {
			cancelModalEl.addEventListener('shown.bs.modal', function (event) {
				$('.cancel-btn').attr('disabled',true);
			});
		}

		$(document).on('change','.cancel-reason',function() {
			$('.cancel-btn').attr('disabled',true);
			if($(this).val() != '') {
				$('.cancel-btn').removeAttr('disabled');
			}
		});
	});
</script>
@endpush