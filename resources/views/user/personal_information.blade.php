<div class="row">
	<div class="update_form col-md-6 mt-4 px-0">
		<div class="container personal_info_section">
			<ul class="list-unstyled">
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.user_id')
							</div>
							<div class="text-content">
								<p> @{{ user.id }} </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary disabled" disabled>
								@lang('messages.edit')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row mt-4">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.legal_name')
							</div>
							<div class="text-content">
								<p v-show="!show_legal_name"> @{{ user.first_name+' '+user.last_name }} </p>
								<p class="text-gray" v-show="show_legal_name"> @lang('messages.legal_name_desc') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :disabled="!show_legal_name" :class="getEditProfileClass(show_legal_name)" v-on:click="toggleSection('show_legal_name',true)" v-show="!show_legal_name">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_legal_name)" v-on:click="toggleSection('show_legal_name',false)" v-show="show_legal_name">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-legal_name" v-show="show_legal_name">
						<div class="col-md-6">
							<div class="form-group">
								<label for="first_name" class="form-label">
									@lang('messages.first_name')
								</label>
								{!! Form::text('first_name',null, ['class' => 'form-control','v-model' => 'user.first_name']) !!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="last_name" class="form-label">
									@lang('messages.last_name')
								</label>
								{!! Form::text('last_name',null, ['class' => 'form-control','v-model' => 'user.last_name']) !!}
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','legal_name')" :disabled="user.first_name == '' || user.last_name == ''">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.gender')
							</div>
							<div class="text-content" v-show="!show_gender">
								<p v-show="user.user_information.gender"> @{{ user.user_information.gender }} </p>
								<p class="text-gray" v-show="!user.user_information.gender"> @lang('messages.not_specified') </p>
							</div>
						</div>
						<div class="text-end col-4">
							@if($user->user_information->gender!==null)
								<button disabled type="button" class="btn btn-primary" :class="getEditProfileClass(show_gender)" v-on:click="toggleSection('show_gender',true)" v-show="!show_gender">
									@lang('messages.edit')
								</button>
							@else
								<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_gender)" v-on:click="toggleSection('show_gender',true)" v-show="!show_gender">
									@lang('messages.edit')
								</button>
							@endif
						</div>
					</div>
					<div class="row edit-gender" v-show="show_gender">
						<div class="col-md-12">
							<div class="form-group">
								<label for="gender" class="form-label">
									@lang('messages.gender')
								</label>
								<select name="gender" class="form-select mt-2" v-model="user.user_information.gender">
									<option value="" disabled> @lang('messages.choose') </option>
									<option value="Male"> @lang('messages.male') </option>
									<option value="Female"> @lang('messages.female') </option>
									<option value="Other"> @lang('messages.others') </option>
								</select>
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','gender')" :disabled="!user.user_information.gender">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.date_of_birth')
							</div>
							<div class="text-content" v-show="!show_dob">
								@{{ user.user_information.dob_formatted }}
							</div>
						</div>
						<div class="text-end col-4">
							@if(Carbon\Carbon::parse($user->user_information->dob)->format('Y-m-d')!=='0000-11-30')
								<button disabled type="button" class="btn btn-primary" :class="getEditProfileClass(show_dob)" v-on:click="toggleSection('show_dob',true)" v-show="!show_dob">
									@lang('messages.edit')
								</button>
							@else
								<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_dob)" v-on:click="toggleSection('show_dob',true)" v-show="!show_dob">
									@lang('messages.edit')
								</button>
							@endif	
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_dob)" v-on:click="toggleSection('show_dob',false)" v-show="show_dob">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-dob" v-show="show_dob">
						<div class="input-group m-0">
							<div class="form-group col-3 me-3 pt-2">
								{!! Form::selectMonthWithDefault('birthday_month', null, null, ['class' => 'form-select', 'v-model' => 'user_birthday.month']) !!}
							</div>
							<div class="form-group col-3 me-3 pt-2">
								{!! Form::selectRangeWithDefault('birthday_day', 1, 31, null, null, [ 'class' => 'form-select', 'v-model' => 'user_birthday.day']) !!}
							</div>
							<div class="form-group col-3 me-3 pt-2">
								{!! Form::selectRangeWithDefault('birthday_year', date('Y'), date('Y') - 50, '', null,[ 'class' => 'form-select', 'v-model' => 'user_birthday.year']) !!}
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','dob')">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.user_email')
							</div>
							<div class="text-content">
								<p v-show="!show_email_addr"> @{{ user.email }} </p>
								<p class="text-gray" v-show="show_email_addr"> @lang('messages.email_address_desc') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_email_addr)" v-on:click="toggleSection('show_email_addr',true)" v-show="!show_email_addr" :disabled="!show_email_addr">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_email_addr)" v-on:click="toggleSection('show_email_addr',false)" v-show="show_email_addr">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-email_addr" v-show="show_email_addr">
						<div class="col-md-12">
							<div class="form-group pt-1">
								<label for="email" class="form-label">
									@lang('messages.email')
								</label>
								{!! Form::text('email',null, ['class' => 'form-control','v-model' => 'user.email']) !!}
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','email_addr')">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.home') @lang('messages.address')
							</div>
							<div class="text-content">
								<p v-show="!show_address"> @{{ user.user_information.address }} </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_address)" v-on:click="toggleSection('show_address',true)" v-show="!show_address">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_address)" v-on:click="toggleSection('show_address',false)" v-show="show_address">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-email_addr" v-show="show_address">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="country_code" class="form-label">
									@lang('messages.country')
								</label>
								{!! Form::select('country_code',$countries->pluck('full_name','name'),null,['class' => 'form-select','disabled' => 'disabled','v-model' => 'user.user_information.country_code', 'placeholder' => Lang::get('messages.select'), 'v-on:change' => "user.user_information.city = ''"]) !!}
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="street_address" class="form-label">
									@lang('messages.street_address')
								</label>
								@if($user->user_information->address_line_1 ==='')
									{!! Form::text('address_line_1',null, ['class' => 'form-control','v-model' => 'user.user_information.address_line_1', 'placeholder' => Lang::get('messages.street_address')]) !!}
								@else
									{!! Form::text('address_line_1',null, ['class' => 'form-control','v-model' => 'user.user_information.address_line_1', 'placeholder' => Lang::get('messages.street_address'),'disabled' => 'disabled']) !!}
								@endif
							</div>
							<div class="col-md-6 form-group">
								<label for="ward" class="form-label">
									@lang('messages.ward')
								</label>
								@if($user->user_information->address_line_2 ==='')
									{!! Form::text('address_line_2',null, ['class' => 'form-control','v-model' => 'user.user_information.address_line_2', 'placeholder' => Lang::get('messages.ward')]) !!}
								@else
									{!! Form::text('address_line_2',null, ['class' => 'form-control','v-model' => 'user.user_information.address_line_2', 'placeholder' => Lang::get('messages.ward'),'disabled' => 'disabled']) !!}
								@endif
							</div>
							<div class="col-md-6 form-group">
								<label for="city_desc" class="form-label">
									@lang('messages.city_desc')
								</label>
								<select name="city" class="form-select" id="city" v-model="user.user_information.city" disabled>
									<option value="">@lang('messages.select')</option>
									<option :value="city.name" v-for="city in cities" v-show="city.country == user.user_information.country_code">@{{city.name}}</option>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="state_desc" class="form-label">
									@lang('messages.state_desc')
								</label>
								@if($user->user_information->state ==='')
									{!! Form::text('state',null, ['class' => 'form-control','v-model' => 'user.user_information.state', 'placeholder' => Lang::get('messages.state_desc')]) !!}
								@else
									{!! Form::text('state',null, ['class' => 'form-control','v-model' => 'user.user_information.state', 'placeholder' => Lang::get('messages.state_desc'),'disabled' => 'disabled']) !!}
								@endif
							</div>
							<div class="col-md-6 form-group">
								<label for="postal_code" class="form-label">
									@lang('messages.postal_code')
								</label>
								@if($user->user_information->postal_code ==='')
									{!! Form::text('postal_code',null, ['class' => 'form-control','v-model' => 'user.user_information.postal_code', 'placeholder' => Lang::get('messages.postal_code')]) !!}
								@else
									{!! Form::text('postal_code',null, ['class' => 'form-control','v-model' => 'user.user_information.postal_code', 'placeholder' => Lang::get('messages.postal_code'),'disabled' => 'disabled']) !!}
								@endif
							</div>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="saveProfileData('personal_info','address')" :disabled="!user.user_information.city || !user.user_information.country_code">
								@lang('messages.save')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.mobile_number')
							</div>
							<div class="text-content">
								<p v-show="user.phone_number && !show_phone_number"> @{{ user.phone_number }} </p>
								<p class="text-gray" v-show="!user.phone_number && !show_phone_number"> @lang('messages.not_provided') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_phone_number)" v-on:click="toggleSection('show_phone_number',true)" v-show="!show_phone_number" :disabled="!show_phone_number">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_phone_number)" v-on:click="toggleSection('show_phone_number',false)" v-show="show_phone_number">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-phone_number" v-show="show_phone_number">
						<div class="col-md-12">
							<div class="row mt-3">
								<div class="col-sm-4">
									<select class="form-select" v-model="user.country_code">
										@foreach($countries as $country)
										<option value="{{ $country->name }}"> {{ $country->full_name.' (+'.$country->phone_code.')' }} </option>
										@endforeach
									</select>
								</div>
								<div class="col-sm-8">
									{!! Form::number('phone_number',null, ['class' => 'form-control','v-model' => 'user.phone_number']) !!}
								</div>
							</div>
							<p class="text-danger"> @{{ verfication_data.error_message }} </p>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="numberVerification('send_otp')" v-show="user.phone_number != original_user.phone_number">
								@lang('messages.send_verification_code')
							</button>
						</div>
					</div>
					<div class="row edit-verification_code" v-show="show_verification_code">
						<div class="col-md-12">
							<h5> @lang('messages.enter_your_security_code') </h5>
							<p class="h6 text-gray"> @{{ verfication_data.status_message }} </p>
							<div class="form-group pt-0">
								{!! Form::text('verification_code',null, ['class' => 'form-control','v-model' => 'verfication_data.code']) !!}
							</div>
							<p class="text-danger"> @{{ verfication_data.error_message }} </p>
						</div>
						<div class="col-md-12 mt-1">
							<button type="button" class="btn btn-primary" v-on:click="numberVerification('verify_otp')">
								@lang('messages.verify')
							</button>
						</div>
					</div>
				</li>
				
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.government_document')
							</div>
							<div class="text-content">
								<p v-show="user.user_document_src && !show_user_document_src"> @{{ user.verification_status }} </p>
								<p class="text-gray" v-show="!user.user_document_src && !show_user_document_src"> @lang('messages.not_provided') </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_document_src)" v-on:click="toggleSection('show_user_document_src',true)" v-show="!show_user_document_src">
								@lang('messages.edit')
							</button>
							<button type="button" class="btn btn-primary" :class="getEditProfileClass(show_user_document_src)" v-on:click="toggleSection('show_user_document_src',false)" v-show="show_user_document_src">
								@lang('messages.cancel')
							</button>
						</div>
					</div>
					<div class="row edit-phone_number" v-show="show_user_document_src">
						<div class="col-md-12" v-if="user.verification_status == 'resubmit'">
							<textarea class="form-control mt-2" disabled>@{{ user.resubmit_reason }}</textarea>							
						</div>
						<div v-else class="col-md-12">
							<div class="col-md-4 text-center " :class="{'loading' : isLoading}">
								<img class="img-fluid rounded" :src="user.user_document_src"/>
							</div>
						</div>
						<div class="col-md-12" v-if="user.verification_status != 'Verified'">
							<button type="button" class="btn btn-sm btn-block bg-primary mt-4 text-white" onclick="$('#government_document').trigger('click');" :disabled="isLoading">
							 @lang('messages.upload_file')
							</button>
							<input type="file" name="government_document" id="government_document" class="d-none" accept="image/*" v-on:change="saveUserDocument($event);" />
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.created_on')
							</div>
							<div class="text-content">
								<p> @{{ user.formatted_created_at }} </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary disabled" disabled>
								@lang('messages.edit')
							</button>
						</div>
					</div>
				</li>
				<li class="line-divider"></li>
				<li class="mt-4">
					<div class="row">
						<div class="col-8">
							<div class="fw-bold">
								@lang('messages.status')
							</div>
							<div class="text-content">
								<p> @{{ user.status }} </p>
							</div>
						</div>
						<div class="text-end col-4">
							<button type="button" class="btn btn-primary disabled" disabled>
								@lang('messages.edit')
							</button>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-md-4 offset-md-2">
		<div class="card">
			<div class="card-body">
				<div class="card-header bg-light mb-4">
					<p class="h5 card-title"> @lang('messages.profile_photo') </p>
				</div>
				<div class="row update_form">
					<div class="text-center px-5 profile_photo_section">
						<div class="photos-section">
							<div class="">
								<div class="col flex-grow-0 text-center" :class="{'loading' : isLoading}">
									<div class="position-relative crop-img">
										<div class="position-absolute cropout-img">
											<img class="img-fluid" :src="user.profile_picture_src"/>
										</div>
										<div class="rounded-circle">
											<img class="img-fluid" :src="user.profile_picture_src"/>
										</div>
										<a href="#" class="common-link right_corner_icon hover-card text-white" v-on:click="removeProfilePicture();">
											<i class="icon icon-delete" area-hidden="true"></i>
										</a>
									</div>
								</div>
								<div class="col mt-md-0 mt-3">
									{{--
									<p class="h6 text-justify lh-base"> @lang('messages.profile_photos_desc2',['replace_key_1' => $site_name]) </p>
									--}}
									<button type="button" class="bg-white btn btn-block mt-4 text-black-50 w-100" onclick="$('#profile_picture').trigger('click');" :disabled="isLoading">
									 @lang('messages.upload_file')
									</button>
									<input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" v-on:change="saveProfilePicture($event);" />
									<div class="col-12 mt-0">
										<small class="text-muted float-start">Max upload size for logo image is 1mb</small>
									</div>							
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card mt-4">
			<div class="card-body">
				<h5 class="card-title">
					<i class="icon icon-info icon-large theme-color" area-hidden="true"></i>
				</h5>
				<span class="h6 fw-bold">
					@lang('messages.what_info_shared_with_other')
				</span>
				<p class="card-text mt-2">
					@lang('messages.contact_info_shared_after_reservation',['replace_key_1' => $site_name])
				</p>
			</div>
		</div>
	</div>
</div>