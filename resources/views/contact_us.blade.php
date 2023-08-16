@extends('layouts.app')
@section('content')
<main role="main" class="main-container">
	<div class="container">
		<div class="col-md-12 mx-auto">
			<div class="row">
				<div class=" text-center">
					<h2 class="h1-responsive font-weight-bold text-center my-4">
						@lang('messages.contact_us')
					</h2>
				</div>
				<div class="col-md-7 text-center mb-4">
					<p class="h4 fw-bolder mb-4"> {{ global_settings('site_name') }} </p>
					<p class="text-center w-responsive mx-auto mb-5">
						Issued by the Department of Planning and Investment of Danang City on January 20 Year 2020.
						<br />
						Number 5928/20. Business License and Tax Code: 0402024321. 
					</p>
					<div class="row text-center">
						<div class="col-md-4">
							<i class="icon icon-map display-6"></i>
							<p>16 An Nhon 3, An Hai Bac Ward, Son Tra District, Danang City - Vietnam.</p>
						</div>
						<div class="col-md-4">
							<i class="icon icon-telephone display-6"></i>
							<p>{{ global_settings('support_number') }}</p>
						</div>
						<div class="col-md-4">
							<i class="icon icon-email display-6"></i>
							<p>{{ global_settings('support_email') }}</p>
						</div>
						<iframe class="mt-3" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15335.70394515958!2d108.2339442!3d16.0693301!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3142197287286dc1%3A0x6318e3cc50c7e358!2zQ8OUTkcgVFkgVE5ISCBEVSBIw40gVknhu4ZU!5e0!3m2!1sen!2s!4v1682496982294!5m2!1sen!2s" frameborder="0" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
					<!-- <img src="{{ asset('images/contact.svg') }}" class="img-contact img-fluid"> -->
				</div>
				<div class="col-md-5 text-wrap">
					<div class="">
						{!! Form::open(['url' => resolveRoute('contact_us'), 'class' => 'form-horizontal','id'=>'contact-form','method' => "POST"]) !!}
						<div class="">
							<div class="form-group">
								<label class="form-label"> @lang('messages.full_name') </label>
								{!! Form::text('name', '', ['class' =>  'form-control']) !!}
								<span class="text-danger"> {{ $errors->first('name') }} </span> 
							</div>
							<div class="form-group">
								<label class="form-label"> @lang('messages.email') </label>
								{!! Form::text('email', '', ['class' =>  'form-control']) !!}
								<span class="text-danger"> {{ $errors->first('email') }} </span> 
							</div>
							<div class="form-group">
								<label class="form-label"> @lang('messages.feedback') </label>
								{!! Form::textarea('feedback', '', ['class' => 'form-control']) !!}
								<span class="text-danger"> {{ $errors->first('feedback') }} </span> 
							</div>
							@if(checkEnabled('ReCaptcha') && credentials('version','ReCaptcha') == '2')
								<div class="recaptcha-container mt-2">
									<div class="g-recaptcha" data-sitekey="{{ credentials('site_key','ReCaptcha') }}"></div>
								</div>
							@endif
							@if($errors->has('g-recaptcha-response'))
								<div class="form-group">
									<span class="text-danger"> {{ $errors->first('g-recaptcha-response') }} </span>
								</div>
							@endif
						</div>
						<div class="text-end">
							<button type="submit" class="btn btn-primary"> @lang('messages.submit') </button>
						</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
@endsection