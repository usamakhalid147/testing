@extends('layouts.hostLayout.app')
@section("content")
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
				<li class="nav-item">
					<a href="#">@lang("admin_messages.agents")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<h4 class="card-title"> @lang("admin_messages.agents") </h4>
							@checkHostPermission('create-host_users')
							<a href="{{ route('host.users.create') }}" class="btn btn-primary btn-round ms-auto">
								@lang('admin_messages.add_agent')
							</a>
							@endcheckHostPermission
						</div>
					</div>
					<div class="card-body">
						<div class="">
							{!! $dataTable->table() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
	<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/datatables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('admin_assets/js/plugin/datatables/buttons.server-side.js') }}"></script>
	{!! $dataTable->scripts() !!}
@endpush