<div class="card">
	<div class="card-header">
		<div class="card-title"> {{ $sub_title }} </div>
	</div>
	<div class="card-body">
		<div class="form-group">
			<label for="username"> @lang('admin_messages.agent') @lang('admin_messages.name') <em class="text-danger">* </em> </label>
			{!! Form::text('username', $result->username ?? '', ['class' => 'form-control', 'id' => 'username']) !!}
			<span class="text-danger">{{ $errors->first('username') }}</span>
		</div>
		<div class="form-group">
			<label for="email"> @lang('admin_messages.agent') @lang('admin_messages.email') <em class="text-danger">* </em> </label>
			{!! Form::email('email', $result->email ?? '', ['class' => 'form-control', 'id' => 'email']) !!}
			<span class="text-danger">{{ $errors->first('email') }}</span>
		</div>
		<div class="form-group">
			<label for="password"> @lang('admin_messages.password') <em class="text-danger">* </em> </label>
			{!! Form::text('password', '', ['class' => 'form-control', 'id' => 'password' ]) !!}
			<span class="text-danger">{{ $errors->first('password') }}</span>
		</div>
		<div class="form-group">
			<label for="role"> @lang('admin_messages.role_title') <em class="text-danger">* </em> </label>
			{!! Form::select('role', $roles, $role_id ?? '', ['class' => 'form-select', 'id' => 'role', 'placeholder' => Lang::get('admin_messages.select')]) !!}
			<span class="text-danger">{{ $errors->first('role') }}</span>
		</div>
		<div class="form-group">
			<label for="user_currency"> @lang('admin_messages.currency_code') <em class="text-danger">* </em> </label>
		    {!! Form::select('user_currency',$currency_list, $result->getRawOriginal('user_currency'), ['id' => 'user_currency','class' => 'form-select', 'placeholder' => Lang::get('admin_messages.select')]) !!}
		</div>
		<input type="hidden" name="user_language" value="USD">
		{{--<div class="form-group">
			<label for="user_language"> @lang('admin_messages.language_code') <em class="text-danger">* </em> </label>
		    {!! Form::select('user_language',$language_list, $result->getRawOriginal('user_language'), ['id' => 'user_language','class' => 'form-select', 'placeholder' => Lang::get('admin_messages.select')]) !!}
			<span class="text-danger">{{ $errors->first('user_language') }}</span>
		</div>--}}
		<div class="form-group">
			<label for="status"> @lang('admin_messages.status') <em class="text-danger">* </em> </label>
			{!! Form::select('status', $status_array, $result->status ?? '', ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get('admin_messages.select')]) !!}
			<span class="text-danger">{{ $errors->first('status') }}</span>
		</div>
		<div class="form-group">
			<label for="primary"> @lang('admin_messages.primary') <em class="text-danger">* </em> </label>
			{!! Form::select('primary', $yes_no_array, $result->primary ?? '', ['class' => 'form-select', 'id' => 'primary', 'placeholder' => Lang::get('admin_messages.select')]) !!}
			<span class="text-danger">{{ $errors->first('primary') }}</span>
		</div>
	</div>
	<div class="card-action">
		<a href="{{ route('admin.admin_users')}}" class="btn btn-danger"> @lang('admin_messages.back') </a>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>
</div>