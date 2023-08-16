@extends('layouts.hostLayout.app')
@section("content")
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
					<a href="#"> {{ $sub_title }} </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<h4 class="card-title"> {{ $sub_title }} </h4>
							<a href="{{ route('host.payout_methods.create') }}" class="btn btn-primary btn-round ms-auto">
								@lang('admin_messages.add_payout_method')
							</a>
						</div>
					</div>
					<div class="card-body">
						<div class="">
							<table class="table">
								<tr>
									<th>@lang('admin_messages.method_type')</th>
									<th>@lang('admin_messages.is_default')</th>
									<th>@lang('admin_messages.currency_code')</th>
									<th>@lang('admin_messages.account_info')</th>
									<th>@lang('admin_messages.action')</th>
								</tr>
								@foreach($payout_methods as $payout_method)
								<tr>
									<td>
										<img src="{{ asset('/images/'.$payout_method->method_type.'.svg') }}" style="width: 70px; height: 50px;">
									</td>
									<td>
										@if($payout_method->is_default > 0)
										<a class="btn btn-success disabled btn-sm">@lang('admin_messages.yes')</a>
										@else
										<a href="{{ route('host.payout_methods.update',['id' => $payout_method->id]) }}" class="btn btn-danger btn-sm">@lang('admin_messages.no')</a>
										@endif
									</td>
									<td>{{ $payout_method->currency_code }}</td>
									<td>{{ $payout_method->payout_id }}</td>
									<td>
										<a href="{{ route('host.payout_methods.delete',['id' => $payout_method->id]) }}" class="h2 info"><span class="fas fa-trash-alt"></span></a>
									</td>
								</tr>
								@endforeach
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection