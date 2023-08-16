@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner" v-cloak>
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.reports") </h4>
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
					<a href="javascript:;"> @lang("admin_messages.reports") </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="card-title"> {{ $sub_title }} </div>
					</div>
					<div class="card-body">
						<div class="card-body" :class="isLoading ? 'loading' : ''">
							{!! Form::open(['url' => route('admin.reports.export'),'id' => 'exportReportForm']) !!}
							{!! Form::hidden('from',null,[':value'=>'report.from']) !!}
							{!! Form::hidden('to',null,[':value'=>'report.to']) !!}
							{!! Form::hidden('category',null,[':value'=>'report.category']) !!}
							{!! Form::close() !!}
							<div class="row mb-5">
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::text('report_from', null, ['class' => 'form-control', 'id' => 'report_from', 'readonly' => 'true','v-model' => 'report.from', 'placeholder' => Lang::get('admin_messages.from')]) !!}
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										{!! Form::text('report_to', null, ['class' => 'form-control', 'id' => 'report_to', 'readonly' => 'true','v-model' => 'report.to', 'placeholder' => Lang::get('admin_messages.to')]) !!}
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<select name="report_category" class="form-select" id="report_category" v-model="report.category">
											<option value=""> @lang('admin_messages.category') </option>
											<option :value="filter.name" v-for="filter in filter_list"> @{{ filter.display_name }} </option>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<button type="button" class="btn btn-sm btn-primary" :disabled="report.category == ''" v-on:click="fetchReports();"> @lang('admin_messages.generate') </button>
										<button type="button" class="btn btn-sm btn-secondary ms-2" :disabled="!showExport" v-on:click="exportReports();"> @lang('admin_messages.export') </button>
									</div>
								</div>
							</div>
							<div class="row" v-if="report.category != ''">
								<div class="col-md-12 text-center">
									<p class="h1" v-show="!isLoading"> @{{ filter_text }} </p>
								</div>
								<div class="col-md-12">
									<div class="table-responsive">
										<table id="reports-table" class="display table table-striped table-hover">
											<thead>
												<tr>
													<th v-for="filter_col in currentFilter.title"> @{{ filter_col }} </th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="report in report_data">
													<td v-for="filter_col in currentFilter.columns"> @{{ report[filter_col] }} </td>
												</tr>
											</tbody>
										</table>
									</div>
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
@push('scripts')
	<script type="text/javascript">
		window.vueInitData = {!! json_encode([
			'filter_list' => $filter_list,
			'report' => [
				'from' => '',
				'to' => '',
				'category' => ''
			],
		]) !!};
	</script>
	<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/datatables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/buttons.server-side.js') }}"></script>
@endpush