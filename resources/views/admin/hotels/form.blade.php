@include('admin.hotels.hotel_steps_template')
<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }}
			@checkPermission('view-rooms')
			<a href="{{ route('admin.rooms',['id' => $hotel->id]) }}" class="btn btn-primary btn-round float-end">
				@lang('admin_messages.manage_rooms')
			</a>
			@endcheckPermission
		</div>
	</div>
	<div class="card-body">		
		<div class="container mt-4">
			<ul class="row g-3 d-none d-md-flex list-unstyled mb-3">
				<li class="w-auto" v-for="data in step_data">
					<a href="javascript:;" class="btn btn-default shadow-card" :class="{'btn-primary' : data.step == current_tab}" v-on:click="goToStep(data)">
						@{{ data.name }}
						<i class="fas fa-exclamation-triangle ms-2" :class="{'text-danger' : data.step != current_tab}" v-show="!data.completed" data-bs-toggle="tooltip" title="@lang('messages.this_step_has_some_required_fields')"></i>
					</a>
				</li>
			</ul>
			<div class="line-divided border"></div>
				<div class="card-body" :class="{'loading': isLoading}">
					@foreach($step_data as $step)
					<div class="form-panel" id="{{ $step['step'] }}" v-show="current_tab == '{{ $step['step'] }}'">
						@yield($step['step'])
					</div>
					@endforeach
				</div>
			</div>
			<div class="card-footer mt-4 pt-4">
				<a href="{{ route('admin.hotels')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
				<button type="button" class="btn btn-success float-end" v-show="current_tab != 'calendar'" v-on:click="saveStep()" :disabled="isLoading"> 
				 @lang('admin_messages.submit')</button>
			</div>
		</div>
	</div>
</div>