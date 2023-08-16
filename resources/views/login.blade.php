@extends('layouts.app')
@section('content')
<main id="site-content" role="main" class="main-container">
	<section class="login-form-wrap my-3 pt-3">
		<div class="card mx-auto">
			<div class="card-body">
				<ul class="social-btn-group">
					@if(checkEnabled('Facebook'))
                    <li class='mt-4'>
                        <button class="btn btn-facebook w-100 py-2 d-flex align-items-center justify-content-center fb-login-btn">
                        <i class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="margin-right: 8px;"><path d="M21 12C21 7.02943 16.9706 3 12 3C7.02943 3 3 7.02943 3 12C3 16.4922 6.29117 20.2155 10.5938 20.8907V14.6016H8.30859V12H10.5938V10.0172C10.5938 7.76156 11.9374 6.51562 13.9932 6.51562C14.9779 6.51562 16.0078 6.69141 16.0078 6.69141V8.90625H14.8729C13.7549 8.90625 13.4062 9.60001 13.4062 10.3117V12H15.9023L15.5033 14.6016H13.4062V20.8907C17.7088 20.2155 21 16.4922 21 12Z" fill="#ffffff"></path></svg>
                        </i>
                        <span class="d-inline-block">
                            @lang('messages.login_with',['replace_key_1' => 'Facebook' ])
                        </span>
                        </button>
                    </li>
                    @endif
					@if(checkEnabled('Google'))
                    <li class='mt-3'>
                        <button class="btn btn-google w-100 p-0 g-signin d-flex align-items-center justify-content-center" data-text="continue_with">
                        <i class="icon">
                            <svg height="24" width="24" viewBox="0 0 24 24" style="margin-right: 8px;"><path d="M12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M7.2 1.53409C7.2 1.00227 7.15227 0.490909 7.06364 -3.19295e-07H0V2.90114H4.03636C3.8625 3.83864 3.33409 4.63295 2.53977 5.16477V7.04659H4.96364C6.38182 5.74091 7.2 3.81818 7.2 1.53409Z" transform="translate(12 10.6367)" fill="#4285F4"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M6.70227 6.075C8.72727 6.075 10.425 5.40341 11.6659 4.25795L9.24205 2.37614C8.57045 2.82614 7.71136 3.09204 6.70227 3.09204C4.74886 3.09204 3.09545 1.77273 2.50568 3.17891e-08H3.16839e-08V1.94318C1.23409 4.39432 3.77045 6.075 6.70227 6.075Z" transform="translate(5.29785 13.4248)" fill="#34A853"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M3.30341 4.79318C3.15341 4.34318 3.06818 3.8625 3.06818 3.36818C3.06818 2.87386 3.15341 2.39318 3.30341 1.94318V3.1719e-08H0.797727C0.289773 1.0125 0 2.15795 0 3.36818C0 4.57841 0.289773 5.72386 0.797727 6.73636L3.30341 4.79318Z" transform="translate(4.5 8.63184)" fill="#FBBC05"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M6.70227 2.98295C7.80341 2.98295 8.79205 3.36136 9.56932 4.10455L11.7205 1.95341C10.4216 0.743182 8.72386 0 6.70227 0C3.77045 0 1.23409 1.68068 3.16839e-08 4.13182L2.50568 6.075C3.09545 4.30227 4.74886 2.98295 6.70227 2.98295Z" transform="translate(5.29785 4.5)" fill="#EA4335"></path></svg>
                        </i>
                        <span class="d-inline-block">
                            @lang('messages.login_with',['replace_key_1' => 'Google' ])
                        </span>
                        </button>
                    </li>
                    @endif
                    @if(checkEnabled('Apple'))
                    <li class='mt-3'>
                        <button class="btn btn-apple w-100 py-2 apple-signin d-flex align-items-center justify-content-center">
                        <i class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="margin-right: 8px;"><path d="M17.0207 11.0941C17.0474 13.9694 19.5431 14.9262 19.5707 14.9384C19.5496 15.0059 19.1719 16.302 18.2559 17.6408C17.4639 18.7982 16.642 19.9514 15.3473 19.9753C14.0751 19.9987 13.666 19.2209 12.2115 19.2209C10.7575 19.2209 10.303 19.9514 9.09866 19.9987C7.84891 20.046 6.89724 18.7471 6.09875 17.5939C4.46712 15.235 3.22022 10.9282 4.89449 8.02108C5.72624 6.57737 7.21262 5.66316 8.82596 5.63971C10.0532 5.6163 11.2115 6.46535 11.9618 6.46535C12.7115 6.46535 14.1191 5.4443 15.5989 5.59425C16.2184 5.62004 17.9574 5.84449 19.074 7.47893C18.984 7.53471 16.9991 8.69025 17.0207 11.0941V11.0941ZM14.6297 4.03363C15.2932 3.23049 15.7398 2.11245 15.6179 1C14.6616 1.03844 13.5051 1.63731 12.8191 2.44001C12.2043 3.15083 11.6659 4.28856 11.8112 5.37899C12.8772 5.46146 13.9662 4.83729 14.6297 4.03364" fill="#ffffff"></path></svg>
                        </i>
                        <span class="d-inline-block">
                            @lang('messages.login_with',['replace_key_1' => 'Apple' ])
                        </span>
                        </button>
                    </li>
                    @endif
					@if(checkEnabled('Twilio'))
                    <li class='mt-3'>
                        <button class="btn btn-mobile w-100 py-2 mobile-signin d-flex align-items-center justify-content-center open-modal" data-current="loginModal" data-target="loginMobileModal">
                        <i class="icon">
                        	<svg version="1.1" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 27.442 27.442" style="enable-background:new 0 0 27.442 27.442;fill:currentColor;" xml:space="preserve"> <g> <path d="M19.494,0H7.948C6.843,0,5.951,0.896,5.951,1.999v23.446c0,1.102,0.892,1.997,1.997,1.997h11.546 c1.103,0,1.997-0.895,1.997-1.997V1.999C21.491,0.896,20.597,0,19.494,0z M10.872,1.214h5.7c0.144,0,0.261,0.215,0.261,0.481 s-0.117,0.482-0.261,0.482h-5.7c-0.145,0-0.26-0.216-0.26-0.482C10.612,1.429,10.727,1.214,10.872,1.214z M13.722,25.469 c-0.703,0-1.275-0.572-1.275-1.276s0.572-1.274,1.275-1.274c0.701,0,1.273,0.57,1.273,1.274S14.423,25.469,13.722,25.469z M19.995,21.1H7.448V3.373h12.547V21.1z"/> </svg>
                        </i>
                        <span class="d-inline-block">
                            @lang('messages.login_with',['replace_key_1' => 'Mobile' ])
                        </span>
                        </button>
                    </li>
                    @endif
                </ul>
				<div class="line-separator my-2 d-flex align-items-center">
					<span class="mx-2">
						@lang('messages.or')
					</span>
				</div>
				<div class="form">
					{!! Form::open(['url' => resolveRoute('authenticate'), 'class' => '','id'=>'user_login_form','method' => "POST"]) !!}
					<div class="form-floating">
						<input type="text" name="login_email" value="{{ displayCrendentials() ? 'ben@cron24.com' : '' }}" class="form-control" placeholder="@lang('messages.email')">
						<label>
							@lang('messages.email')
						</label>
					</div>
					<span class="text-danger"> {{ $errors->first('login_email') }} </span>
					<div class="password-with-toggler input-group floating-input-group flex-nowrap">
						<div class="form-floating flex-grow-1">
							<input type="password" name="login_password" class="password form-control" value="{{ displayCrendentials() ? '12345678' : ''}}" placeholder="@lang('messages.password')">
							<label> @lang('messages.password') </label>
						</div>
						<span class="input-group-text"><i class="icon icon-eye cursor-pointer toggle-password active" area-hidden="true"></i></span>
					</div>
					<span class="text-danger"> {{ $errors->first('login_password') }} </span>
					<div class="form-check">
						<input type="checkbox" name="remember_me" id="log_remember_me" class="form-check-input" checked>
						<label class="form-check-label" for="log_remember_me"> @lang('messages.remember_me') </label>
						<a href="#" class="float-end open-modal" data-current="loginModal" data-target="forgotPasswordModal">
							@lang('messages.forgot_password')?
						</a>
					</div>
					@if(checkEnabled('ReCaptcha') && credentials('version','ReCaptcha') == '2')
					<div class="recaptcha-container mt-2">
						<div class="g-recaptcha" data-sitekey="{{ credentials('site_key','ReCaptcha') }}"></div>
					</div>
					@endif
					@if($errors->has('g-recaptcha-response'))
						<span class="text-danger"> {{ $errors->first('g-recaptcha-response') }} </span>
					@endif
					<div class="form-floating mt-4">
						<button type="submit" class="btn btn-primary d-flex w-100 justify-content-center">
							@lang('messages.login')
						</button>
					</div>
					{!! Form::close() !!}
				</div>
				<div class="line-separator my-2 d-flex align-items-center"></div>
				<div class="mt-4 text-center">
					@lang('messages.dont_have_account')
					<a href="{{ route('signup') }}" class="">
						@lang('messages.signup')
					</a>
				</div>
			</div>
		</div>
	</section>
</main>
@endsection