@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
	<div class="panel-header bg-primary-gradient">
		<div class="page-inner py-5">
			<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
				<div>
					<h2 class="text-white pb-2 fw-bold"> {{ $site_name }} </h2>
				</div>
				<div class="ms-auto py-md-0">
					<a href="{{ route('host.reports') }}" class="btn btn-primary btn-round"> @lang('admin_messages.reports') </a>
				</div>
			</div>
		</div>
	</div>
	<div class="page-inner mt--5">
		<div class="row">

			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-info bubble-shadow-small">
									<i class="fas fa-money-bill-wave"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">@lang('admin_messages.total_earning')</p>
									<h4 class="card-title">{{ session('currency_symbol') }}{{ $total_earnings }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-success bubble-shadow-small">
									<i class="fas fa-calendar-check"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">@lang('admin_messages.today_reservations')</p>
									<h4 class="card-title">{{ $today_reservation }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body ">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-primary bubble-shadow-small">
									<i class="fas fa-list-alt"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">@lang('admin_messages.total_reservations')</p>
									<h4 class="card-title">{{ $reservations }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-success bubble-shadow-small">
									<i class="fas fa-hotel"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">@lang('admin_messages.total_hotels')</p>
									<h4 class="card-title">{{ $hotels }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mt-2">
			<div class="card">
				<div class="card-header">
					<div class="card-head-row">
						<div class="card-title info">
							@lang('admin_messages.earning_statistics')
						</div>
						<div class="card-tools">
							<a href="javascript:void(0);" v-on:click="updateBarChartData('decrement')">
								<i class="fas fa-chevron-left"></i>
							</a>
							<span class="mx-2"> @{{ bar_chart.month_names[bar_chart.month_index - 1] }} @{{ bar_chart.current_year }} </span>
							<a href="javascript:void(0);" v-on:click="updateBarChartData('increment')">
								<i class="fas fa-chevron-right"></i>
							</a>									
						</div>
					</div>
				</div>
				<div class="card-body">
					<div id="chart-container">
						<div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
						<canvas height="375" id="BarChart"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<div class="card" :class="{'loading' : isLoading }">
					<div class="card-header">
						<div class="card-head-row">
							<div class="card-title info">
								@lang('admin_messages.transaction_breakdown')
							</div>
							<div class="card-tools">
								<a href="javascript:void(0);" v-on:click="updateChartData('decrement')">
									<i class="fas fa-chevron-left"></i>
								</a>
								<span class="mx-2"> @{{ currentYear }} </span>
								<a href="javascript:void(0);" v-on:click="updateChartData('increment')">
									<i class="fas fa-chevron-right"></i>
								</a>									
							</div>
						</div>
					</div>
					<div class="card-body">
						<div id="chart-container">
							<div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
							<canvas height="375" id="LineChart"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="card full-height">
					<div class="card-header">
						@lang('admin_messages.today_reservations')
					</div>
					<div class="card-body">
						@foreach($today_reservations as $booking)
						<div class="d-flex">
							<div class="avatar">
								<a href="{{ route('view_profile',['id' => $booking['user_id']]) }}">
									<img src="{{ $booking['profile_picture'] }}" alt="{{ $booking['user_name'] }}" class="avatar-img rounded-circle">
								</a>
							</div>
							<div class="flex-1 ms-3 pt-1">
								<a href="{{ route('host.reservations.show',['id' => $booking['id']]) }}"> #{{ $booking['id'] }} </a>
								<span class="ps-3"> {{ $booking['hotel_name'] }} </span>
								@foreach($booking['sub_rooms'] as $sub_room)
									<p class="text-muted m-0"> {{ $sub_room['room_name'] }} x {{ $sub_room['guests'] }} x {{ $sub_room['total_rooms'] }} </p>
								@endforeach
							</div>
							<div class="float-end pt-1">
								<h6 class="text-success fw-bold"> {{ $booking['total'] }} </h6>
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
		<div class="row">
			<div class="col-md-5">
				<div class="card full-height" :class="{'loading' : isLoading }">
					<div class="card-header">
						<div class="card-head-row">
							<div class="card-title info"> @lang('admin_messages.recent_reservations') </div>
						</div>
					</div>
					<div class="card-body">
						@foreach($recent_reservations as $booking)
						<div class="d-flex">
							<div class="avatar">
								<a href="{{ route('view_profile',['id' => $booking['user_id']]) }}">
									<img src="{{ $booking['profile_picture'] }}" alt="{{ $booking['user_name'] }}" class="avatar-img rounded-circle">
								</a>
							</div>
							<div class="flex-1 ms-3 pt-1">
								<h6 class="text-uppercase fw-bold mb-1">
									<a href="{{ route('host.reservations.show',['id' => $booking['id']]) }}"> #{{ $booking['id'] }} </a>
									<span class="text-warning ps-3"> {{ $booking['status'] }} </span>
								</h6>
								<a href="{{ resolveRoute('hotel_details',['id' => $booking['hotel_id']]) }}">
									<span class="text-muted"> {{ $booking['hotel_name'] }} </span>
								</a>
							</div>
							<div class="float-end pt-1">
								<h6 class="text-success fw-bold"> {{ $booking['total'] }} </h6>
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
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		'minYear' => global_settings('starting_year'),
		'maxYear' => date('Y'),
		'currentYear' => date('Y'),
		'dashboard_data' => $dashboard_data,
		'bar_chart' => $bar_chart,
	]) !!};
</script>
@endpush