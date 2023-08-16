<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="input_name"> @lang('admin_messages.name') <em class="text-danger"> * </em> </label>
			{!! Form::text('name', old('name',$result->name), ['class' => 'form-control', 'id' => 'input_name']) !!}
			<span class="text-danger">{{ $errors->first('name') }}</span>
		</div>
		<div class="form-group">
			<label for="input_description"> @lang('admin_messages.role_title') <em class="text-danger"> * </em> </label>
			{!! Form::text('description', old('description',$result->description), ['class' => 'form-control', 'id' => 'input_description']) !!}
			<span class="text-danger">{{ $errors->first('description') }}</span>
		</div>
		@if(count($permissions))
		<div class="form-group">
			<label for="permission">@lang('admin_messages.permission') <em class="text-danger"> * </em> </label>
			<div class="row ms-2">
				@foreach($permissions as $key => $values)
				<div class="col-3">
					<div class="form-check form-role">
						@php
							$permission = $all_permissions->where('name','view-'.$key)->first();
						@endphp
						@if($permission)
						<input type="checkbox" name="permission[]" class="form-check-input" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" :checked="{{ in_array($permission->id,$old_permissions) ? 'true':'false' }}">
						<label for="permission_{{ $permission->id }}" class="form-check-label"> {{ $permission->role_type }} </label>
						@else
						-
						@endif
					</div>
				</div>
				<div class="col-3">
					<div class="form-check form-role">
						@php
							$permission = $all_permissions->where('name','create-'.$key)->first();
						@endphp
						@if($permission)
						<input type="checkbox" name="permission[]" class="form-check-input" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" :checked="{{ in_array($permission->id,$old_permissions) ? 'true':'false' }}">
						<label for="permission_{{ $permission->id }}" class="form-check-label"> {{ $permission->role_type }} </label>
						@else
						-
						@endif
					</div>
				</div>
				<div class="col-3">
					<div class="form-check form-role">
						@php
							$permission = $all_permissions->where('name','update-'.$key)->first();
						@endphp
						@if($permission)
						<input type="checkbox" name="permission[]" class="form-check-input" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" :checked="{{ in_array($permission->id,$old_permissions) ? 'true':'false' }}">
						<label for="permission_{{ $permission->id }}" class="form-check-label"> {{ $permission->role_type }} </label>
						@else
						-
						@endif
					</div>
				</div>
				<div class="col-3">
					<div class="form-check form-role">
						@php
							$permission = $all_permissions->where('name','delete-'.$key)->first();
						@endphp
						@if($permission)
						<input type="checkbox" name="permission[]" class="form-check-input" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" :checked="{{ in_array($permission->id,$old_permissions) ? 'true':'false' }}">
						<label for="permission_{{ $permission->id }}" class="form-check-label"> {{ $permission->role_type }} </label>
						@else
						-
						@endif
					</div>
				</div>
				@endforeach
			</div>
			<span class="text-danger"> {{ $errors->first('permission') }} </span>
		</div>
		@endif
	</div>
	<div class="card-action">
		<a href="{{ route('admin.roles')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>