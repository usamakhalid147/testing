@extends('layouts.hostLayout.app')
@section('content')
<div class="content">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title"> @lang("admin_messages.manager_profile") </h4>
            <ul class="breadcrumbs">
                <li class="nav-home">
                    <a href="{{ route('host.dashboard') }}">
                        <i class="flaticon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="flaticon-right-arrow"></i>
                </li>
                <li class="nav-item">
                    <a href="#">@lang("admin_messages.edit")</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(['url' => route('host.update',['id' => $result->id]), 'class' => 'form-horizontal','method' => "PUT",'files' => true]) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="full_name">@lang('admin_messages.full_name')<em class="text-danger">*</em></label>
                                        {!! Form::text('full_name',old('full_name',$result->full_name),['class'=>'form-control','id'=>'full_name','disabled' => 'disabled'])!!}
                                        <span class="text-danger">{{$errors->first('full_name')}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="title"> @lang('admin_messages.manager') @lang('admin_messages.title') <em class="text-danger"> * </em> </label>
                                        {!! Form::text('title',old('title',$result->title), ['class' => 'form-control', 'id' => 'title',  'placeholder' => Lang::get('admin_messages.title'), 'disabled' => 'disabled']) !!}
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email"> @lang('admin_messages.manager') @lang('messages.email') <em class="text-danger"> * </em> </label>
                                        {!! Form::text('email',old('email',$result->email), ['class' => 'form-control', 'id' => 'email', 'disabled' => 'disabled']) !!}
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="password"> @lang('admin_messages.password') <em class="text-danger"> * </em> </label>
                                        {!! Form::text('password', '', ['class' => 'form-control', 'id' => 'password']) !!}
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="number">@lang('admin_messages.telephone_number')</label>
                                        {!! Form::text('telephone_number', old('telephone_number', $result->telephone_number), [
                                            'class' => 'form-control',
                                            'id' => 'number',
                                            'placeholder' => Lang::get('admin_messages.telephone_number'),
                                            'disabled' => isset($result->telephone_number) ? 'disabled' : null
                                        ]) !!}
                                        <span class="text-danger">{{ $errors->first('telephone_number') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="number">@lang('admin_messages.manager_mobile_number')<em class="text-danger">*</em></label>
                                        {!!Form::text('phone_number',old('phone_number',$result->phone_number),['class'=>'form-control','id'=>'phone_number','placeholder'=>Lang::get('admin_messages.manager_mobile_number'),'disabled' => 'disabled'])!!}
                                        <span class="text-danger">{{$errors->first('phone_number')}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="country_code"> @lang('admin_messages.country_code') <em class="text-danger"> *</em></label>
                                        {!! Form::select('country_code', $countries, old('country_code', $result->country_code), ['class' => 'form-select', 'id' => 'country_code', 'placeholder' => Lang::get('admin_messages.select'), 'v-model' => 'selected_country', 'v-on:change' => "selected_city = ''", 'disabled' => 'disabled']) !!}
                                        <span class="text-danger">{{ $errors->first('country_code')}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="city"> @lang('admin_messages.province') <em class="text-danger"> *</em></label>
                                        <select name="city" class="form-control" v-model="selected_city" disabled>
                                            <option value="">@lang('messages.select')</option>
                                            <option :value="city.name" v-for="city in cities" v-show="city.country == selected_country">@{{city.name}}</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('city')}}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="dob">@lang('admin_messages.dob')</label>
                                        {!! Form::text('dob', optional($result->user_information)->dob, [
                                            'class' => 'form-control ',
                                            'id' => 'dob',
                                            'disabled' => $result->user_information->dateOfBirth !== '-'
                                        ]) !!}
                                        <span class="text-danger">{{ $errors->first('dob') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender">@lang('admin_messages.gender')</label>
                                        {!! Form::select('gender', [null=>"Select","Male"=> Lang::get('messages.male'),"Female"=> Lang::get('messages.female'),"Other"=> Lang::get('messages.others')], old('gender', optional($result->user_information)->gender), [
                                            'class' => 'form-select',
                                            'id' => 'gender',
                                            'disabled' => old('gender', optional($result->user_information)->gender) !== null ? 'disabled' : null
                                        ]) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="status"> @lang('admin_messages.status') <em class="text-danger"> * </em> </label>
                                        {!! Form::select('status', array('active' => 'Active', 'inactive' => 'Inactive'), $result->status, ['class' => 'form-select', 'id' => 'status', 'placeholder' => Lang::get('admin_messages.select'),'disabled' => 'disabled','disabled' => true]) !!}
                                        <span class="text-danger">{{ $errors->first('status') }}</span>
                                    </div>
                                    @if($result->id != '')
                                    <div class="form-group">
                                        <label for="created_at"> @lang('admin_messages.created_at')</label>
                                        {!! Form::text('created_at',$result->formatted_created_at,['class'=>'form-control','id'=>'id','readonly'=>'true'])!!}
                                        <span class="text-danger">{{$errors->first('user_id')}}</span>
                                    </div>
                                    @endif
                                    @if($result->id != '')
                                    <div class="form-group input-file input-file-image">
                                        <div class="img-button-div">
                                            <img class="img-upload-preview" src="{{ $result->profile_picture_src }}">
                                            @if($result->src != '')
                                                <button id="remove-photo-btn" class="btn btn-danger btn-rounded mb-4" type="button">@lang('admin_messages.remove_photo')</button>
                                            @endif
                                        </div>
                                        <input type="file" class="form-control form-control-file" id="profile_picture" name="profile_picture" accept="image/*" onchange="validateFileSize(this)">
                                        <label for="profile_picture" class="label-input-file btn btn-default btn-round">
                                            <span class="btn-label"><i class="fa fa-file-image"></i></span>
                                            @lang('admin_messages.choose_file')
                                        </label>
                                        <span class="text-danger d-block">{{ $errors->first('image') }}</span>
                                    </div>
                                    @else
                                    <div class="form-group input-file input-file-image">
                                        <img class="img-upload-preview">
                                        <input type="file" class="form-control form-control-file" id="profile_picture" name="profile_picture" accept="image/*" onchange="validateFileSize(this)">
                                        <label for="profile_picture" class="label-input-file btn btn-default btn-round">
                                            <span class="btn-label"><i class="fa fa-file-image"></i></span>
                                            @lang('admin_messages.choose_file')
                                        </label>
                                        <span class="text-danger d-block">{{ $errors->first('image') }}</span>
                                    </div>
                                    @endif
                                    <div class="form-group mt-0 pt-0">
										<small class="text-muted">Max upload size for logo image is 1mb</small>
                                    </div>
                                    <div class="card-footer px-2 pt-4">
                                        <button type="submit" class="btn btn-primary float-end" id="add"> @lang('admin_messages.submit') </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    $(document).ready(function() {
        var dob = $('#dob');
        var dobValue = dob.val();
        var flatpickrOptions = {
            altInput: true,
            maxDate: 'today',
            altFormat: flatpickrFormat,
            dateFormat: "Y-m-d",
            allowInput: true, // enable manual input
        };
        flatpickr(dob[0], flatpickrOptions);
    });
    window.vueInitData = {!! json_encode([
            'cities' => $city_list,
            'selected_country' => $result->country_code,
            'selected_city' => $result->city,
        ]) 
    !!}
    function validateFileSize(inputFile) {
        if (inputFile.files && inputFile.files[0]) {
            var fileSize = inputFile.files[0].size;
            if (fileSize > 1033414) { // 5MB in bytes
                flashMessage("File upload Size is greater than 1mb, try uploading a smaller image", 'danger');
                inputFile.value = ''; // clear the selected file
            }
        }
    }
    // AJAX script to remove photo
    $(document).ready(function () {
        $('#remove-photo-btn').click(function() {
            var url = "{{ route('host.delete_profile_image') }}";
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