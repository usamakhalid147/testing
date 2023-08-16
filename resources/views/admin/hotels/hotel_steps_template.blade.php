@section('description')
<div class="content-section">
	<div class="row">
		<div class="form-group col-md-12" :class="{'required-input': hotel.name == ''}">
			<label for="name">
				@lang('admin_messages.property') @lang('messages.name')
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.catch_guests_attention_with_title')"></i>
				<em class="text-danger">*</em>
			</label>
			{!! Form::text('name',null,['id' => 'name','class'=>'form-control','v-model' => 'hotel.name','placeholder' => Lang::get('admin_messages.property').' '.Lang::get('messages.name')]) !!}
			<span class="text-danger" v-show="error_messages.name"> @lang('validation.required',['attribute' => Lang::get('messages.hotel_name')]) </span>
		</div>
		<div class="form-group col-md-12" :class="{'required-input': hotel.star_rating == ''}">
			<label for="property_type">
				@lang('admin_messages.property') @lang('messages.star_rating') <em class="text-danger">*</em>
			</label>
			<select name="star_rating" id="star_rating" class="form-select" v-model="hotel.star_rating">
				@foreach($star_rating_array as $star_rating)
				<option value="{{ $star_rating['key'] }}" > {{ $star_rating['value'] }} </option>
				@endforeach
			</select>
			<span class="text-danger" v-show="error_messages.star_rating"> @lang('validation.required',['attribute' => Lang::get('messages.star_rating')]) </span>
		</div>
		<div class="form-group col-md-12" :class="{'required-input': hotel.property_type == ''}">
			<label for="property_type">
				@lang('messages.hotel_type') <em class="text-danger">*</em>
			</label>
			<select name="property_type" id="property_type" class="form-control form-select" v-model="hotel.property_type">
				@foreach($property_types as $property_type)
				<option value="{{ $property_type->id }}" :selected="hotel.property_type == {{ $property_type->id }}"> {{ $property_type->name }} </option>
				@endforeach
			</select>
			<span class="text-danger" v-show="error_messages.property_type"> @lang('validation.required',['attribute' => Lang::get('messages.property_type')]) </span>
		</div>
		<div class="form-group col-md-12" :class="{'required-input': hotel.description == ''}">
			<label for="description">
				@lang('messages.about') @lang('admin_messages.property')
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.mention_best_features_of_your_space')"></i>
				<em class="text-danger">*</em>
			</label>
			{!! Form::textarea('description',null,['id' => 'description','class'=>'form-control','rows' => '3', 'v-model' => 'hotel.description','placeholder' => Lang::get('messages.about')." ".Lang::get('admin_messages.property')]) !!}
			<span class="text-danger" v-show="error_messages.description"> @lang('validation.required',['attribute' => Lang::get('messages.description')]) </span>
		</div>
		<div class="form-group col-md-12" :class="{'required-input': hotel.tele_phone_number == ''}">
			<label for="tele_phone_number">
				@lang('admin_messages.property') @lang('messages.tele_phone_number')
				<em class="text-danger">*</em>
			</label>
			{!! Form::text('tele_phone_number',null,['id' => 'tele_phone_number','class'=>'form-control','v-model' => 'hotel.tele_phone_number','placeholder' => Lang::get('admin_messages.property')." ".Lang::get('messages.tele_phone_number')]) !!}
			<span class="text-danger" v-show="error_messages.tele_phone_number"> @lang('validation.required',['attribute' => Lang::get('messages.tele_phone_number')]) </span>
		</div>
		<div class="form-group col-md-12">
			<label for="extension_number">
				@lang('admin_messages.property') @lang('messages.extension_number')
			</label>
			{!! Form::text('extension_number',null,['id' => 'extension_number','class'=>'form-control','v-model' => 'hotel.extension_number','placeholder' => Lang::get('admin_messages.property')." ".Lang::get('messages.extension_number')]) !!}
			<span class="text-danger" v-show="error_messages.extension_number"> @lang('validation.required',['attribute' => Lang::get('messages.extension_number')]) </span>
		</div>
		<div class="form-group col-md-12">
			<label for="fax_number">
				@lang('admin_messages.property') @lang('messages.fax_number')
			</label>
			{!! Form::text('fax_number',null,['id' => 'fax_number','class'=>'form-control','v-model' => 'hotel.fax_number','placeholder' => Lang::get('admin_messages.property')." ".Lang::get('messages.fax_number')]) !!}
			<span class="text-danger" v-show="error_messages.fax_number"> @lang('validation.required',['attribute' => Lang::get('messages.fax_number')]) </span>
		</div>
		<div class="form-group col-md-12">
			<label for="website">
				@lang('admin_messages.property') @lang('messages.website')
			</label>
			{!! Form::text('website',null,['id' => 'website','class'=>'form-control','v-model' => 'hotel.website','placeholder' => Lang::get('admin_messages.property')." ".Lang::get('messages.website')]) !!}
			<span class="text-danger" v-show="error_messages.website"> @lang('validation.required',['attribute' => Lang::get('messages.website')]) </span>
		</div>
		<div class="form-group col-md-12" :class="{'required-input': hotel.email == ''}">
			<label for="email">
				@lang('admin_messages.property') @lang('messages.email')
				<em class="text-danger">*</em>
			</label>
			{!! Form::text('email',null,['id' => 'email','class'=>'form-control','v-model' => 'hotel.email','placeholder' => Lang::get('admin_messages.property')." ".Lang::get('messages.email')]) !!}
			<span class="text-danger" v-show="error_messages.email"> @lang('validation.required',['attribute' => Lang::get('messages.email')]) </span>
		</div>
		<div class="form-group input-file input-file-image">
			<label for="logo">
				@lang('admin_messages.property') @lang('messages.logo')
			</label>
			<div class="img-button-div">
				<img class="img-upload-preview" :src="hotel.logo ? hotel.logo_src : '{{ asset('images/preview_thumbnail.png') }}' ">
				@if($hotel->logo!=='')
					<button id="remove-photo-btn" class="btn btn-danger btn-rounded mb-4" type="button" data-id="{{ $hotel->id }}">@lang('admin_messages.remove_photo')</button>
				@endif 
			</div>
			<input type="file" class="form-control form-control-file" id="image" name="logo" accept="image/*">
			<label for="image" class="label-input-file btn btn-default btn-round">
				<span class="btn-label"><i class="fa fa-file-image"></i></span>
				@lang('admin_messages.choose_file')
			</label>
			<p class="text-danger"> @{{ (error_messages.logo) ? error_messages.logo[0] : '' }} </p>
		</div>
		{{--
		<div class="form-group col-md-12">
			<label for="your_space">
				@lang('messages.your_space') (@lang('messages.optional'))
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.describe_look_and_feel_of_your_space')"></i>
			</label>
			{!! Form::textarea('your_space',null,['id' => 'your_space','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.your_space','placeholder' => Lang::get('messages.your_space')]) !!}
			<p class="text-danger"> {{ $errors->first('your_space') }} </p>
		</div>
		<div class="form-group col-md-12">
			<label for="interaction_with_guests">
				@lang('messages.interaction_with_guests') (@lang('messages.optional'))
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.share_how_available_you_during_stay')"></i>
			</label>
			{!! Form::textarea('interaction_with_guests',null,['id' => 'interaction_with_guests','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.interaction_with_guests','placeholder' => Lang::get('messages.interaction_with_guests')]) !!}
			<p class="text-danger"> {{ $errors->first('interaction_with_guests') }} </p>
		</div>
		<div class="form-group col-md-12">
			<label for="your_neighborhood">
				@lang('messages.your_neighborhood') (@lang('messages.optional'))
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.share_whate_make_neighbourhood_special')"></i>
			</label>
			{!! Form::textarea('your_neighborhood',null,['id' => 'your_neighborhood','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.your_neighborhood','placeholder' => Lang::get('messages.your_neighborhood')]) !!}
			<p class="text-danger"> {{ $errors->first('your_neighborhood') }} </p>
		</div>
		<div class="form-group col-md-12">
			<label for="getting_around">
				@lang('messages.getting_around') (@lang('messages.optional'))
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.add_info_about_getting_around_your_place')"></i>
			</label>
			{!! Form::textarea('getting_around',null,['id' => 'getting_around','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.getting_around','placeholder' => Lang::get('messages.getting_around')]) !!}
			<p class="text-danger"> {{ $errors->first('getting_around') }} </p>
		</div>
		<div class="form-group col-md-12">
			<label for="other_things_to_note">
				@lang('messages.other_things_to_note') (@lang('messages.optional'))
				<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.any_other_things_need_to_note_by_guest')"></i>
			</label>
			{!! Form::textarea('other_things_to_note',null,['id' => 'other_things_to_note','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.other_things_to_note','placeholder' => Lang::get('messages.other_things_to_note')]) !!}
			<p class="text-danger"> {{ $errors->first('other_things_to_note') }} </p>
		</div>
		--}}
		<div class="form-group col-md-12">
		
		</div>
	</div>
</div>
@endsection
@section('location')
<div class="content-section">
	<div class="row">
		<div class="col-md-6" :class="{'required-input': hotel.hotel_address.country_code == ''}">
			<label class="label" for="country_code"> @lang('messages.country') <em class="text-danger"> *</em></label>
			<select name="country_code" class="form-control" id="country_code" v-model="hotel.hotel_address.country_code" v-on:change="hotel.hotel_address.city = ''" readonly>
				<option :value="country.name" v-for="country in countries" v-show="country.name == hotel.user.country_code">@{{country.full_name}}</option>
			</select>
			<span class="text-danger" v-show="error_messages.country_code"> @lang('validation.required',['attribute' => Lang::get('messages.country')]) </span>
		</div>
	</div>
	<div class="row mt-2">
		<div class="form-group col-md-6" :class="{'required-input': hotel.hotel_address.address_line_1 == ''}">
			<label for="address_line"> @lang('admin_messages.property') @lang('messages.address') <em class="text-danger"> *</em></label>
			{!! Form::text('address_line_1',null,['id' => 'address_line_1','class'=>'form-control','autocomplete'=>'off','v-model' => 'hotel.hotel_address.address_line_1','v-on:change' => 'resetAutoComplete()','placeholder' => Lang::get('messages.street_address')]) !!}
			<span class="text-danger" v-show="error_messages.address_line_1"> @lang('validation.required',['attribute' => Lang::get('messages.street_address')]) </span>
			<p class="text-danger" v-show="error_messages.latitude || error_messages.longitude"> @lang('messages.choose_from_autocomplete') </p>
		</div>
		<div class="form-group col-md-6">
			<label for="address_line2"> @lang('messages.ward') </label>
			{!! Form::text('address_line_2',null,['id' => 'address_line_2','class'=>'form-control','v-model' => 'hotel.hotel_address.address_line_2'>'off','placeholder' => Lang::get('messages.unit_apt_suite')]) !!}
		</div>
	</div>
	<div class="row mt-2">
		<div class="form-group col-md-6" :class="{'required-input': hotel.hotel_address.city == ''}">
			<label for="city"> @lang('messages.city_desc') <em class="text-danger"> *</em></label>
			<select name="city" class="form-select" id="city" v-model="hotel.hotel_address.city" readonly>
				<option value="">@lang('messages.select')</option>
				<option :value="city.name" v-for="city in cities" v-show="city.country == hotel.hotel_address.country_code">@{{city.name}}</option>
			</select>
			<span class="text-danger" v-show="error_messages.city"> @lang('validation.required',['attribute' => Lang::get('messages.city')]) </span>
		</div>
		<div class="form-group col-md-6" :class="{'required-input': hotel.hotel_address.state == ''}">
			<label for="state"> @lang('messages.town') <em class="text-danger"> *</em></label>
			{!! Form::text('state',null,['id' => 'state','class'=>'form-control','v-model' => 'hotel.hotel_address.state','autocomplete'=>'off','placeholder' => Lang::get('messages.state')]) !!}
			<span class="text-danger" v-show="error_messages.state"> @lang('validation.required',['attribute' => Lang::get('messages.state')]) </span>
		</div>
	</div>
	<div class="row mt-2">
		<div class="form-group col-md-6" :class="{'required-input': hotel.hotel_address.postal_code == ''}">
			<label for="postal_code"> @lang('messages.postal_code')<em class="text-danger"> *</em> </label>
			{!! Form::text('postal_code',null,['id' => 'postal_code','class'=>'form-control','v-model' => 'hotel.hotel_address.postal_code','placeholder' => Lang::get('messages.postal_code')]) !!}
			<span class="text-danger" v-show="error_messages.postal_code"> @lang('validation.required',['attribute' => Lang::get('messages.postal_code')]) </span>
		</div>
	</div>
</div>
@endsection
@section('photos')
<div class="content-section">
	<div class="photos-section d-flex align-items-center mb-2">
		<div class="add_photos-section">
			<div class="d-flex px-2">
				<input type="file" ref="file" class="d-none" name="photos[]" multiple="true" id="upload_photos" accept="image/*" v-on:change="previewPhoto($event);">
				<button type="button" class="btn btn-default" onclick="$('#upload_photos').trigger('click');">
				@lang('messages.add_photos')
				<i class="fa fa-upload ml-1" aria-hidden="true"></i>
				</button>
			</div>
			<p class="text-danger"> @{{ (error_messages.photos) ? error_messages.photos : '' }} </p>
		</div>
		<div class="ms-auto" v-show="hotel.hotel_photos.length > 0">
			@{{ hotel.hotel_photos.length }}
			<span v-show="!hotel.hotel_photos.length > 1"> @choice('messages.photo',1) </span>
			<span v-show="hotel.hotel_photos.length > 1"> @choice('messages.photo',2) </span>
		</div>
	</div>
	<div class="hotel_image-container mt-4">
		<ul class="row list-unstyled hotel_image-row">
			{!! Form::hidden('removed_photos',null,[':value' => 'removed_photos.toString()']) !!}
			<li v-for="(image,index) in hotel.hotel_photos" class="image-wrapper col-md-3 col-lg-4" id="@{{ 'hotel_photo_'+image.id }}">
				{!! Form::hidden('image_ids',null,['class'=>'image_id',':value' => ' image.id']) !!}
				<p class="text-danger" v-if="image.is_error"> This Image Can't exceed 5 mb. </p>
				<div class="card">
					<img :src="image.image_src" class="card-img-top rounded hotel_image">
					<button type="button" class="hotel-delete_icon fas fa-trash" v-on:click="deletePhoto(index)"> 
					</button>
					<div class="card-body d-none">
						<p class="card-text">
							{!! Form::textarea('photos_description',null,['id' => 'description','class'=>'form-control','rows' => '2', 'v-model' => 'image.description']) !!}
						</p>
					</div>
				</div>
			</li>
		</ul>
	</div>
</div>
@endsection
@section('contacts')
<div class="content-section">
	<div class="form-group row" :class="{'required-input': hotel.contact_email == '' || hotel.contact_email == undefined }">
		<label for="contact_email" class="col-sm-5 col-form-label">@lang('messages.contact_email_desc') <em class="text-danger"> *</em></label>
		<div class="col-sm-7">
			{!! Form::email('contact_email',null,['class'=>'form-control', 'placeholder' => Lang::get('messages.contact_email_desc'),'v-model' => 'hotel.contact_email']) !!}
			<span class="text-danger"> @{{ (error_messages.contact_email) ? error_messages.contact_email[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row" :class="{'required-input': hotel.cancel_email == '' || hotel.cancel_email == undefined }">
		<label for="cancel_email" class="col-sm-5 col-form-label">@lang('messages.cancel_email_desc') <em class="text-danger"> *</em></label>
		<div class="col-sm-7">
			{!! Form::text('cancel_email',null,['class'=>'form-control', 'placeholder' => Lang::get('messages.cancel_email_desc'),'v-model' => 'hotel.cancel_email']) !!}
			<span class="text-danger"> @{{ (error_messages.cancel_email) ? error_messages.cancel_email[0] : '' }} </span>
		</div>
	</div>
</div>
@endsection
@section('booking')
<div class="content-section">
	<input type="hidden" name="notice_days" value="0">
	{{--
	<div class="form-group row">
		<label for="notice_days" class="col-sm-3 col-form-label"> @lang('messages.notice_days') </label>
		<div class="col-sm-9">
			{!! Form::number('notice_days',$hotel->notice_days,['class'=>'form-control', 'placeholder' => Lang::get('messages.notice_days')]) !!}
			<span class="text-danger"> @{{ (error_messages.notice_days) ? error_messages.notice_days[0] : '' }} </span>
		</div>
	</div>
	--}}
	<div class="form-group row">
		<label for="minimum_length_of_stay" class="col-sm-3 col-form-label"> @lang('messages.min_los') <em class="text-danger"> *</em></label>
		<div class="col-sm-9" :class="{'required-input': hotel.min_los == ''}">
			{!! Form::number('min_los',null,['class'=>'form-control', 'placeholder' => Lang::get('messages.min_los'), 'v-model' => 'hotel.min_los']) !!}
			<span class="text-danger"> @{{ (error_messages.min_los) ? error_messages.min_los[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row">
		<label for="maximum_length_of_stay" class="col-sm-3 col-form-label"> @lang('messages.max_los') </label>
		<div class="col-sm-9">
			{!! Form::number('max_los',$hotel->max_los,['class'=>'form-control', 'placeholder' => Lang::get('messages.max_los')]) !!}
			<span class="text-danger"> @{{ (error_messages.max_los) ? error_messages.max_los[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label"> @lang('messages.when_can_guests_checkin')  </label>
		<div class="col-sm-9">
			{!! Form::select('checkin_time',$checkin_times_array, $hotel->checkin_time,['class'=>'form-select','v-model' => 'hotel.checkin_time']) !!}
			<span class="text-danger"> @{{ (error_messages.checkin_time) ? error_messages.checkin_time[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label"> @lang('messages.when_can_guests_checkout') </label>
		<div class="col-sm-9">
			{!! Form::select('checkout_time',$checkout_times_array, $hotel->checkout_time,['class'=>'form-select','v-model' => 'hotel.checkout_time']) !!}
			<span class="text-danger"> @{{ (error_messages.checkout_time) ? error_messages.checkout_time[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row">
		<label for="hotel_policy" class="col-sm-3 col-form-label"> @lang('messages.hotel_policy') </label>
		<textarea name="hotel_policy" class="form-control hotel_policy" v-model="hotel.hotel_policy" id="content"></textarea>
		<span class="text-danger">{{ $errors->first('hotel_policy') }}</span>
	</div>
	<div class="form-group row">
		<label for="checkin_guidance">
			@lang('messages.checkin_guidance')
			<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.share_any_special_instruction')"></i>
		</label>
		{!! Form::textarea('guidance','',['id' => 'checkin_guidance','class'=>'form-control','rows' => '2', 'v-model' => 'hotel.guidance','placeholder' => Lang::get('messages.checkin_guidance')]) !!}
		<span class="text-danger"> {{ $errors->first('guidance') }} </span>
	</div>
</div>
@endsection
@section('tax')
<div class="content-section">
	<div class="form-group row">
		<label class="form-label col-md-3">@lang('messages.service_charge') @lang('admin_messages.type')</label>
		<div class="col-md-9">
			<div class="form-check-inline">
				<label class="form-check-label"> 
					<input class="form-check-input" type="radio" name="service_charge_type" value="fixed" v-model="hotel.service_charge_type" v-on:change="updateServiceCharge()">
					@lang('admin_messages.fixed')
				</label>
			</div>
			<div class="form-check-inline">
				<label class="form-check-label"> 
					<input class="form-check-input" type="radio" name="service_charge_type" value="percentage" v-model="hotel.service_charge_type" v-on:change="updateServiceCharge()">
					@lang('admin_messages.percentage')
				</label>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="service_charge" class="col-sm-3 col-form-label"> @lang('messages.service_charge')</label>
		<div class="col-sm-9">
			<div class="input-group">
				{!! Form::text('service_charge',null,['class'=>'form-control','v-model' => 'hotel.service_charge']) !!}
				<div class="input-group-append">
					<span class="input-group-text" v-if="hotel.service_charge_type == 'fixed'">{{ session('currency') }}</span>
					<span class="input-group-text" v-if="hotel.service_charge_type == 'percentage'">%</span>
				</div>
			</div>
			<span class="text-danger" v-if="error_messages['service_charge']"> @{{ (error_messages.service_charge) ? error_messages.service_charge[0] : '' }}
			</span>
		</div>
	</div>
	<div class="form-group row">
		<label class="form-label col-md-3">@lang('messages.property_tax') @lang('admin_messages.type')</label>
		<div class="col-md-9">
			<div class="form-check-inline">
				<label class="form-check-label"> 
					<input class="form-check-input" type="radio" name="property_tax_type" value="fixed" v-model="hotel.property_tax_type" v-on:change="updatePropertyTax()">
					@lang('admin_messages.fixed')
				</label>
			</div>
			<div class="form-check-inline">
				<label class="form-check-label"> 
					<input class="form-check-input" type="radio" name="property_tax_type" value="percentage" v-model="hotel.property_tax_type" v-on:change="updatePropertyTax()">
					@lang('admin_messages.percentage')
				</label>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<label for="property_tax" class="col-sm-3 col-form-label"> @lang('messages.property_tax')</label>
		<div class="col-sm-9">
			<div class="input-group">
				{!! Form::text('property_tax',null,['class'=>'form-control','v-model' => 'hotel.property_tax']) !!}
				<div class="input-group-append">
					<span class="input-group-text" v-if="hotel.property_tax_type == 'fixed'">{{ session('currency') }}</span>
					<span class="input-group-text" v-if="hotel.property_tax_type == 'percentage'">%</span>
				</div>
			</div>
			<span class="text-danger" v-if="error_messages['property_tax']"> @{{ (error_messages.property_tax) ? error_messages.property_tax[0] : '' }}
			</span>
		</div>
	</div>
</div>
@endsection
@section('more_details')
<div class="content-section">
	<div class="row">
		@if($amenity_types->count() > 0)
		<div class="amenity-section mt-4">
			<div class="content-title">
				<p class="fw-bold h4">
					@lang('messages.what_amenities_do_you_offer')
					<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.these_are_amenities_guests_usually_expect')"></i><em class="text-danger"> *</em>
				</p>
			</div>
			<div class="content-form">
				<div class="form-group pt-1">
					{!! Form::hidden('amenities', null, ['id' => 'amenities', ':value' => 'hotel.amenities']) !!}
					@foreach($amenity_types as $amenity_type)
						@if($amenity_type->amenities->count() > 0)
							<div class="row">
								<div class="col-12 my-2">
									<h5 class="fw-bold">
										{{ $amenity_type->name }}
										@if($amenity_type->description != '')
											<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" title="{{ $amenity_type->description }}" area-hidden="true"></i>
										@endif
									</h5>
								</div>
							</div>
							<div class="row">
								@foreach($amenity_type->amenities as $amenity)
								<div class="col-md-4">
									<input type="checkbox" id="amenity_{{$amenity->id}}" class="form-check-input amenities me-2" value="{{ $amenity->id }}" :checked="{{ $selected_amenities->where('id',$amenity->id)->count() }}">
									<label class="form-check-label" for="amenity_{{$amenity->id}}"> 
										{{ $amenity->name }}
										@if($amenity->description != '')
										<i class="icon icon-info cursor-pointer" data-bs-toggle="tooltip" title="{{ $amenity->description }}" area-hidden="true"></i>
										@endif
									</label>
								</div>
								@endforeach
							</div>
						@endif
					@endforeach
					<p class="text-danger"> {{ $errors->first('amenities') }} </p>
				</div>
				<span class="text-danger" v-show="error_messages.amenities"> @lang('validation.required',['attribute' => Lang::get('messages.amenities')]) </span>
			</div>
		</div>
		@endif
		@if($guest_accesses->count() > 0)
		<div class="amenity-section mt-4">
			<div class="content-title">
				<p class="fw-bold h4">
					@lang('messages.what_spaces_can_guests_use')
					<i class="fas fa-question-circle cursor-pointer" data-toggle="tooltip" data-placement="top" title="@lang('messages.include_common_areas')"></i>
				</p>
			</div>
			<div class="content-form">
				<div class="form-group pt-1">
					<input type="hidden" name="guest_accesses" v-model="hotel.guest_accesses">
					<div class="row">
						@foreach($guest_accesses as $guest_access)
						<div class="col-md-4">
							<input type="checkbox" id="guest_access_{{$guest_access->id}}" class="form-check-input guest_accesses me-2" value="{{ $guest_access->id }}" :checked="{{ $selected_guest_accesses->where('id',$guest_access->id)->count() }}">
							<label class="form-check-label" for="guest_access_{{$guest_access->id}}"> 
								{{ $guest_access->name }}
								@if($guest_access->description != '')
								<i class="icon icon-info cursor-pointer" data-bs-toggle="tooltip" title="{{ $guest_access->description }}" area-hidden="true"></i>
								@endif
							</label>
						</div>
						@endforeach
					</div>
					<p class="text-danger"> {{ $errors->first('guest_accesses') }} </p>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>
@endsection
@section('hotel_status')
<div class="content-section">
	<div class="form-group row" :class="{'required-input': hotel.status == ''}">
		<label for="status" class="col-sm-3 col-form-label"> @lang('messages.hotel_status') <em class="text-danger">*</em></label>
		<div class="col-sm-9">
			{!! Form::select('status',$status_array,null,['class'=>'form-select','v-model' => 'hotel.status']) !!}
			<span class="text-danger" v-if="error_messages['status']"> @lang('validation.required',['attribute' => Lang::get('messages.hotel_status')])
				</span>
		</div>
	</div>
	<div class="form-group row" :class="{'required-input': hotel.admin_status == ''}">
		<label for="admin_status" class="col-sm-3 col-form-label"> @lang('messages.admin_status') <em class="text-danger">*</em></label>
		<div class="col-sm-9">
			{!! Form::select('admin_status',$admin_status_array,null,['class'=>'form-select','v-model' => 'hotel.admin_status']) !!}
			<span class="text-danger" v-if="error_messages['admin_status']"> @lang('validation.required',['attribute' => Lang::get('messages.admin_status')])
				</span>
		</div>
	</div>
	<div class="form-group row" :class="{'required-input': hotel.admin_commission == ''}">
		<label for="admin_commission" class="col-sm-3 col-form-label"> @lang('messages.admin_commission') % <em class="text-danger">*</em></label>
		<div class="col-sm-9">
			{!! Form::text('admin_commission',null,['class'=>'form-control','v-model' => 'hotel.admin_commission']) !!}
			<span class="text-danger" v-if="error_messages['admin_commission']"> @lang('validation.required',['attribute' => Lang::get('messages.admin_commission')])
				</span>
		</div>
	</div>
</div>
@endsection
@push('scripts')
    <script>
	$(document).ready(function () {
        $('#remove-photo-btn').click(function() {
            var id = $(this).data('id');
            var url = "{{ route('admin.delete_hotel_propety_logo', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    flashMessage("Image Deleted Successfully", 'success');
                    location.reload(true);
                },
                error: function() {
                    alert('Unable to remove photo. Please try again later.');
                }
            });
        });
    });
	</script>
@endpush