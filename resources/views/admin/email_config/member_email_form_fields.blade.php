<div class="card-body">
        <input type="text" hidden name="site_value" value="MemberEmailConfig">
        <div class="form-group">
			<label for="driver"> @lang('admin_messages.driver') <em class="text-danger">*</em> </label>
			{!! Form::select('driver', MAIL_DRIVERS, old('driver',credentials('driver','MemberEmailConfig')), ['class' => 'form-select', 'id' => 'driver']) !!}
			<span class="text-danger">{{ $errors->first('driver') }}</span>
		</div>
		<div class="form-group">
			<label for="host"> @lang('admin_messages.host') <em class="text-danger">*</em> </label>
			{!! Form::text('host', old('host',credentials('host','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'host']) !!}
			<span class="text-danger">{{ $errors->first('host') }}</span>
		</div>
		<div class="form-group">
			<label for="port"> @lang('admin_messages.port') <em class="text-danger">*</em> </label>
			{!! Form::text('port', old('port',credentials('port','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'port']) !!}
			<span class="text-danger">{{ $errors->first('port') }}</span>
		</div>
		<div class="form-group">
			<label for="encryption"> @lang('admin_messages.encryption') <em class="text-danger">*</em> </label>
			{!! Form::text('encryption', old('encryption',credentials('encryption','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'encryption']) !!}
			<span class="text-danger">{{ $errors->first('encryption') }}</span>
		</div>
		<div class="form-group">
			<label for="from_name"> @lang('admin_messages.from_name') <em class="text-danger">*</em> </label>
			{!! Form::text('from_name', old('from_name',credentials('from_name','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'from_name']) !!}
			<span class="text-danger">{{ $errors->first('from_name') }}</span>
		</div>
		<div class="form-group">
			<label for="from_address"> @lang('admin_messages.from_address') <em class="text-danger">*</em> </label>
			{!! Form::text('from_address', old('from_address',credentials('from_address','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'from_address']) !!}
			<span class="text-danger">{{ $errors->first('from_address') }}</span>
		</div>
		<div class="form-group">
			<label for="username"> @lang('admin_messages.username') <em class="text-danger">*</em> </label>
			{!! Form::text('username', old('username',credentials('username','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'username']) !!}
			<span class="text-danger">{{ $errors->first('username') }}</span>
		</div>
		<div class="form-group">
			<label for="app_password"> @lang('admin_messages.password') <em class="text-danger">*</em> </label>
			{!! Form::text('app_password', old('app_password',credentials('password','MemberEmailConfig')), ['class' => 'form-control', 'id' => 'app_password']) !!}
			<span class="text-danger">{{ $errors->first('app_password') }}</span>
		</div>
	</div>	
	<div class="card-action">
		<button type="reset" class="btn btn-danger"> @lang('admin_messages.reset') </button>
		<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
	</div>