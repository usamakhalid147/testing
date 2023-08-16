<h4 class="fw-bold"> @lang('messages.manager_details') </h4>
@if($result->id != '')
<div class="form-group">
    <label for="id">@lang('admin_messages.user_id')<em class="text-danger">*</em></label>
    {!! Form::text('id',$result->id,['class'=>'form-control','id'=>'id','readonly' => true])!!}
    <span class="text-danger">{{$errors->first('id')}}</span>
</div>
@endif
<div class="form-group">
    <label for="full_name">@lang('admin_messages.full_name')<em class="text-danger">*</em></label>
    {!! Form::text('full_name',old('full_name',$result->full_name),['class'=>'form-control','id'=>'full_name'])!!}
    <span class="text-danger">{{$errors->first('full_name')}}</span>
</div>
<div class="form-group">
    <label for="title"> @lang('admin_messages.manager') @lang('admin_messages.title') <em class="text-danger"> * </em> </label>
    {!! Form::text('title',old('title',$result->title), ['class' => 'form-control', 'id' => 'title',  'placeholder' => Lang::get('admin_messages.title')]) !!}
    <span class="text-danger">{{ $errors->first('title') }}</span>
</div>
<div class="form-group">
    <label for="email"> @lang('admin_messages.manager') @lang('messages.email') <em class="text-danger"> * </em> </label>
    {!! Form::text('email',old('email',$result->email), ['class' => 'form-control', 'id' => 'email']) !!}
    <span class="text-danger">{{ $errors->first('email') }}</span>
</div>
<div class="form-group">
    <label for="password"> @lang('admin_messages.password') <em class="text-danger"> * </em> </label>
    {!! Form::text('password', '', ['class' => 'form-control', 'id' => 'password']) !!}
    <span class="text-danger">{{ $errors->first('password') }}</span>
</div>
<div class="form-group">
    <label for="number">@lang('admin_messages.telephone_number')</label>
    {!!Form::text('telephone_number',old('number',$result->telephone_number),['class'=>'form-control','id'=>'number','placeholder'=>Lang::get('admin_messages.telephone_number')]) !!}
    <span class="text-danger">{{$errors->first('telephone_number')}}</span>
</div>
<div class="form-group">
    <label for="number">@lang('admin_messages.manager_mobile_number')<em class="text-danger">*</em></label>
    {!!Form::text('phone_number',old('phone_number',$result->phone_number),['class'=>'form-control','id'=>'phone_number','placeholder'=>Lang::get('admin_messages.manager_mobile_number')])!!}
    <span class="text-danger">{{$errors->first('phone_number')}}</span>
</div>
<div class="form-group">
    <label for="country_code"> @lang('admin_messages.country_code') <em class="text-danger"> *</em></label>
    {!! Form::select('country_code', $countries ,old('country_code',$result->country_code), ['class' => 'form-select', 'id' => 'country_code','placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_country', 'v-on:change' => "selected_city = ''"]) !!}
    <span class="text-danger">{{ $errors->first('country_code')}}</span>
</div>
<div class="form-group">
    <label for="city"> @lang('admin_messages.province') <em class="text-danger"> *</em></label>
    <select name="city" class="form-control" v-model="selected_city">
        <option value="">@lang('messages.select')</option>
        <option :value="city.name" v-for="city in cities" v-show="city.country == selected_country">@{{city.name}}</option>
    </select>
    <span class="text-danger">{{ $errors->first('city')}}</span>
</div>
<div class="form-group">
    <label for="dob"> @lang('admin_messages.dob') </label>
    {!! Form::text('dob', optional($result->user_information)->dob, ['class' => 'form-control', 'id' => 'dob']) !!}
    <span class="text-danger">{{ $errors->first('dob') }}</span>
</div>
<div class="form-group">
    <label for="gender"> @lang('admin_messages.gender') </label>
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
<hr>
<h4 class="fw-bold"> @lang('messages.company_details') </h4>
@if($result->id != '')
<div class="form-group">
    <label for="company_id"> @lang('admin_messages.company_id')</label>
    {!! Form::text('company_id',optional($result->company)->id,['class'=>'form-control','id'=>'id','readonly'=>'true'])!!}
    <span class="text-danger">{{$errors->first('user_id')}}</span>
</div>
@endif
<div class="form-group">
    <label for="company_name">@lang('admin_messages.company_name')</label>
    {!!Form::text('company_name',old('company_name',optional($result->company)->company_name),['class'=>'form-control','id'=>'company_name'])!!}
    <span class="text-danger">{{$errors->first('company_name')}}</span>
</div>
<div class="form-group">
    <label for="company_tax_number">@lang('admin_messages.company_tax_number')</label>
    {!!Form::text('company_tax_number',optional($result->company)->company_tax_number,['class'=>'form-control','id'=>'company_tax_number'])!!}
    <span class="text-danger">{{$errors->first('company_tax_number')}}</span>
</div>
<div class="form-group">
    <label for="company_tele_phone_number">@lang('admin_messages.company_tele_phone_number')</label>
    {!!Form::text('company_tele_phone_number',optional($result->company)->company_tele_phone_number,['class'=>'form-control','id'=>'company_tele_phone_number'])!!}
    <span class="text-danger">{{$errors->first('company_tele_phone_number')}}</span>
</div>
<div class="form-group">
    <label for="company_fax_number">@lang('admin_messages.company_fax_number')</label>
    {!!Form::text('company_fax_number',optional($result->company)->company_fax_number,['class'=>'form-control','id'=>'company_fax_number'])!!}
    <span class="text-danger">{{$errors->first('company_fax_number')}}</span>
</div>
<div class="form-group">
    <label for="company_address">@lang('admin_messages.company_address')</label>
    {!!Form::text('address_line_1',optional($result->company)->address_line_1,['class'=>'form-control','id'=>'address_line_1'])!!}
    <span class="text-danger">{{$errors->first('address_line_1')}}</span>
</div>
<div class="form-group">
    <label for="ward">@lang('admin_messages.company_ward')</label>
    {!!Form::text('address_line_2',optional($result->company)->address_line_2,['class'=>'form-control','id'=>'ward'])!!}
    <span class="text-danger">{{$errors->first('address_line_2')}}</span>
</div>
<div class="form-group">
    <label for="state">@lang('admin_messages.cities')</label>
    {!!Form::text('state',optional($result->company)->state,['class'=>'form-control','id'=>'state'])!!}
    <span class="text-danger">{{$errors->first('state')}}</span>
</div>
<div class="form-group">
    <label for="country_code"> @lang('admin_messages.company_country_code') </label>
    {!! Form::select('company_country_code', $countries ,null, ['class' => 'form-select', 'id' => 'country_code','placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_company_country', 'v-on:change' => "selected_company_city = ''"]) !!}
    <span class="text-danger">{{ $errors->first('company_country_code')}}</span>
</div>
<div class="form-group">
    <label for="city"> @lang('admin_messages.province') </label>
    <select name="company_city" class="form-control" v-model="selected_company_city">
        <option value="">@lang('messages.select')</option>
        <option :value="city.name" v-for="city in cities" v-show="city.country == selected_company_country">@{{city.name}}</option>
    </select>
    <span class="text-danger">{{ $errors->first('company_city') }}</span>
</div>
<div class="form-group">
    <label for="postal_code">@lang('admin_messages.postal_code')</label>
    {!!Form::text('postal_code',optional($result->company)->postal_code,['class'=>'form-control','id'=>'postal_code'])!!}
    <span class="text-danger">{{$errors->first('postal_code')}}</span>
</div>
<div class="form-group">
    <label for="company_website">@lang('admin_messages.company_website')</label>
    {!!Form::text('company_website',optional($result->company)->company_website,['class'=>'form-control','id'=>'company_website'])!!}
    <span class="text-danger">{{$errors->first('company_website')}}</span>
</div>
<div class="form-group">
    <label for="company_email">@lang('admin_messages.company_email')</label>
    {!!Form::text('company_email',optional($result->company)->company_email,['class'=>'form-control','id'=>'company_email'])!!}
    <span class="text-danger">{{$errors->first('company_email')}}</span>
</div>
@push('scripts')
<script type="text/javascript">
    window.vueInitData = {!! json_encode([
        'verification_status' => old('verification_status',$result->verification_status),
        'cities' => $city_list,
        'selected_country' => old('country_code',$result->country_code ?? ''),
        'selected_city' => old('city',$result->city ?? ''),
        'selected_company_country' => old('company_country_code',optional($result->company)->country_code),
        'selected_company_city' => old('company_city',optional($result->company)->city),
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