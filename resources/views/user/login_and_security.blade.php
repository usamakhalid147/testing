<div class="row">
	<div class="update_form col-md-6 mt-4 px-0">
		<div class="container personal_info_section">
			<ul class="list-unstyled">
				<li class="mt-4" v-show="user.has_signup_with_email">
					<div class="d-flex justify-content-between">
						<div>
							<div class="fw-bold">
								@lang('messages.password')
							</div>
							<div class="text-content">
							</div>
						</div>
						<div>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_password)" v-on:click="toggleSection('show_password',true)" v-show="!show_password">
								@lang('messages.update')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_password)" v-on:click="toggleSection('show_password',false)" v-show="show_password">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-password" :class="{'loading' : isLoading}" v-show="show_password">
						<div class="">
							<div class="password-with-toggler input-group floating-input-group flex-nowrap">
								<div class="form-floating flex-grow-1">
									<input type="password" name="current_password" class="form-control current" v-model="current_password">
									<label for="current_password" class="form-label">
										@lang('messages.current_password')
									</label>
								</div>
								<span class="input-group-text">
									<i class="icon icon-eye cursor-pointer toggle-password active" data-password="current" area-hidden="true"></i>
								</span>
							</div>
							<span class="text-danger"> @{{ (verfication_data.error_message.current_password) ? verfication_data.error_message.current_password[0] : '' }} </span>
						</div>
						<div class="">
							<div class="password-with-toggler input-group floating-input-group flex-nowrap">
								<div class="form-floating flex-grow-1">
									<input type="password" name="password" class="form-control new" v-model="password">
									<label for="password" class="form-label">
										@lang('messages.new_password')
									</label>
								</div>
								<span class="input-group-text">
									<i class="icon icon-eye cursor-pointer toggle-password active" data-password="new" area-hidden="true"></i>
								</span>
							</div>
							<span class="text-danger"> @{{ (verfication_data.error_message.password) ? verfication_data.error_message.password[0] : '' }} </span>
						</div>
						<div class="">
							<div class="password-with-toggler input-group floating-input-group flex-nowrap">
								<div class="form-floating flex-grow-1">
									<input type="password" name="password_confirmation" class="form-control confirm" v-model="password_confirmation">
									<label for="password_confirmation" class="form-label">
										@lang('messages.confirm_password')
									</label>
								</div>
								<span class="input-group-text">
									<i class="icon icon-eye cursor-pointer toggle-password active" data-password="confirm" area-hidden="true"></i>
								</span>
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('login_and_security','password')">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider" v-if="user.has_signup_with_email"></li>
				@if(credentials('is_enabled','Facebook'))
				<li class="mt-4">
					<div class="d-flex justify-content-between">
						<div>
							<div class="fw-bold">
								@lang('messages.facebook')
							</div>
							<div class="text-content">
								<p class="text-gray"> {{ $user->user_verification->facebook != 1 ? Lang::get('messages.not_connected') : Lang::get('messages.connected') }} </p>
							</div>
						</div>
						<div>
							@if($user->user_verification->facebook != 1)
							<button type="button" class="btn btn-primary fb-login-btn">
								@lang('messages.connect')
							</button>
							@else
							<a href="{{ resolveRoute('disconnect_social_account',['auth_type' => 'Facebook']) }}" class="btn btn-primary">
								@lang('messages.disconnect')
							</a>							
							@endif
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				@endif
				@if(credentials('is_enabled','Google'))
				<li class="mt-4">
					<div class="d-flex justify-content-between">
						<div>
							<div class="fw-bold">
								@lang('messages.google')
							</div>
							<div class="text-content">
								<p class="text-gray"> {{ $user->user_verification->google != 1 ? Lang::get('messages.not_connected') : Lang::get('messages.connected') }} </p>
							</div>
						</div>
						<div>
							@if($user->user_verification->google != 1)
							<button type="button" class="btn {{ isSecure() ? 'border-0 p-0' : 'btn-primary' }} g-signin" data-text="continue_with">
								@lang('messages.connect')
							</button>
							@else
							<a href="{{ resolveRoute('disconnect_social_account',['auth_type' => 'Google']) }}" class="btn btn-primary">
								@lang('messages.disconnect')
							</a>							
							@endif
						</div>
					</div>
				</li>
				@endif
			</ul>
		</div>
	</div>
	<div class="offset-md-2 col-md-4 mt-4 pt-4">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">
					<i class="icon icon-security icon-large theme-color" area-hidden="true"></i>
				</h5>
				<span class="h6 fw-bold">
					@lang('messages.keep_account_secure')
				</span>
				<p class="card-text mt-2">
					@lang('messages.keep_account_secure_desc')
				</p>
			</div>
		</div>
	</div>
</div>