@extends('layouts.hostLayout.app')
@section("content")
<div class="content">
	<div class="page-inner">
		<div class="d-flex justify-content-between">
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
						@if($id)
							<a href="{{ route('host.hotels.edit',['id' => $id])}}">@lang("admin_messages.hotel") : {{ $filter }}</a>
						@else
							<a href="{{ route('host.hotels')}}">@lang("admin_messages.hotels")</a>
						@endif
					</li>
					<li class="separator">
						<i class="flaticon-right-arrow"></i>
					</li>
					<li class="nav-item">
						<a href="#">@lang("admin_messages.rooms")</a>
					</li>
				</ul>
			</div>
			<div class="col-md-3">
				<div class="dropdown">
				  	<button class="custom-dropdown w-100 custom-wrap btn-white form-control" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    {{$filter}}
				  	</button>
					<div class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
						<a class="dropdown-item custom-wrap" href="{{ $id == 0 ? 'javascript:void(0);' : route('host.rooms') }}">@lang('admin_messages.all_rooms')</a>
						@foreach($hotels as $hotel_id => $name)
						<a class="dropdown-item custom-wrap" href="{{ $id == $hotel_id ? 'javascript:void(0);' : route('host.rooms',['id' => $hotel_id]) }}" >{{ $name }}</a>
						@endforeach
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<h4 class="card-title">
								@if($id)
									{{$filter}} 
								@else
									@lang('admin_messages.all_rooms')
								@endif
							</h4>
							@if($id)
								@checkHostPermission('create-host_rooms')
								<a href="{{ route('host.rooms.create',['id' => $id]) }}" class="btn btn-primary btn-round ms-auto">
									@lang('admin_messages.add_room')
								</a>
								@endcheckHostPermission
							@endif
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