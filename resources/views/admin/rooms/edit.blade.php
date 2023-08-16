@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.manage_rooms") </h4>
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
					<a href="{{ route('admin.hotels.edit',['id' => $room->hotel->id]) }}">@lang("admin_messages.hotel") : {{ $room->hotel->name }}</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="{{ route('admin.rooms',['id' => $room->hotel->id]) }}">@lang("admin_messages.rooms")</a>
				</li>
				<li class="separator">
					<i class="flaticon-right-arrow"></i>
				</li>
				<li class="nav-item">
					<a href="#">@lang("admin_messages.edit") : {{ $room->name }}</a>
				</li>
			</ul>
		</div>
		<div class="row">
			{!! Form::open(['url' => '#', 'class' => 'form-horizontal','id'=>'room_form','method' => "PUT",'files' => true]) !!}
			{!! Form::hidden('hotel_id',$room->hotel->id) !!}
			{!! Form::hidden('room_id',$room->id) !!}
			{!! Form::hidden('step',null,[':value' => 'current_step.step']) !!}
				@include('admin.rooms.form')
			{!! Form::close() !!}
		</div>

		<!-- Calendar Model Start -->
		<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModal" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header justify-content-center border-bottom">
						<h3> @lang('messages.update_availability') </h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body" :class="{'loading' :isLoading }">
						<div class="calendar-availability d-flex">
							<label class="availability-option" :class="(calendar_data.status == 'available') ? 'availability-selected' : '' ">
								<span> @lang('messages.available') </span>
								<input type="radio" name="calendar_status" class="availability-input" value="available" v-model="calendar_data.status" :checked="calendar_data.status == 'available'">
							</label>
							<label class="availability-option" :class="(calendar_data.status == 'not_available') ? 'availability-selected' : ''">
								<span> @lang('messages.not_available') </span>
								<input type="radio" name="calendar_status" class="availability-input" value="not_available" v-model="calendar_data.status" :checked="calendar_data.status == 'not_available'">
							</label>
							<button type="button" class="btn btn-danger mx-2" v-show="calendar_data.calendar_id != ''" v-on:click="updateCalendarEvent('delete')">
								<i class="fas fa-trash-alt"></i>
							</button>
						</div>
						<div class="form-floating mb-3">
							<input type="text" name="start_date" class="form-control mt-2" id="start_date" v-model="calendar_data.formatted_start_date" placeholder="@lang('messages.start_date')" readonly>
							<label for="start_date" class="form-label"> @lang('messages.start_date') </label>
						</div>
						<div class="form-floating mb-3">
							<input type="text" name="end_date" class="form-control mt-2" id="end_date" v-model="calendar_data.formatted_end_date" placeholder="@lang('messages.end_date')" readonly>
							<label for="end_date" class="form-label"> @lang('messages.end_date') </label>
						</div>
						<div class="input-group floating-input-group">
							<span class="input-group-text"> @{{ room.hotel_room_price.currency_symbol }} </span>
							<div class="form-floating flex-grow-1">
								<input type="text" name="price" class="form-control" id="price" v-model="calendar_data.price" placeholder="@lang('messages.price')">
								<label for="price" class="form-label"> @lang('messages.price') </label>
							</div>
							<p class="text-danger"> @{{ (error_messages.price) ? error_messages.price[0] : '' }}</p>
						</div>
						<div class="form-floating">
							<input type="text" name="notes" class="form-control mt-2" id="notes" v-model="calendar_data.notes" placeholder="@lang('messages.notes')">
							<label for="notes" class="form-label"> @lang('messages.notes') </label>
						</div>
						<span class="text-danger"> @{{ calendar_data.error_message }} </span>
						<div class="mt-5 pt-md-3 d-flex align-items-center">
							<button class="btn btn-default me-3" data-bs-dismiss="modal"> @lang('messages.cancel') </button>
							<button type="button" class="btn btn-primary ms-auto px-4 px-md-5" v-on:click="updateCalendarEvent()">
								@lang('messages.save')
							</button>
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
	routeList['update_room'] = {!! json_encode(route('admin.rooms.update',['id' => $room->id])); !!};
	routeList["update_room_photo_order"] = {!! json_encode(route("admin.rooms.update_room_photo_order")) !!},

	window.vueInitData = {!! json_encode([
		"room" => $room,
		"room_id" => $room->id,
		"hotel_id" => $room->hotel->id,
		"user_id" => $room->hotel->user_id,
		"step_data" => $step_data,
		"current_step" => $current_step ?? [],
		'meal_plan_options' => $meal_plan_options ?? [],
		"bed_types" => $bed_types ?? [],
		"hotel_room_promotions" => [
			'early_bird' => $room->hotel_room_promotions->Where('type','early_bird')->values(),
			'min_max' => $room->hotel_room_promotions->Where('type','min_max')->values(),
			'day_before_checkin' => $room->hotel_room_promotions->Where('type','day_before_checkin')->values(),
		],
		"translations"	=> formatTranslationData($translations) ?? [],
		"calendar_text" => [
			'available' => Lang::get('admin_messages.available'),
			'sold' => Lang::get('admin_messages.sold'),
			'price' => Lang::get('admin_messages.price'),
			'not_available' => Lang::get('messages.not_available'),
			'edit' => Lang::get('admin_messages.edit'),
			'close' => Lang::get('admin_messages.close'),
		],
		]) !!};
	</script>
@endpush