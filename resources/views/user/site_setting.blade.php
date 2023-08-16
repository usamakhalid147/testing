<div class="row">
	<div class="update_form col-md-6 mt-4 px-0">
		<div class="container personal_info_section">
			<ul class="list-unstyled">
				{{-- <li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.preferred_language')
							</div>
							<div class="text-content" v-show="!show_user_language">
								<p v-show="user.user_language" v-show="user.user_language"> @{{ user.user_language_name }} </p>
								<p class="text-gray" v-show="!user.user_language"> @lang('messages.not_specified') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_language)" v-on:click="toggleSection('show_user_language',true)" v-show="!show_user_language">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_language)" v-on:click="toggleSection('show_user_language',false)" v-show="show_user_language">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					{{--<div class="row edit-user_language animated fadeInDown" v-show="show_user_language">
						<div class="col-md-12">
							<div class="form-group">
								<div class="form-group">
								    {!! Form::select('user_language',$language_list, null, ['id' => 'user-language','class' => 'form-select mt-2','v-model' => 'user.user_language']) !!}
								  </div>
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','user_language')" :disabled="!user.user_language">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li> --}}
				{{-- <li class="line-divider"></li> --}}
				{{-- <li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.preferred_currency')
							</div>
							<div class="text-content" v-show="!show_user_currency">
								<p v-show="user.user_currency" v-show="user.user_currency"> @{{ user.user_currency }} </p>
								<p class="text-gray" v-show="!user.user_currency"> @lang('messages.not_specified') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_currency)" v-on:click="toggleSection('show_user_currency',true)" v-show="!show_user_currency">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_currency)" v-on:click="toggleSection('show_user_currency',false)" v-show="show_user_currency">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-user_currency animated fadeInDown" v-show="show_user_currency">
						<div class="col-md-12">
							<div class="form-group">
							    {!! Form::select('user_currency',$currency_list, null, ['id' => 'user-currency','class' => 'form-select mt-2','v-model' => 'user.user_currency']) !!}
							 </div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','user_currency')" :disabled="!user.user_currency">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li> --}}
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.preferred_timezone')
							</div>
							<div class="text-content" v-show="!show_timezone">
								<p v-show="user.timezone" v-show="user.timezone"> @{{ user.timezone }} </p>
								<p class="text-gray" v-show="!user.timezone"> @lang('messages.not_specified') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_timezone)" v-on:click="toggleSection('show_timezone',true)" v-show="!show_timezone">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_timezone)" v-on:click="toggleSection('show_timezone',false)" v-show="show_timezone">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-timezone animated fadeInDown" v-show="show_timezone">
						<div class="col-md-12">
							<div class="form-group">
								{!! Form::select('timezone',$timezones, null, ['id' => 'user-timezone','class' => 'form-select mt-2','v-model' => 'user.timezone']) !!}
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','timezone')" :disabled="!user.timezone">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
			</ul>
		</div>
	</div>
	<div class="offset-md-2 col-md-4 mt-4 pt-4">
		<div class="card">
			<div class="card-body">
				<h5 class="card-title">
					<i class="icon icon-preference icon-large theme-color" area-hidden="true"></i>
				</h5>
				<span class="h6 fw-bold">
					@lang('messages.your_global_preference')
				</span>
				<p class="card-text mt-2">
					@lang('messages.change_your_preference')
				</p>
			</div>
		</div>
	</div>
</div>