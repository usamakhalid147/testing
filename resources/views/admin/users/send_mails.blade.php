@extends('layouts.adminLayout.app')
@section('content')
<div class="content">
	<div class="page-inner">
		<div class="page-header">
			<h4 class="page-title"> @lang("admin_messages.email_to_users") </h4>
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
					<a href="#">@lang("admin_messages.email_to_users")</a>
				</li>
			</ul>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['url' => route('admin.email_to_users'), 'class' => 'form-horizontal']) !!}
					<div class="card">
						<div class="card-header">
							<div class="card-title"> {{ $sub_title }} </div>
						</div>
						<div class="card-body">
							<div class="form-check">
								<label> @lang('admin_messages.mail_to') <em class="text-danger"> * </em> </label><br/>
								<label class="form-radio-label">
									<input class="form-radio-input" type="radio" name="mail_to" v-model="mail_to" value="all">
									<span class="form-radio-sign"> @lang('admin_messages.all_users') </span>
								</label>
								<label class="form-radio-label ms-3">
									<input class="form-radio-input" type="radio" name="mail_to" v-model="mail_to" value="specific">
									<span class="form-radio-sign"> @lang('admin_messages.specific_users') </span>
								</label>
								<span class="text-danger">{{ $errors->first('mail_to') }}</span>
							</div>
							<div class="form-group" :class="{'d-none' : mail_to != 'specific'}">
								<label for="emails"> @lang('admin_messages.emails') <em class="text-danger"> * </em></label>
								{!! Form::select('emails[]', $user_email_list,'',['class' => 'w-100 form-select', 'id' => 'emails','multiple' => 'multiple']) !!}
								<span class="text-danger">{{ $errors->first('emails') }}</span>
							</div>
							<div class="form-group">
								<label for="title"> @lang('admin_messages.subject') <em class="text-danger"> * </em></label>
								{!! Form::text('subject', '', ['class' => 'form-control', 'id' => 'subject']) !!}
								<span class="text-danger">{{ $errors->first('subject') }}</span>
							</div>

							<div class="form-group">
								<label for="content">
									@lang('admin_messages.content') <em class="text-danger"> * </em>
									<p class="my-0 small"> (@lang('admin_messages.salutation_automatically_added')) </p>
								</label>
								<textarea name="content" class="form-control rich-text-editor" id="content"></textarea>
								<span class="text-danger">{{ $errors->first('content') }}</span>
							</div>
						</div>
						<div class="card-action">
							<a href="{{ route('admin.email_to_users')}}" class="btn btn-danger"> @lang('admin_messages.cancel') </a>
							<button type="submit" class="btn btn-primary float-end"> @lang('admin_messages.submit') </button>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	window.vueInitData = {!! json_encode([
		'mail_to' => old('mail_to','all'),
	]) !!}
</script>
<script src="{{ asset('admin_assets/js/plugin/select2/select2.min.js') }}"></script>
@endpush