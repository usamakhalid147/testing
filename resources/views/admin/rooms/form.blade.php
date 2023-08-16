@include('admin.rooms.room_steps_template')
<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }}
		</div>
	</div>
	<div class="card-body">
		<div class="container">
			<ul class="row g-3 d-none d-md-flex list-unstyled mb-3">
				<li class="w-auto" v-for="step in step_data">		
					<a href="javascript:;" class="btn btn-default shadow-card" :class="{'btn-primary' : step.step == current_step['step']}" v-on:click="goToStep(step)">
						@{{ step.name }}
						<i class="fas fa-exclamation-triangle ms-2" :class="{'text-danger' : step.step != current_step['step']}" v-show="!step.completed" data-bs-toggle="tooltip" title="@lang('messages.this_step_has_some_required_fields')"></i>
					</a>
				</li>
			</ul>
			<div class="line-divided border"></div>
			<div class="card-body" :class="{'loading': isLoading}">
				@foreach($step_data as $step)
					<div class="form-panel" id="{{ $step['step'] }}" v-show="current_step['step'] == '{{ $step['step'] }}'">
						@yield($step['step'])
					</div>
				@endforeach
			</div>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.rooms',['id' => $room->hotel_id])}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="button" class="btn btn-success float-end" v-on:click="saveStep()" :disabled="isLoading" v-show="current_tab != 'calendar'"> 
		 @lang('admin_messages.submit')</button>
	</div>
</div>