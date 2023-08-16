@if($result->id != '')
<div class="form-group">
    <label for="id"> @lang('admin_messages.user_id')</label>
    {!! Form::text('id',$result->id,['class'=>'form-control','id'=>'id','readonly'=>'true'])!!}
    <span class="text-danger">{{$errors->first('user_id')}}</span>
</div>
@endif
<div class="form-group">
    <label for="full_name"> @lang('admin_messages.full_name') <em class="text-danger"> * </em> </label>
    {!! Form::text('full_name', $result->full_name, ['class' => 'form-control', 'id' => 'full_name']) !!}
    <span class="text-danger">{{ $errors->first('full_name') }}</span>
</div>
<div class="form-group">
    <label for="email"> @lang('admin_messages.user') @lang('admin_messages.email') <em class="text-danger"> * </em> </label>
    {!! Form::text('email', $result->email, ['class' => 'form-control', 'id' => 'email']) !!}
    <span class="text-danger">{{ $errors->first('email') }}</span>
</div>
<div class="form-group">
    <label for="password"> @lang('admin_messages.password') <em class="text-danger"> * </em> </label>
    {!! Form::text('password', '', ['class' => 'form-control', 'id' => 'password']) !!}
    <span class="text-danger">{{ $errors->first('password') }}</span>
</div>
<div class="form-group">
    <label for="phone_number">@lang('admin_messages.user')
        @lang('admin_messages.mobile_number')<em class="text-danger">*</em></label>
        {!!Form::text('phone_number',$result->phone_number,['class'=>'form-control','id'=>'phone_number'])!!}
        <span class="text-danger">{{$errors->first('phone_number')}}</span>
</div>
<div class="form-group">
    <label for="address_line_1">@lang('admin_messages.home_address')</label>
    {!!Form::text('address_line_1',optional($result->user_information)->address_line_1,['class'=>'form-control','id'=>'address_line_1'])!!}
    <span class="text-danger">{{$errors->first('address_line_1')}}</span>
</div>
<div class="form-group">
    <label for="address_line_2">@lang('admin_messages.ward')</label>
    {!!Form::text('address_line_2',optional($result->user_information)->address_line_2,['class'=>'form-control','id'=>'address_line_2'])!!}<span class="text-danger">{{$errors->first('address_line_2')}}</span>
</div>
<div class="form-group">
    <label for="country_code"> @lang('admin_messages.country_code') <em class="text-danger"> *</em></label>
    {!! Form::select('country_code', $countries ,$result->country_code, ['class' => 'form-select', 'id' => 'country_code','placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_country', 'v-on:change' => "selected_city = ''"]) !!}
    <span class="text-danger">{{ $errors->first('country_code')}}</span>
</div>
<div class="form-group">
    <label for="city"> @lang('messages.city_desc') <em class="text-danger"> *</em></label>
    <select name="city" class="form-control" v-model="selected_city">
        <option value="">@lang('messages.select')</option>
        <option :value="city.name" v-for="city in cities" v-show="city.country == selected_country">@{{city.name}}</option>
    </select>
    <span class="text-danger">{{ $errors->first('city')}}</span>
</div>
<div class="form-group">
    <label for="state">@lang('admin_messages.cities')</label>
    {!!Form::text('state',optional($result->user_information)->state,['class'=>'form-control','id'=>'state'])!!}<span class="text-danger">{{$errors->first('state')}}</span>
</div>
<div class="form-group">
    <label for="postal_code">@lang('admin_messages.postal_code')</label>
    {!!Form::text('postal_code',optional($result->user_information)->postal_code,['class'=>'form-control','id'=>'postal_code'])!!}<span class="text-danger">{{$errors->first('postal_code')}}</span>
</div>
<div class="form-group">
    <label for="dob"> @lang('admin_messages.dob') </label>
    {!! Form::text('dob', optional($result->user_information)->dob, ['class' => 'form-control', 'id' => 'dob']) !!}
    <span class="text-danger">{{ $errors->first('dob') }}</span>
</div>
<div class="form-group">
    <label for="gender"> @lang('admin_messages.gender')</label>
    {!! Form::select('gender', ["Male"=> Lang::get('messages.male'),"Female"=> Lang::get('messages.female'),"Other"=> Lang::get('messages.others')],optional($result->user_information)->gender, ['class' => 'form-select', 'id' => 'gender']) !!}
</div>
<div class="form-group">
    <label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
    {!! Form::select('status', array('active' => 'Active', 'inactive' => 'Inactive'), $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get('admin_messages.select')]) !!}
    <span class="text-danger">{{ $errors->first('status') }}</span>
</div>
@if($result->id != '' && $result->verification_status != 'no')
<div class="form-group">
    <label for="verification_status"> @lang('admin_messages.verification_status') </label>
    {!! Form::select('verification_status', array('pending' => 'Pending', 'verified' => 'Verified', 'resubmit' => 'Resubmit'), $result->verification_status, ['class' => 'form-select', 'id' => 'verification_status','v-model' => 'verification_status']) !!}
    <span class="text-danger"> {{ $errors->first('verification_status') }} </span>
</div>
<div class="form-group" v-show="verification_status == 'resubmit'">
    <label for="resubmit_reason"> @lang('admin_messages.resubmit_reason') </label>
    {!! Form::textarea('resubmit_reason',$result->resubmit_reason, ['class' => 'form-control', 'id' => 'resubmit_reason','rows' => '3']) !!}
    <span class="text-danger"> {{ $errors->first('id_resubmit_reason') }} </span>
</div>
<div class="form-group">
    <label for="verification_document"> @lang('admin_messages.verification_document') </label>
    <div class="row">
        <div class="form-group input-file input-file-image">
            <img class="img-upload-preview" src="{{ $result->user_document_src }}">
        </div>
    </div>
    <span class="text-danger"> {{ $errors->first('verification_status') }} </span>
</div>
@endif
@if($result->id != '')
<div class="form-group">
    <label for="created_at"> @lang('admin_messages.created_at')</label>
    {!! Form::text('created_at',$result->formatted_created_at,['class'=>'form-control','id'=>'id','readonly'=>'true'])!!}
    <span class="text-danger">{{$errors->first('user_id')}}</span>
</div>
@endif
@push('scripts')
<script type="text/javascript">
    window.vueInitData = {!! json_encode([
        'verification_status' => old('verification_status',$result->verification_status),
        'cities' => $city_list,
        'selected_country' => $result->country_code ?? '',
        'selected_city' => $result->city ?? '',
    ]) !!}
    $(document).ready(function() {
        var flatpickrOptions = {
            altInput: true,
            maxDate: 'today',
            altFormat: flatpickrFormat,
            dateFormat: "Y-m-d",
        };

        flatpickr('#dob', flatpickrOptions);
    });
</script>
@endpush