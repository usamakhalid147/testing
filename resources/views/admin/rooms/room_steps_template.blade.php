@section('room_details')
<div class="content-section">
	<div class="form-group col-md-12" :class="{'required-input': room.name == ''}">
		<label>@lang('admin_messages.category') @lang('admin_messages.name') <em class="text-danger"> *</em></label>
		<input type="text" name="name" id="name" class="form-control" v-model="room.name" placeholder="{{ Lang::get('messages.category').' '.Lang::get('admin_messages.name') }}">
		<span class="text-danger"> @{{ (error_messages.name) ? error_messages.name[0] : '' }} </span>
	</div>
	<div class="form-group col-md-12" :class="{'required-input': room.description == ''}">
		<label>@lang('messages.room') @lang('admin_messages.description') <em class="text-danger"> *</em></label>
		<textarea rows="5" name="description" id="description" class="form-control" v-model="room.description" placeholder="{{ Lang::get('messages.room').' '.Lang::get('admin_messages.description') }}"></textarea>
		<span class="text-danger"> @{{ (error_messages.description) ? error_messages.description[0] : '' }} </span>
	</div>
	{{--<div class="form-group row" :class="{'required-input': room.room_type == ''}">
		<label class="col-md-3">@lang('admin_messages.room_type') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<select name="room_type" id="room_type" class="col-md-9 form-select" v-model="room.room_type">
				@foreach($room_types as $room_type)
				<option value="{{ $room_type->id }}">{{ $room_type->name }}</option>
				@endforeach
			</select>
			<span class="text-danger"> @{{ (error_messages.room_type) ? error_messages.room_type[0] : '' }} </span>
		</div>
	</div>--}}
	<div class="form-group row" :class="{'required-input': room.bed_type == ''}">
		<label class="col-md-3 col-form-label">@lang('admin_messages.bed_type') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<select name="bed_type" id="bed_type" class="col-md-9 form-select" v-model="room.bed_type">
				@foreach($bed_types as $bed_type)
				<option value="{{ $bed_type->id }}">{{ $bed_type->name }}</option>
				@endforeach
			</select>
			<span class="text-danger"> @{{ (error_messages.bed_type) ? error_messages.bed_type[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row" :class="{'required-input': room.beds == ''}">
		<label class="col-md-3 col-form-label">@lang('admin_messages.no_of_beds') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<input type="number" name="beds" id="beds" class="form-control" v-model="room.beds" placeholder="{{ Lang::get('admin_messages.no_of_beds') }}">
			<span class="text-danger"> @{{ (error_messages.beds) ? error_messages.beds[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">@lang('admin_messages.room_size')</label>
		<div class="col-md-9">
			<div class="input-group">
				<input type="hidden" name="room_size_type" id="room_size_type" :value="room.room_size_type">
				<input type="text" name="room_size" id="room_size" class="form-control" v-model="room.room_size" placeholder="{{ Lang::get('messages.room_size') }}">
				<div class="input-group-append">
			    	<button :class="room.room_size_type == 'm2' ? 'btn btn-default' : 'btn btn-no-hover-default'" type="button" v-on:click="room.room_size_type = 'm2'">@lang('messages.sq_m')</button>
			    	<button :class="room.room_size_type == 'sqft' ? 'btn btn-default' : 'btn btn-no-hover-default'" type="button" v-on:click="room.room_size_type = 'sqft'">@lang('messages.sq_ft')</button>
			    </div>	
			</div>
		</div>
	</div>
	<div clas="line-separator"></div>
    <div class="form-group row">
		<label class="col-md-3 col-form-label">@lang('messages.cancellation_policy')</label>
		<div class="col-md-9">
			<div class="mb-3">
				<div class="row text-center">
	    			<div class="col-md-5">
	    				@lang('messages.days_before_check_in')
	    			</div>
	    			<div class="col-md-5">
	    				@lang('messages.refundable_amount')
	    			</div>
	    		</div>
			</div>
			<div class="mb-3" v-for="(cancellation_policy,key) in room.cancellation_policies">
	    		<input type="hidden" :name="'cancellation_policies['+key+'][id]'" :value="cancellation_policy.id">
	    		<div class="row align-items-center">
	    			<div class="col-md-5">
			    		<div :class="{'required-input': cancellation_policy.days == ''}">
			    			<div class="input-group">
				    			<input type="text" :name="'cancellation_policies['+key+'][days]'" class="form-control" v-model="cancellation_policy.days">
				    			<div class="input-group-append">
							      <span class="input-group-text"> days </span>
						        </div>
						    </div>
				    		<span class="text-danger"> @{{ (error_messages['cancellation_policies.'+key+'.days']) ? error_messages['cancellation_policies.'+key+'.days'][0] : '' }} </span>
			    		</div>
			    	</div>
			    	<div class="col-md-5">
			    		<div :class="{'required-input': cancellation_policy.percentage == ''}">
			    			<div class="input-group">
				    			<input type="text" :name="'cancellation_policies['+key+'][percentage]'" class="form-control" v-model="cancellation_policy.percentage">
				    			<div class="input-group-append">
							      <span class="input-group-text"> % </span>
						        </div>
						    </div>
				    		<span class="text-danger"> @{{ (error_messages['cancellation_policies.'+key+'.percentage']) ? error_messages['cancellation_policies.'+key+'.percentage'][0] : '' }} </span>
				    	</div>
				    </div>
				    <div class="col-md-1">
						<button type="button" class="btn btn-icon btn-danger btn-sm" v-on:click="removeCancellationPolicy(key)">
							<span class="fas fa-trash-alt"></span>
						</button>
					</div>
		    	</div>
	    	</div>
			<div class="d-flex">
			    <a href="javascript:void(0)" class="btn btn-primary text-white ms-auto" v-on:click="addCancellationPolicy()">
					@lang('messages.add')
				</a>
		    </div>
		</div>
	</div>
</div>
@endsection
@section('more_details')
<div class="content-section">
	<div class="form-group row">
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
					<input type="hidden" name="amenities" id='amenities' :value="room.amenities"> 
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
									<input type="checkbox" id="amenity_{{$amenity->id}}" class="form-check-input me-2 amenities" value="{{ $amenity->id }}" :checked="{{ $selected_amenities->where('id',$amenity->id)->count() }}">
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
					<span class="text-danger"> @{{ (error_messages.amenities) ? error_messages.amenities[0] : '' }} </span>
				</div>
			</div>
		</div>
		@endif
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
		<div class="ms-auto" v-show="room.hotel_room_photos.length > 0">
			@{{ room.hotel_room_photos.length }}
			<span v-show="!room.hotel_room_photos.length > 1"> @choice('messages.photo',1) </span>
			<span v-show="room.hotel_room_photos.length > 1"> @choice('messages.photo',2) </span>
		</div>
	</div>
	<div class="hotel_image-container mt-4">
		<ul class="row list-unstyled hotel_image-row">
			{!! Form::hidden('removed_photos',null,[':value' => 'removed_photos.toString()']) !!}
			<li v-for="(image,index) in room.hotel_room_photos" class="image-wrapper col-md-3 col-lg-4" :id="'room_photo_'+image.id">
				{!! Form::hidden('image_ids',null,['class'=>'image_id',':value' => ' image.id']) !!}
				<p class="text-danger" v-if="image.is_error"> This Image Can't exceed 5 mb. </p>
				<div class="card">
					<img :src="image.image_src" class="card-img-top rounded hotel_image">
					<button type="button" class="hotel-delete_icon fas fa-trash" v-on:click="deletePhoto(index)">
					</button>
				</div>
			</li>
		</ul>
	</div>
</div>
@endsection
@section('price_details')
<div class="content-section">
	<div class="form-group row" :class="{'required-input': room.number == ''}">
		<label class="col-md-3 col-form-label">@lang('admin_messages.total_no_rooms') <br/> @lang('admin_messages.total_no_category') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<input type="number" name="number" id="number" class="form-control" v-model="room.number" placeholder="{{ Lang::get('admin_messages.total_no_category_desc') }}">
			<span class="text-danger"> @{{ (error_messages.number) ? error_messages.number[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row py-0">
		<label class="col-md-3 align-self-md-center">@lang('admin_messages.occupancy') 
		</label>
		<div class="col-md-9 d-flex">
			<div class="form-group col p-0" :class="{'required-input': room.adults == ''}">
				<label class="fw-normal">@lang('admin_messages.base_adults') <em class="text-danger"> *</em></label>
				<input type="number" name="adults" id="adults" class="form-control" v-model="room.adults" placeholder="{{ Lang::get('admin_messages.base_adults') }}">
				<span class="text-danger"> @{{ (error_messages.adults) ? error_messages.adults[0] : '' }} </span>
			</div>
			<div class="form-group col p-0 ps-2" :class="{'required-input': room.children === ''}">
				<label class="fw-normal">@lang('admin_messages.base_children') <em class="text-danger"> *</em></label>
				<input type="number" name="children" id="children" class="form-control" v-model="room.children" placeholder="{{ Lang::get('admin_messages.base_children') }}">
				<span class="text-danger"> @{{ (error_messages.children) ? error_messages.children[0] : '' }} </span>
			</div>
		</div>
	</div>
	<div class="form-group row py-0">
		<label class="col-md-3 align-self-md-center">@lang('admin_messages.max_occupancy') @lang('admin_messages.per_room')
		</label>
		<div class="col-md-9 d-flex">
			<div class="form-group col p-0" :class="{'required-input': room.max_adults == ''}">
				<label class="fw-normal">@lang('admin_messages.max_adults') <em class="text-danger"> *</em></label>
				<input type="number" name="max_adults" id="max_adults" class="form-control" v-model="room.max_adults" placeholder="{{ Lang::get('admin_messages.max_adults') }}">
				<span class="text-danger"> @{{ (error_messages.max_adults) ? error_messages.max_adults[0] : '' }} </span>
			</div>
			<div class="form-group col p-0 ps-2" :class="{'required-input': room.max_children === ''}">
				<label class="fw-normal">@lang('admin_messages.max_children') <em class="text-danger"> *</em></label>
				<input type="number" name="max_children" id="max_children" class="form-control" v-model="room.max_children" placeholder="{{ Lang::get('admin_messages.max_children') }}">
				<span class="text-danger"> @{{ (error_messages.max_children) ? error_messages.max_children[0] : '' }} </span>
			</div>
		</div>
	</div>
	
	<div class="form-group row" :class="{'required-input': room.hotel_room_price.price == ''}">
		<label class="col-md-3">@lang('admin_messages.base_room_rate_per_night') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<div class="input-group">
				<input type="hidden" name="currency_code" value="{{ session('currency') }}">
				<input type="number" name="price" class="form-control" placeholder="{{ Lang::get('admin_messages.base_room_rate_per_night') }}" v-model="room.hotel_room_price.price">
		  		<div class="input-group-append">
			    	<span class="input-group-text">{{ session('currency') }}</span>
			  	</div>
			</div>
			<span class="text-danger"> @{{ (error_messages.price) ? error_messages.price[0] : '' }} </span>
		</div>
	</div>
	<div class="form-group row py-0">
		<label class="col-md-3 align-self-md-center">
			@lang('admin_messages.extra_occupancy_rate') <br/> @lang('admin_messages.per_night') <em class="text-danger"> *</em></label>
		<div class="col-md-9 d-flex">
			<div class="form-group col p-0">
				<label class="fw-normal">@lang('admin_messages.extra_adult_rate_per_night')</label>
				<div class="input-group">
					<input type="number" name="adult_price" class="form-control" placeholder="{{ Lang::get('admin_messages.extra_adult_rate_per_night') }}" v-model="room.hotel_room_price.adult_price">
			  		<div class="input-group-append">
				    	<span class="input-group-text">{{ session('currency') }}</span>
				  	</div>
			  </div>
			</div>
			<div class="form-group col p-0 ps-2">
				<label class="fw-normal">@lang('admin_messages.extra_children_rate_per_night')</label>
				<div class="input-group">
					<input type="number" name="children_price" class="form-control" placeholder="{{ Lang::get('admin_messages.extra_children_rate_per_night') }}" v-model="room.hotel_room_price.children_price">
			  		<div class="input-group-append">
				    	<span class="input-group-text">{{ session('currency') }}</span>
				  	</div>
				  </div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('other_prices')
<div class="content-section">
	<div class="form-group">
		<div class="content-title my-3" v-if="meal_plan_options.length > 0">
			<p class="fw-bold h4"> @lang('admin_messages.meal_plans') </p>
		</div>
		<div class="content-form">
			<div class="row mb-3" v-for="(meal_plan,key) in room.meal_plans">
				<input type="hidden" :name="'meal_plans['+key+'][id]'" :value="meal_plan.id">
				<div class="col-md-5" :class="{'required-input' : meal_plan.type_id ==''}">
					<select :name="'meal_plans['+key+'][plan]'" class="form-select" v-model="meal_plan.type_id">
						<option value="" disabled> @lang('messages.select') </option>
						<option v-for="option in meal_plan_options" :selected="meal_plan.type_id == option.id" :value="option.id" v-show="option.id == meal_plan.type_id || canDisplayMealPlan(option.id)"> @{{ option.name }} </option>
					</select>
					<span class="text-danger"> @{{ (error_messages['meal_plans.'+key+'.plan']) ? error_messages['meal_plans.'+key+'.plan'][0] : '' }} </span>
				</div>
				<div class="col-md-5" :class="{'required-input' : meal_plan.price <= 0}">
					<div class="input-group">
						<input type="text" :name="'meal_plans['+key+'][price]'" class="form-control" v-model="meal_plan.price" placeholder="@lang('admin_messages.price')">
						<div class="input-group-append">
							<span class="input-group-text">{{ session('currency') }}</span>
						</div>
					</div>
					<span class="text-danger"> @{{ (error_messages['meal_plans.'+key+'.price']) ? error_messages['meal_plans.'+key+'.price'][0] : '' }} </span>
				</div>
				<div class="col-md-2">
					<button class="btn btn-icon btn-danger btn-sm" v-on:click="removeMealPlan(key)">
						<span class="fas fa-trash-alt"></span>
					</button>
				</div>
			</div>
			<div class="d-flex" >
				<a href="javascript:void(0)" class="btn btn-info btn-round" v-on:click="addMealPlan();" v-show="room.meal_plans.length != meal_plan_options.length">
					<span class="fa fa-plus"></span> <span>@lang('messages.add')</span>
				</a>
			</div>
		</div>
		<div class="line-separator"></div>
		<div class="content-title my-3" v-if="bed_types.length > 0">
			<p class="fw-bold h6"> @lang('admin_messages.extra_beds') </p>
		</div>
		<div class="mt-3">
			<div class="row mb-3" v-for="(extra_bed,key) in room.extra_beds">
				<input type="hidden" :name="'extra_beds['+key+'][id]'" :value="extra_bed.id">
				<div class="col-md-3" :class="{'required-input' : extra_bed.type_id ==''}">
					<select :name="'extra_beds['+key+'][plan]'" class="form-select" v-model="extra_bed.type_id">
						<option value="" disabled> @lang('messages.select') </option>
						<option v-for="option in bed_types" :selected="extra_bed.type_id == option.id" :value="option.id" v-show="option.id == extra_bed.type_id || canDisplayExtraBed(option.id)"> @{{ option.name }} </option>
					</select>
					<span class="text-danger"> @{{ (error_messages['extra_beds.'+key+'.plan']) ? error_messages['extra_beds.'+key+'.plan'][0] : '' }} </span>
				</div>
				<div class="col-md-3" :class="{'required-input' : extra_bed.price <= 0}">
					<div class="input-group">
						<input type="number" :name="'extra_beds['+key+'][price]'" class="form-control" v-model="extra_bed.price" placeholder="@lang('admin_messages.price')">
						<div class="input-group-append">
							<span class="input-group-text">{{ session('currency') }}</span>
						</div>
					</div>
					<span class="text-danger"> @{{ (error_messages['extra_beds.'+key+'.price']) ? error_messages['extra_beds.'+key+'.price'][0] : '' }} </span>
				</div>
				<div class="col-md-3" :class="{'required-input' : extra_bed.size <= 0}">
					<div class="input-group">
						<input type="text" :name="'extra_beds['+key+'][size]'" class="form-control" v-model="extra_bed.size" placeholder="@lang('admin_messages.bed_size')">
					</div>
					<span class="text-danger"> @{{ (error_messages['extra_beds.'+key+'.size']) ? error_messages['extra_beds.'+key+'.size'][0] : '' }} </span>
				</div>
				<div class="col-md-2" :class="{'required-input' : extra_bed.guest_type <= 0}">
					<div class="input-group">
						<select  :name="'extra_beds['+key+'][guest_type]'" class="form-select" v-model="extra_bed.guest_type">
							<option value="adult">@lang('admin_messages.adult')</option>
							<option value="children">@lang('admin_messages.children')</option>
						</select>
					</div>
					<span class="text-danger"> @{{ (error_messages['extra_beds.'+key+'.guest_type']) ? error_messages['extra_beds.'+key+'.guest_type'][0] : '' }} </span>
				</div>
				<div class="col-md-1">
					<button class="btn btn-icon btn-sm btn-danger" v-on:click="removeExtraBed(key)">
						<span class="fas fa-trash-alt"></span>
					</button>
				</div>
			</div>
			<div class="d-flex" >
				<a href="javascript:void(0)" class="btn btn-info btn-round text-white" v-on:click="addExtraBed();" v-show="room.extra_beds.length != bed_types.length">
					<span class="fa fa-plus"></span> <span>@lang('messages.add')</span>
				</a>
			</div>
		</div>
	</div>
</div>
@endsection
@section('room_status')
<div class="content-section">
	<div class="row">
		<label class="col-md-3">@lang('admin_messages.room_status') <em class="text-danger"> *</em></label>
		<div class="col-md-9">
			<select name="status" class="form-select" id="status" v-model="room.status">
				<option value="In Progress" selected v-if="room.status == 'In Progress'">@lang('admin_messages.in_progress')</option>
				<option value="Listed" :disabled="checkStepsCompleted">@lang('admin_messages.listed')</option>
				<option value="Unlisted" :disabled="checkStepsCompleted">@lang('admin_messages.unlisted')</option>
			</select>
			<span class="text-danger"> @{{ (error_messages.status) ? error_messages.status[0] : '' }} </span>
		</div>
	</div>
</div>
@endsection
@section('calendar')
<div class="content-section">
	<div class="table-responsive no-transition">
		<div id="full_calendar"></div>
	</div>
</div>
@endsection
@section('promotions')
<div class="content-section">
	<div class="row p-2 mt-2">
		<ul class="nav nav-pills nav-info nav-fill col-md-8" role="tablist">
			<li class="nav-item col-3">
				<a class="nav-link active" id="early_bird-tab" data-bs-toggle="pill" href="#early_bird" role="tab">@lang('admin_messages.early_bird')</a>
			</li>
			<li class="nav-item col-3">
				<a class="nav-link" id="min_max-tab" data-bs-toggle="pill" href="#min_max" role="tab">@lang('messages.minimum_stay')</a>
			</li>
			<li class="nav-item col-3">
				<a class="nav-link" id="day_before_checkin-tab" data-bs-toggle="pill" href="#day_before_checkin" role="tab">@lang('admin_messages.day_before_checkin')</a>
			</li>
		</ul>
		<div class="tab-content mt-2 mb-3">
			<div class="tab-pane fade show active" id="early_bird" role="tabpanel">
				<div class="m-4" v-for="(early_bird,index1) in hotel_room_promotions.early_bird">
					<div class="line-divided border mb-4" v-show="index1 > 0"></div>
					<div class="my-2 justify-content-between d-flex">
						<p class="h3">
							#@{{index1 + 1}}
							<span class="fw-bold">
								<span class="px-2" v-if="typeof early_bird.name == 'string' && early_bird.name != ''"> @{{early_bird.name}} </span>
								<span v-else>@lang('admin_messages.early_bird')</span>
							</span>
							{{--
							<span class="ms-2">
								<span class="badge badge-success" v-if="early_bird.status == 1">@lang('admin_messages.active')</span>
								<span class="badge badge-danger" v-else>@lang('admin_messages.inactive')</span>
							</span>
							--}}
						</p>
						<div class="d-flex">
							{{--
							<a type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" :href="'#earlyBirdColapse_'+index1">@lang('admin_messages.edit')</a>
							--}}
							<button type="button" class="btn btn-danger btn-sm btn-icon ms-2" v-on:click="removePromotion('early_bird',index1)"><span class="fa fa-trash-alt"></span></button>
						</div>
					</div>
					<div class="collapse show p-3" :id="'earlyBirdColapse_'+index1">
						<input type="hidden" :name="'promotions[early_bird]['+index1+'][id]'" class="form-control" v-model="early_bird.id">
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.promotion_title')</label>
							<div class="col-md-9">
								<input type="text" :name="'promotions[early_bird]['+index1+'][name]'" class="form-control" v-model="early_bird.name" placeholder="{{ Lang::get('admin_messages.your_promotion_name_own_tracking') }}">
							</div>
						</div>
						<div class="form-group row">
							<label class="form-label col-md-3">@lang('admin_messages.discount_type')</label>
							<div class="col-md-9">
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[early_bird]['+index1+'][value_type]'" value="fixed" v-model="early_bird.value_type">
										@lang('admin_messages.fixed')
									</label>
								</div>
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[early_bird]['+index1+'][value_type]'" value="percentage" v-model="early_bird.value_type">
										@lang('admin_messages.percentage')
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.discount_value') <em class="text-danger"> *</em></label>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-prepend" v-if="early_bird.value_type == 'percentage'">
										<span class="input-group-text"> %  </span>
									</div>
									<input class="form-control" :name="'promotions[early_bird]['+index1+'][value]'" type="number" placeholder="{{ Lang::get('admin_messages.value') }}" v-model="early_bird.value">
									<div class="input-group-append" v-if="early_bird.value_type == 'fixed'">
										<span class="input-group-text"> {{ session('currency') }} </span>
									</div>
								</div>
								<span class="text-danger">  @{{ (error_messages['promotions.early_bird.'+index1+'.value']) ? error_messages['promotions.early_bird.'+index1+'.value'][0] : '' }}  </span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.guest_must_book') <em class="text-danger"> *</em> </label>
							<div class="col-md-9">
								<div class="input-group">
									<select class="form-control" :name="'promotions[early_bird]['+index1+'][days]'" v-model="early_bird.days">
										@foreach($day_array as $key => $day)
										<option value="{{ $key }}" v-show="{{ $key }} == early_bird.days || canDisplayEarlyBird('early_bird',{{$key}})"> {{ $day }} </option>
										@endforeach
									</select>
									<div class="input-group-append">
										<span class="input-group-text">@lang('admin_messages.days') </span>
									</div>
								</div>
								<span class="text-danger"> @{{ (error_messages['promotions.early_bird.'+index1+'.days']) ? error_messages['promotions.early_bird.'+index1+'.days'][0] : '' }}  </span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.status')</label>
							<div class="col-md-9">
								<select :name="'promotions[early_bird]['+index1+'][status]'" class="form-select" v-model="early_bird.status">
									<option value="1" >@lang('admin_messages.active')</option>
									<option value="0">@lang('admin_messages.inactive')</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<button type="button" class="btn btn-default btn-round float-end" v-on:click="addPromotion('early_bird')" v-if="hotel_room_promotions.early_bird.length == 0">
					<span class="fa fa-plus"></span> 
					@lang('admin_messages.add_new')
				</button>
			</div>
			<div class="tab-pane fade" id="min_max" role="tabpanel">
				<div class="m-4" v-for="(min_max,index2) in hotel_room_promotions.min_max">
					<div class="line-divided border mb-4" v-show="index2 > 0"></div>
					<div class="my-2 justify-content-between d-flex">
						<p class="h3">
							#@{{index2 + 1}}
							<span class="fw-bold">
								<span class="px-2" v-if="typeof min_max.name == 'string' && min_max.name != ''">
									@{{min_max.name}} 
								</span>
								<span v-else>@lang('messages.minimum_stay')</span>
							</span>
							{{--
							<span class="ms-2">
								<span class="badge badge-success" v-if="min_max.status == 1">@lang('admin_messages.active')</span>
								<span class="badge badge-danger" v-else>@lang('admin_messages.inactive')</span>
							</span>
							--}}
						</p>
						<div class="d-flex">
							{{--
							<a type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" :href="'#minMaxColapse_'+index2">@lang('admin_messages.edit')</a>
							--}}
							<button type="button" class="btn btn-danger btn-sm btn-icon ms-2" v-on:click="removePromotion('min_max',index2)"><span class="fa fa-trash-alt"></span></button>
						</div>
					</div>
					<div class="collapse show p-3" :id="'minMaxColapse_'+index2">
						<input type="hidden" :name="'promotions[min_max]['+index2+'][id]'" class="form-control" v-model="min_max.id">
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.promotion_title')</label>
							<div class="col-md-9">
								<input type="text" :name="'promotions[min_max]['+index2+'][name]'" class="form-control" v-model="min_max.name" placeholder="{{ Lang::get('admin_messages.your_promotion_name_own_tracking') }}">
							</div>
						</div>
						<div class="form-group row">
							<label class="form-label col-md-3">@lang('admin_messages.discount_type')</label>
							<div class="col-md-9">
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[min_max]['+index2+'][value_type]'" value="fixed" v-model="min_max.value_type">
										@lang('admin_messages.fixed')
									</label>
								</div>
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[min_max]['+index2+'][value_type]'" value="percentage" v-model="min_max.value_type">
										@lang('admin_messages.percentage')
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.discount_value') <em class="text-danger"> *</em></label>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-prepend" v-if="min_max.value_type == 'percentage'">
										<span class="input-group-text"> % </span>
									</div>
									<input class="form-control" :name="'promotions[min_max]['+index2+'][value]'" type="number" placeholder="{{ Lang::get('admin_messages.value') }}" v-model="min_max.value">
									<div class="input-group-append" v-if="min_max.value_type == 'fixed'">
										<span class="input-group-text"> {{ session('currency') }} </span>
									</div>
								</div>
								<span class="text-danger">  @{{ (error_messages['promotions.min_max.'+index2+'.value']) ? error_messages['promotions.min_max.'+index2+'.value'][0] : '' }}  </span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('messages.minimum_stay') <em class="text-danger"> *</em></label>
							<div class="col-md-9">
								<input type="number" :name="'promotions[min_max]['+index2+'][min_los]'" class="form-control" placeholder="{{ Lang::get('messages.min_los') }}" v-model="min_max.min_los">
								<span class="text-danger">  @{{ (error_messages['promotions.min_max.'+index2+'.min_los']) ? error_messages['promotions.min_max.'+index2+'.min_los'][0] : '' }}  </span>
							</div>
						</div>
						{{--<div class="form-group row">
							<label class="col-md-3">@lang('messages.max_los') <em class="text-danger"> *</em></label>
							<div class="col-md-9">
								<input type="number" :name="'promotions[min_max]['+index2+'][max_los]'" class="form-control" placeholder="{{ Lang::get('messages.max_los') }}" v-model="min_max.max_los">
								<span class="text-danger">  @{{ (error_messages['promotions.min_max.'+index2+'.max_los']) ? error_messages['promotions.min_max.'+index2+'.max_los'][0] : '' }}  </span>
							</div>
						</div>--}}
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.status')</label>
							<div class="col-md-9">
								<select :name="'promotions[min_max]['+index2+'][status]'" class="form-select" v-model="min_max.status">
									<option value="1" >@lang('admin_messages.active')</option>
									<option value="0">@lang('admin_messages.inactive')</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<button type="button" class="btn btn-default btn-round float-end" v-on:click="addPromotion('min_max')" v-if="hotel_room_promotions.min_max.length == 0">
					<span class="fa fa-plus"></span> 
					@lang('admin_messages.add_new')
				</button>
			</div>
			<div class="tab-pane fade" id="day_before_checkin" role="tabpanel">
				<div class="m-4" v-for="(day_before_checkin,index3) in hotel_room_promotions.day_before_checkin">
					<div class="line-divided border mb-4" v-show="index3 > 0"></div>
					<div class="my-2 justify-content-between d-flex">
						<p class="h3">
							#@{{index3 + 1}}
							<span class="fw-bold">
								<span class="px-2" v-if="typeof day_before_checkin.name == 'string' && day_before_checkin.name != ''">
									@{{day_before_checkin.name}}
								</span>
								<span v-else>@lang('admin_messages.day_before_checkin')</span>
							</span>
							{{--
							<span class="ms-2">
								<span class="badge badge-success" v-if="day_before_checkin.status == 1">@lang('admin_messages.active')</span>
								<span class="badge badge-danger" v-else>@lang('admin_messages.inactive')</span>
							</span>
							--}}
						</p>
						<div class="d-flex">
							{{--
							<a type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" :href="'#dayBeforeCheckinColapse_'+index3">@lang('admin_messages.edit')</a>
							--}}
							<button type="button" class="btn btn-danger btn-sm btn-icon ms-2" v-on:click="removePromotion('day_before_checkin',index3)"><span class="fa fa-trash-alt"></span></button>
						</div>
					</div>
					<div class="collapse show p-3" :id="'dayBeforeCheckinColapse_'+index3">
						<input type="hidden" :name="'promotions[day_before_checkin]['+index3+'][id]'" class="form-control" v-model="day_before_checkin.id">
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.promotion_title')</label>
							<div class="col-md-9">
								<input type="text" :name="'promotions[day_before_checkin]['+index3+'][name]'" class="form-control" v-model="day_before_checkin.name" placeholder="{{ Lang::get('admin_messages.your_promotion_name_own_tracking') }}">
							</div>
						</div>
						<div class="form-group row">
							<label class="form-label col-md-3">@lang('admin_messages.discount_type')</label>
							<div class="col-md-9">
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[day_before_checkin]['+index3+'][value_type]'" value="fixed" v-model="day_before_checkin.value_type">
										@lang('admin_messages.fixed')
									</label>
								</div>
								<div class="form-check-inline">
									<label class="form-check-label"> 
										<input class="form-check-input" type="radio" :name="'promotions[day_before_checkin]['+index3+'][value_type]'" value="percentage" v-model="day_before_checkin.value_type">
										@lang('admin_messages.percentage')
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.discount_value') <em class="text-danger"> *</em></label>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-prepend" v-if="day_before_checkin.value_type == 'percentage'">
										<span class="input-group-text"> % </span>
									</div>
									<input class="form-control" :name="'promotions[day_before_checkin]['+index3+'][value]'" type="number" placeholder="{{ Lang::get('admin_messages.value') }}" v-model="day_before_checkin.value">
									<div class="input-group-append" v-if="day_before_checkin.value_type == 'fixed'">
										<span class="input-group-text"> {{ session('currency') }} </span>
									</div>
								</div>
								<span class="text-danger">  @{{ (error_messages['promotions.day_before_checkin.'+index3+'.value']) ? error_messages['promotions.day_before_checkin.'+index3+'.value'][0] : '' }}  </span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.guest_must_book') <em class="text-danger"> *</em> </label>
							<div class="col-md-9">
								<div class="input-group">
									<input class="form-control" :name="'promotions[day_before_checkin]['+index3+'][days]'" type="text" placeholder="{{ Lang::get('admin_messages.days') }}" v-model="day_before_checkin.days">
									<div class="input-group-append">
										<span class="input-group-text">@lang('admin_messages.days') </span>
									</div>
								</div>
								<p class="small m-0 fst-italic">Min: 1 @lang('admin_messages.days')</p>
								<span class="text-danger"> @{{ (error_messages['promotions.day_before_checkin.'+index3+'.days']) ? error_messages['promotions.day_before_checkin.'+index3+'.days'][0] : '' }}  </span>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3">@lang('admin_messages.status')</label>
							<div class="col-md-9">
								<select :name="'promotions[day_before_checkin]['+index3+'][status]'" class="form-select" v-model="day_before_checkin.status">
									<option value="1" >@lang('admin_messages.active')</option>
									<option value="0">@lang('admin_messages.inactive')</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<button type="button" class="btn btn-default btn-round float-end" v-on:click="addPromotion('day_before_checkin')" v-if="hotel_room_promotions.day_before_checkin.length == 0">
					<span class="fa fa-plus"></span> 
					@lang('admin_messages.add_new')
				</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('payment_method')
<div class="content-section">
	<div class="form-group row" :class="{'required-input': room.payment_method == ''}">
		<label for="status" class="col-sm-3 col-form-label"> @lang('messages.payment_method') <em class="text-danger">*</em></label>
		<div class="col-sm-9">
			<input type="hidden" name="payment_method" v-model="room.payment_method">
			@foreach(PAYMENT_METHODS as $payment_method)
				<label class="mx-3"> 
					<input type="checkbox" value="{{ $payment_method['key'] }}" class="payment_method form-check-input me-2" :checked="{{ $selected_payment_methods->where('key',$payment_method['key'])->count() }}">
					{{ $payment_method['value'] }}
				</label>
			@endforeach
			<p class="text-danger"> @{{ (error_messages.payment_method) ? error_messages.payment_method[0] : '' }} </p>
		</div>
	</div>
</div>
@endsection