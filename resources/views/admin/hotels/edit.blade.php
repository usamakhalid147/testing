@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.manage_hotels") </h4>
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
					<a href="{{ route('admin.hotels') }}">@lang("admin_messages.hotels")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.edit") : {{$hotel->name}} </a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => '#','id'=>'hotel_form','files' => true, 'method' => 'PUT']) !!}
				{!! Form::hidden('hotel_id',$hotel->id) !!}
				{!! Form::hidden('step', null, [':value' => 'current_tab']) !!}
				@include('admin.hotels.form')
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	routeList['update_hotel'] = {!! json_encode(route('admin.hotels.update',['id' => $hotel->id])); !!};
	routeList['update_photo_order'] = {!! json_encode(route('admin.hotels.update_photo_order',['id' => $hotel->id])); !!};

	window.vueInitData = {!! json_encode([
			'hotel' => $hotel,
			'hotel_id' => $hotel->id,
			'user_id' => $hotel->user_id,
			'current_step' => $current_step,
			"step_data" => $step_data,
			"translations"	=> formatTranslationData($translations) ?? [],
			'countries' => $countries,
			'cities' => $city_list,
		]);
	!!}
</script>
@endpush