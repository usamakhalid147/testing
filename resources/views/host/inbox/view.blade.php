@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="d-flex justify-content-between">
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
			<div class="col-md-3">
				{{--
				<div class="dropdown">
				  	<button class="custom-dropdown w-100 custom-wrap btn-white form-control" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    {{$filters_array[$type]}}
				  	</button>
					<div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
						@foreach($filters_array as $name => $filter)
						<a class="dropdown-item custom-wrap {{ $name == $type ? 'disabled' : '' }}" href="{{ route('host.'.$active_menu,['type' => $name]) }}" >{{ $filter }}</a>
						@endforeach
					</div>
				</div>
				--}}
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<h4 class="card-title"> {{ $sub_title }} </h4>
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