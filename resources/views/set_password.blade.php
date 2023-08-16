@extends('layouts.app')
@section('content')
<main id="site-content" role="main" class="main-container">
	<div class="container">
		<div class="wrapper">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<div class="form">
							<h3 class="text-center"> @lang('messages.set_new_password') </h3>
							{!! Form::open(['url' => resolveRoute('set_password'), 'class' => '','id'=>'set_password_form']) !!}
							{!! Form::hidden('email',$email) !!}
							{!! Form::hidden('reset_token',$reset_token) !!}
							<div class="form-group">
								<label for="new_password" class="form-label"> @lang('messages.new_password') </label>
								<input type="password" name="password" class="form-control">
							</div>
							<div class="form-group">
								<label for="confirm_password" class="form-label"> @lang('messages.confirm_password') </label>
								<input type="password" name="password_confirmation" class="form-control">
							</div>
							<span class="text-danger"> {{ $errors->first('password') }} </span>
							<div class="form-group mt-4">
								<button type="submit" class="btn btn-primary d-flex w-100 justify-content-center">
								@lang('messages.update_password')
								</button>
							</div>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection