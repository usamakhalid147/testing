@extends('layouts.adminLayout.app')
@section('content')
<div class="content" v-cloak>
	<div class="panel-header bg-primary-gradient">
		<div class="page-inner py-5">
			<div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
				<div>
					<h2 class="text-white pb-2 fw-bold"> {{ $site_name }} </h2>
					<h5 class="text-white op-7 mb-2"> @lang('admin_messages.quick_summary_of_system') </h5>
				</div>
				@checkHostPermission('*-host_reports')
				<div class="ms-md-auto py-2 py-md-0">
					<a href="{{ route('admin.reports') }}" class="btn btn-primary btn-round"> @lang('admin_messages.reports') </a>
				</div>
				@endcheckHostPermission
			</div>
		</div>
	</div>
	<div class="page-inner mt--5" :class="{'loading' : isLoading }">
		<div class="row mt--2">
			<div class="col-md-6">
				<div class="card full-height">
					<div class="card-body">
						<div class="card-title"> @lang('admin_messages.overall_statistics') </div>
						<div class="card-category"> @lang('admin_messages.overall_info_about_statistics') </div>
						<div class="d-flex flex-wrap justify-content-around pb-2 pt-4">
							<div class="px-2 pb-2 pb-md-0 text-center">
								<div id="users"></div>
								<h6 class="fw-bold mt-3 mb-0"> @lang('admin_messages.total_users') </h6>
							</div>
							<div class="px-2 pb-2 pb-md-0 text-center">
								<div id="hotels"></div>
								<h6 class="fw-bold mt-3 mb-0"> @lang('admin_messages.total_hotels') </h6>
							</div>
							<div class="px-2 pb-2 pb-md-0 text-center">
								<div id="reservations"></div>
								<h6 class="fw-bold mt-3 mb-0"> @lang('admin_messages.total_reservations') </h6>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card full-height">
					<div class="card-body">
						<div class="card-title"> @lang('admin_messages.total_income_payout_statistics') </div>
						<div class="row py-3">
							<div class="col-md-4 d-flex flex-column justify-content-around">
								<div>
									<h6 class="fw-bold text-uppercase text-primary op-8">@lang('admin_messages.total_transactions')</h6>
									<h3 class="fw-bold"> <span> @{{ dashboard_data.currency_symbol }} </span> @{{ dashboard_data.total_transactions }} </h3>
								</div>
								<div class="d-none">
									<h6 class="fw-bold text-uppercase text-warning op-8">@lang('admin_messages.paid_out')</h6>
									<h3 class="fw-bold"> <span> @{{ dashboard_data.currency_symbol }} </span> @{{ dashboard_data.paid_out }} </h3>
								</div>
								<div>
									<h6 class="fw-bold text-uppercase text-success op-8">@lang('admin_messages.admin_earnings')</h6>
									<h3 class="fw-bold"> <span> @{{ dashboard_data.currency_symbol }} </span> @{{ dashboard_data.admin_earnings }} </h3>
								</div>
							</div>
							<div class="col-md-8">
								<div id="chart-container">
									<canvas id="totalIncomeChart"></canvas>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<div class="card">
					<div class="card-header">
						<div class="card-head-row">
							<div class="card-title">
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
				<div class="card">
					<div class="card-header">
						<div class="card-head-row card-tools-still-right">
							<h4 class="card-title"> @lang('admin_messages.country_statistics') </h4>
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive table-hover" style="height: 375px;">
							<table class="table">
								<tbody>
									<tr>
										<th> @lang('admin_messages.country') </th>
										<th> @lang('admin_messages.hotels') </th>
									</tr>
									<tr v-for="geo_data in dashboard_data.geo_data">
										<td> @{{ geo_data.country_name }} </td>
										<td class="fw-bold"> @{{ geo_data.hotel_count }} </td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="card full-height">
					<div class="card-body">
						<div class="card-title fw-mediumbold"> @lang('admin_messages.recent_users') </div>
						<div class="separator-dashed"></div>
						<div class="card-list">
							@foreach($recent_users as $user)
							<div class="item-list">
								<div class="avatar">
									<a href="{{ route('view_profile',['id' => $user['id']]) }}">
										<img src="{{ $user['profile_picture'] }}" alt="{{ $user['name'] }}" class="avatar-img rounded-circle">
									</a>
								</div>
								<div class="info-user ms-3">
									<div class="username"> {{ $user['name'] }} </div>
									<div class="status"> {{ $user['email'] }} </div>
								</div>
								<a href="{{ route('admin.users.edit',['id' => $user['id']]) }}" class="text-info h4">
									<i class="fa fa-edit"></i>
								</a>
							</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="card full-height">
					<div class="card-header">
						<div class="card-head-row">
							<div class="card-title"> @lang('admin_messages.recent_transactions') </div>
						</div>
					</div>
					<div class="card-body">
						@foreach($recent_transactions as $transaction)
						<div class="d-flex">
							<div class="avatar">
								<a href="{{ route('view_profile',['id' => $transaction['user_id']]) }}">
									<img src="{{ $transaction['profile_picture'] }}" alt="{{ $transaction['user_name'] }}" class="avatar-img rounded-circle">
								</a>
							</div>
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
								<h6 class="text-success fw-bold"> {{ $transaction['amount'] }} </h6>
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
	]) !!};
</script>
@endpush